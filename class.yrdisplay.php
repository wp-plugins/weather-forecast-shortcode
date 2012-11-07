<?php
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden', true, 403 );
	exit( '<!DOCTYPE html><html><body><h1>No direct access allowed</h1></body></html>' );
}

// Av Lennart André Rolland <lennart.andre.rolland@nrk.no>
// Klasse for å vise data fra yr. Kompatibel med YRComms sin datastruktur
class YRDisplay{

	protected $lang;
	// Akkumulator variabl for å holde på generert HTML
	var $ht='';
	// Yr Url
	var $yr_url='';
	// Yr stedsnavn
	var $yr_name='';
	// Yr data
	var $yr_data=Array();

	//Filename for cached HTML. MD5 hash will be prepended to allow caching of several pages
	var $datafile='yr.html';
	//The complete path to the cache file
	var $datapath='';

	// Norsk grovinndeling av de 360 grader vindretning
	var $yr_vindrettninger=array(
    'nord','nord-nord&oslash;st','nord&oslash;st','&oslash;st-nord&oslash;st',
    '&oslash;st','&oslash;st-s&oslash;r&oslash;st','s&oslash;r&oslash;st','s&oslash;r-s&oslash;r&oslash;st',
    's&oslash;r','s&oslash;r-s&oslash;rvest', 's&oslash;rvest','vest-s&oslash;rvest',
    'vest', 'vest-nordvest','nordvest', 'nord-nordvest', 'nord');

	// Hvor hentes bilder til symboler fra?
	var $yr_imgpath='http://fil.nrk.no/yr/grafikk/sym/b38';


	//Generer header for varselet
	public function getHeader($use_full_html){
		// Her kan du endre header til hva du vil. NB! Husk å skru det på, ved å endre instillingene i toppen av dokumentet
		if($use_full_html){
			$this->ht.=<<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>V&aelig;rvarsel fra yr.no</title>
    <link href="http://www12.nrk.no/yr.no/yr-php.css" rel="stylesheet" type="text/css" />
  </head>
  <body>

EOT
			;
		}
		$this->ht.=<<<EOT
    <div id="yr-varsel">

EOT
		;
	}

	//Generer footer for varselet
	public function getFooter($use_full_html){
		$this->ht.=<<<EOT
    </div>

EOT
		;
		// Her kan du endre footer til hva du vil. NB! Husk å skru det på, ved å endre instillingene i toppen av dokumentet
		if($use_full_html){
			$this->ht.=<<<EOT
  </body>
</html>

EOT
			;
		}
	}


   //Generer Copyright for data fra yr.no
   public function getBanner($target='_top'){
      $url=YRComms::convertEncodingEntities($this->yr_url);
      $this->ht.=<<<EOT
      <h1><a href="http://www.yr.no/" target="$target"><img src="http://fil.nrk.no/yr/grafikk/php-varsel/topp.png" alt="yr.no" title="yr.no er en tjeneste fra Meteorologisk institutt og NRK" /></a></h1>

EOT
      ;
   }


   //Generer Copyright for data fra yr.no
   public function getCopyright($target='_top'){
      $url=YRComms::convertEncodingEntities($this->yr_url);
      /*
       Du må ta med teksten nedenfor og ha med lenke til yr.no.
       Om du fjerner denne teksten og lenkene, bryter du vilkårene for bruk av data fra yr.no.
       Det er straffbart å bruke data fra yr.no i strid med vilkårene.
       Du finner vilkårene på http://www.yr.no/verdata/1.3316805
       */
      if ( $this->lang=='nb' ) {
	  $this->ht.=<<<EOT
      <h2><a href="$url" target="$target">V&aelig;rvarsel for $this->yr_name</a></h2>
      <p><a href="http://www.yr.no/" target="$target" title="Til yr.no for full værmelding"><strong>Værvarsel fra yr.no, levert av Meteorologisk institutt og NRK.</strong></a></p>

EOT
      ;
	  } else {
	  $this->ht.=<<<EOT
      <h2><a href="$url" target="$target">Forecast for $this->yr_name</a></h2>
      <p><a href="http://www.yr.no/" target="$target" title="To yr.no for complete forecast"><strong>Weather forecast from yr.no, delivered by the Norwegian Meteorological Institute and the NRK.</strong></a></p>

EOT
      ;
	  }
   }


   //Generer tekst for været
	public function getWeatherText(){
		if((isset($this->yr_data['TEXT'])) && (isset($this->yr_data['TEXT'][0]['LOCATION']))&& (isset($this->yr_data['TEXT'][0]['LOCATION'][0]['ATTRIBUTES'])) ){
			$yr_place=utf8_encode($this->yr_data['TEXT'][0]['LOCATION'][0]['ATTRIBUTES']['NAME']);	// Fixed by knutsp
			if(!isset($this->yr_data['TEXT'][0]['LOCATION'][0]['TIME']))return;
			foreach($this->yr_data['TEXT'][0]['LOCATION'][0]['TIME'] as $yr_var2){
				// Små bokstaver
				$l=(YRComms::convertEncodingUTF($yr_var2['TITLE'][0]['VALUE']));
				// Rettet encoding
				$e=YRComms::reviveSafeTags(YRComms::convertEncodingUTF($yr_var2['BODY'][0]['VALUE']));
				// Spytt ut!
				$this->ht.=<<<EOT
      <p><strong>$yr_place $l</strong>: $e</p>

EOT
				;
			}
		}
	}

	//Generer lenker til andre varsel
	public function getLinks($target='_top'){
		// Rens url
		$url=YRComms::convertEncodingEntities($this->yr_url);
		// Spytt ut
      if ( $this->lang=='nb' ) {
		$this->ht.=<<<EOT
      <p class="yr-lenker">Mer på yr.no:
        <a href="$url/time_for_time.html" target="$target">Time for time</a> |
        <a href="$url/langtidsvarsel.html" target="$target">Langtidsvarsel</a> |
        <a href="$url/radar.html" target="$target">Radar</a> |
        <a href="$url/avansert_kart.html" target="$target">Avansert kart</a> |
        <a href="$url/statistikk.html" target="$target">Været som var</a>
      </p>

EOT
		;
	  } else {
		$this->ht.=<<<EOT
      <p class="yr-lenker">More on yr.no:
        <a href="$url/" target="$target">Overview</a> |
        <a href="$url/hour_by_hour.html" target="$target">Hour by hour</a> |
        <a href="$url/long.html" target="$target">Long term</a> |
        <a href="$url/radar.html" target="$target">Radar</a> |
        <a href="$url/advanced_map.html" target="$target">Advanced map</a> |
        <a href="$url/statistics.html" target="$target">Statistics</a>
      </p>

EOT
		;
	  }
	}

	//Generer header for værdatatabellen
	public function getWeatherTableHeader(){
		$name=$this->yr_name;
		if ( $this->lang=='nb' ) {
		$this->ht.=<<<EOT
      <table summary="V&aelig;rvarsel for $name fra yr.no">
        <thead>
          <tr>
            <th scope="col" class="v" colspan="3"><strong>Varsel for $name</strong>&nbsp;</th>
            <th scope="col">Nedbør&nbsp;</th>
            <th scope="col">Temp.</th>
            <th scope="col" class="v">Vind</th>
            <th scope="col">Vindstyrke</th>
          </tr>
        </thead>
        <tbody>

EOT
		;
		} else {
		$this->ht.=<<<EOT
      <table summary="Forecast for $name from yr.no">
        <thead>
          <tr>
            <th scope="col" class="v" colspan="3"><strong>Forecast for $name</strong>&nbsp;</th>
            <th scope="col">Precip.</th>
            <th scope="col">Temp.</th>
            <th scope="col" class="v">Wind</th>
            <th scope="col">speed</th>
          </tr>
        </thead>
        <tbody>

EOT
		;
		}
	}


	//Generer innholdet i værdatatabellen
	public function getWeatherTableContent(){
		$thisdate='';
		$dayctr=0;
		if(!isset($this->yr_data['TABULAR'][0]['TIME']))return;
		$a=$this->yr_data['TABULAR'][0]['TIME'];

		foreach($a as $yr_var3){
			list($fromdate, $fromtime)=explode('T', $yr_var3['ATTRIBUTES']['FROM']);
			list($todate, $totime)=explode('T', $yr_var3['ATTRIBUTES']['TO']);
			$fromtime=YRComms::parseTime($fromtime);
			$totime=YRComms::parseTime($totime, 1);
			if($fromdate!=$thisdate){
				$divider=<<<EOT
          <tr>
            <td colspan="7" class="skilje"></td>
          </tr>

EOT
				;
				list($thisyear, $thismonth, $thisdate)=explode('-', $fromdate);
				$displaydate=$thisdate.".".$thismonth.".".$thisyear;
				$firstcellcont=$displaydate;
				$thisdate=$fromdate;
				++$dayctr;
			}else $divider=$firstcellcont='';

			// Vis ny dato
			if($dayctr<7){
				$this->ht.=$divider;
				// Behandle symbol
				$imgno=$yr_var3['SYMBOL'][0]['ATTRIBUTES']['NUMBER'];
				$imgvar=$yr_var3['SYMBOL'][0]['ATTRIBUTES']['VAR'];
				if($imgno<10)$imgno='0'.$imgno;
				switch($imgno){
					case '01': case '02': case '03': case '05': case '06': case '07': case '08':
						$imgno.="d"; $do_daynight=1; break;
					default: $do_daynight=0;
				}
				// Behandle regn
				$rain=$yr_var3['PRECIPITATION'][0]['ATTRIBUTES']['VALUE'];
				if($rain==0.0)$rain="0";
				else{
					$rain=intval($rain);
					if($rain<1)$rain='&lt;1';
					else $rain=round($rain);
				}
				$rain.="&nbsp;mm";
				// Behandle vind
				$winddir=round($yr_var3['WINDDIRECTION'][0]['ATTRIBUTES']['DEG']/22.5);
//				$winddirtext=$this->yr_vindrettninger[$winddir];
//				$winddirtext = strtolower(utf8_encode($yr_var3['WINDDIRECTION'][0]['ATTRIBUTES']['NAME']));	// knutsp
				
				$winddirtext = $yr_var3['WINDDIRECTION'][0]['ATTRIBUTES']['NAME'];	// knutsp
				$winddirtext = mb_convert_encoding( $winddirtext, 'UTF-8', 'UTF-8, ISO-8859-1' );	// knutsp
				if ( $this->lang=='nb' )
					$winddirtext = mb_convert_case( $winddirtext, MB_CASE_LOWER );	// knutsp
				// Behandle temperatur
				$temper=round($yr_var3['TEMPERATURE'][0]['ATTRIBUTES']['VALUE']);
				if($temper>=0)$tempclass='pluss';
				else $tempclass='minus';

				// Rund av vindhastighet
				$r=round($yr_var3['WINDSPEED'][0]['ATTRIBUTES']['MPS']);
				// Så legger vi ut hele den ferdige linjen
				$s=$yr_var3['SYMBOL'][0]['ATTRIBUTES']['NAME'];
				$w=$yr_var3['WINDSPEED'][0]['ATTRIBUTES']['NAME'];
				if ( $this->lang=='en' )
					$from = 'from';
				else
					$from = 'fra';

				$this->ht.=<<<EOT
          <tr>
            <th>$firstcellcont</th>
            <th>$fromtime&#8211;$totime</th>
            <td><img src="$this->yr_imgpath/$imgvar.png" width="38" height="38" alt="$s" /></td>
            <td>$rain</td>
            <td class="$tempclass">$temper&nbsp;&deg;C</td>
            <td class="v">$w $from $winddirtext</td>
            <td>$r&nbsp;m/s</td>
          </tr>

EOT
				;
			}
		}
	}

	//Generer footer for værdatatabellen
	public function xgetWeatherTableFooter($target='_top'){
		$this->ht.=<<<EOT
          <tr>
            <td colspan="7" class="skilje"></td>
          </tr>
        </tbody>
      </table>
      <p>V&aelig;rsymbolet og nedb&oslash;rsvarselet gjelder for hele perioden, temperatur- og vindvarselet er for det f&oslash;rste tidspunktet. &lt;1 mm betyr at det vil komme mellom 0,1 og 0,9 mm nedb&oslash;r.<br />
      <a href="http://www.yr.no/1.3362862" target="$target">Slik forst&aring;r du varslene fra yr.no</a>.</p>
      <p>Vil du ogs&aring; ha <a href="http://www.yr.no/verdata/" target="$target">v&aelig;rvarsel fra yr.no p&aring; dine nettsider</a>?</p>
EOT
		;
	}

	public function getWeatherTableFooter($target='_top'){
		$this->ht.=<<<EOT
          <tr>
            <td colspan="7" class="skilje"></td>
          </tr>
        </tbody>
      </table>
EOT
		;
	}


	// Handle cache directory (re)creation and cachefile name selection
	private function handleDataDir($clean_datadir=false,$summary=''){
		global $yr_datadir;
		// The md5 sum is to avoid caching to the same file on parameter changes
		$this->datapath=$yr_datadir .'/'. ($summary!='' ? (md5($summary).'['.$summary.']_') : '').$this->datafile;
		// Delete cache dir
		if ($clean_datadir) {
			unlink($this->datapath);
			rmdir($yr_datadir);
		}
		// Create new cache folder with correct permissions
		if(!is_dir($yr_datadir))mkdir($yr_datadir,0764);
	}


	//Main with caching
	public function generateHTMLCached($url,$name,$xml, $url, $try_curl, $useHtmlHeader=true, $useHtmlFooter=true, $useBanner=true, $useText=true, $useLinks=true, $useTable=true, $maxage=0, $timeout=10, $urlTarget='_top'){
		if ( strpos($url,'/place/') )
			$this->lang = 'en';
		else
			$this->lang = 'nb';
		//Default to the name in the url
		if(null==$name||''==trim($name))$name=array_pop(explode('/',$url));
		$this->handleDataDir(false,htmlentities("$name.$useHtmlHeader.$useHtmlFooter.$useBanner.$useText.$useLinks.$useTable.$maxage.$timeout.$urlTarget"));
		$yr_cached = $this->datapath;
		// Clean name
		$name=YRComms::convertEncodingUTF($name);
		$name=YRComms::convertEncodingEntities($name);
		// Clean URL
		$url=YRComms::convertEncodingUTF($url);
		// Er mellomlagring enablet, og trenger vi egentlig laste ny data, eller holder mellomlagret data?
		if(($maxage>0)&&((file_exists($yr_cached))&&((time()-filemtime($yr_cached))<$maxage))){
			$data['value']=file_get_contents($yr_cached);
			// Sjekk for feil
			if(false==$data['value']){
	  	$data['value']='<p>Det oppstod en feil mens værdata ble lest fra lokalt mellomlager. Vennligst gjør administrator oppmerksom på dette! Teknisk: Sjekk at rettighetene er i orden som beskrevet i bruksanvisningen for dette scriptet</p>';
	  	$data['error'] = true;
	  }
		}
		// Vi kjører live, og saver samtidig en versjon til mellomlager
		else{
			$data=$this->generateHTML($url,$name,$xml->getXMLTree($url, $try_curl, $timeout),$useHtmlHeader,$useHtmlFooter,$useBanner,$useText,$useLinks,$useTable,$urlTarget);
			// Lagre til mellomlager
			if($maxage>0 && !$data['error'] ){
				$f=fopen($yr_cached,"w");
				if(null!=$f){
					fwrite($f,$data['value']);
					fclose($f);
				}
			}
		}
		// Returner resultat
		return $data['value'];
	}

	private function getErrorMessage(){
		if(isset($this->yr_data['ERROR'])){
			$error=$this->yr_data['ERROR'][0]['VALUE'];
			//die(retar($error));
			$this->ht.='<p style="color:red; background:black; font-weight:900px">' .$error.'</p>';
	  return true;
		}
		return false;
	}

	//Main
	public function generateHTML($url,$name,$data,$useHtmlHeader=true,$useHtmlFooter=true,$useBanner=true,$useText=true,$useLinks=true,$useTable=true,$urlTarget='_top'){
		// Fyll inn data fra parametrene
		$this->ht='';
		$this->yr_url=$url;
		$this->yr_name=$name;
		$this->yr_data=$data;

		// Generer HTML i $ht
		$this->getHeader($useHtmlHeader);
		$data['error'] = $this->getErrorMessage();
		if($useBanner)$this->getBanner($urlTarget);
		$this->getCopyright($urlTarget);
		if($useText)$this->getWeatherText();
		if($useLinks)$this->getLinks($urlTarget);
		if($useTable){
			$this->getWeatherTableHeader();
			$this->getWeatherTableContent();
			$this->getWeatherTableFooter($urlTarget);
		}
		$this->getFooter($useHtmlFooter);

		// Returner resultat
		//return YRComms::convertEncodingEntities($this->ht);
		$data['value'] = $this->ht;
		return $data;
	}
}