<?php
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden', true, 403 );
	exit( '<!DOCTYPE html><html><body><h1>No direct access allowed</h1></body></html>' );
}
// Av Lennart André Rolland <lennart.andre.rolland@nrk.no>
// Klasse for lesing og tilrettelegging av YR data
class YRComms{

	//Generer gyldig yr.no array med værdata byttet ut med en enkel feilmelding
	private function getYrDataErrorMessage($msg="Feil"){
		return Array(
      '0'=> Array('tag'=> 'WEATHERDATA','type'=> 'open','level'=> '1'),
      '1'=> Array('tag'=> 'LOCATION','type'=> 'open','level'=> '2'),
      '2'=> Array('tag'=> 'NAME','type'=> 'complete','level'=> '3','value'=> $msg),
      '3'=> Array('tag'=> 'LOCATION','type'=> 'complete','level'=> '3'),
      '4'=> Array( 'tag'=> 'LOCATION', 'type'=> 'close', 'level'=> '2'),
      '5'=> Array( 'tag'=> 'FORECAST', 'type'=> 'open', 'level'=> '2'),
      '6'=> Array( 'tag'=> 'ERROR', 'type'=> 'complete', 'level'=> '3', 'value'=> $msg),
      '7'=> Array( 'tag'=> 'FORECAST', 'type'=> 'close', 'level'=> '2'),
      '8'=> Array( 'tag'=> 'WEATHERDATA', 'type'=> 'close', 'level'=> '1')
		);
	}

	//Generer gyldig yr.no XML med værdata byttet ut med en enkel feilmelding
	private function getYrXMLErrorMessage($msg="Feil"){
		$msg=$this->getXMLEntities($msg);
		//die('errmsg:'.$msg);
		$data=<<<EOT
<weatherdata>
  <location />
  <forecast>
  <error>$msg</error>
    <text>
      <location />
    </text>
  </forecast>
</weatherdata>

EOT
		;
		//die($data);
		return $data;
	}

	// Sørger for å laste ned XML fra yr.no og leverer data tilbake i en streng
	private function loadXMLData($xml_url,$try_curl=true,$timeout=10){
		global $yr_datadir;
		$xml_url.='/varsel.xml';
		// Lag en timeout på contexten
		$ctx = stream_context_create(array( 'http' => array('timeout' => $timeout)));

		// Prøv å åpne direkte først
		//NOTE: This will spew ugly errors even when they are handled later. There is no way to avoid this but prefixing with @ (slow) or turning off error reporting
  		$data=file_get_contents($xml_url,0,$ctx);

		if(false!=$data){
			//Jippi vi klarte det med vanlig fopen url wrappers!
		}
		// Vanlig fopen_wrapper feilet, men vi har cURL tilgjengelig
		else if($try_curl && function_exists('curl_init')){
			$lokal_xml_url = $yr_datadir .'/curl.temp.xml';
			$data='';
			$ch = curl_init($xml_url);
			// Åpne den lokale temp filen for skrive tilgang (med cURL hooks enablet)
			$fp = fopen($lokal_xml_url, "w");
			// Last fra yr.no til lokal kopi med curl
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POSTFIELDS, '');
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_exec($ch);
			curl_close($ch);
			// Lukk lokal kopi
			fclose($fp);
			// Åpne lokal kopi igjen og les in alt innholdet
			$data=file_get_contents($lokal_xml_url,0,$ctx);
			//Slett temp data
			unlink($lokal_xml_url);
			// Sjekk for feil
			if(false==$data)$data=$this->getYrXMLErrorMessage('Det oppstod en feil mens værdata ble lest fra yr.no. Teknisk info: Mest antakelig: kobling feilet. Nest mest antakelig: Det mangler støtte for fopen wrapper, og cURL feilet også. Minst antakelig: cURL har ikke rettigheter til å lagre temp.xml');
		}
		// Vi har verken fopen_wrappers eller cURL
		else{
			$data=$this->getYrXMLErrorMessage('Det oppstod en feil mens værdata ble forsøkt lest fra yr.no. Teknisk info: Denne PHP-installasjon har verken URL enablede fopen_wrappers eller cURL. Dette gjør det umulig å hente ned værdata. Se imiddlertid følgende dokumentasjon: http://no.php.net/manual/en/wrappers.php, http://no.php.net/manual/en/book.curl.php');
			//die('<pre>LO:'.retar($data));
		}
		//die('<pre>XML for:'.$xml_url.' WAS: '.$data);
		// Når vi har kommet hit er det noe som tyder på at vi har lykkes med å laste værdata, ller i det minste lage en teilmelding som beskriver eventuelle problemer
		return $data;
	}

	// Last XML til en array struktur
	private function parseXMLIntoStruct($data){
		global $yr_datadir;
		$parser = xml_parser_create('ISO-8859-1');
		if((0==$parser)||(FALSE==$parser))return $this->getYrDataErrorMessage('Det oppstod en feil mens værdata ble forsøkt hentet fra yr.no. Teknisk info: Kunne ikke lage XML parseren.');
		$vals = array();
		//die('<pre>'.retar($data).'</pre>');
		if(FALSE==xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1))return $this->getYrDataErrorMessage('Det oppstod en feil mens værdata ble forsøkt hentet fra yr.no. Teknisk info: Kunne ikke stille inn XML-parseren.');
		if(0==xml_parse_into_struct($parser, $data, $vals, $index))return $this->getYrDataErrorMessage('Det oppstod en feil mens værdata ble forsøkt hentet fra yr.no. Teknisk info: Parsing av XML feilet.');
		if(FALSE==xml_parser_free($parser))return $this->getYrDataErrorMessage('Det oppstod en feil mens værdata ble forsøkt hentet fra yr.no. Kunne ikke frigjøre XML-parseren.');
		//die('<pre>'.retar($vals).'</pre>');
		return $vals;
	}


	// Rense tekst data (av sikkerhetshensyn)
	private function sanitizeString($in){
		//return $in;
		if(is_array($in))return $in;
		if(null==$in)return null;
		return htmlentities(strip_tags($in));
	}

	// Rense tekst data (av sikkerhetshensyn)
	public function reviveSafeTags($in){
		//$in=$in.'<strong>STRONG</strong> <u>UNDERLINE</u> <b>BOLD</b> <i>ITALICS</i>';
		return str_ireplace(array('&lt;strong&gt;','&lt;/strong&gt;','&lt;u&gt;','&lt;/u&gt;','&lt;b&gt;','&lt;/b&gt;','&lt;i&gt;','&lt;/i&gt;'),array('<strong>','</strong>','<u>','</u>','<b>','</b>','<i>','</i>'),$in);
	}



	private function rearrangeChildren($vals, &$i) {
		$children = array(); // Contains node data
		// Sikkerhet: sørg for at all data som parses strippes for farlige ting
		if (isset($vals[$i]['value']))$children['VALUE'] = $this->sanitizeString($vals[$i]['value']);
		while (++$i < count($vals)){
			// Sikkerhet: sørg for at all data som parses strippes for farlige ting
			if(isset($vals[$i]['value']))$val=$this->sanitizeString($vals[$i]['value']);
			else unset($val);
			if(isset($vals[$i]['type']))$typ=$this->sanitizeString($vals[$i]['type']);
			else unset($typ);
			if(isset($vals[$i]['attributes']))$atr=$this->sanitizeString($vals[$i]['attributes']);
			else unset($atr);
			if(isset($vals[$i]['tag']))$tag=$this->sanitizeString($vals[$i]['tag']);
			else unset($tag);
			// Fyll inn strukturen vær slik vi vil ha den
			switch ($vals[$i]['type']){
				case 'cdata': $children['VALUE']=(isset($children['VALUE']))?$val:$children['VALUE'].$val; break;
				case 'complete':
					if (isset($atr)) {
						$children[$tag][]['ATTRIBUTES'] = $atr;
						$index = count($children[$tag])-1;
						if (isset($val))$children[$tag][$index]['VALUE'] = $val;
						else $children[$tag][$index]['VALUE'] = '';
					} else {
						if (isset($val))$children[$tag][]['VALUE'] = $val;
						else $children[$tag][]['VALUE'] = '';
					}
					break;
				case 'open':
					if (isset($atr)) {
						$children[$tag][]['ATTRIBUTES'] = $atr;
						$index = count($children[$tag])-1;
						$children[$tag][$index] = array_merge($children[$tag][$index],$this->rearrangeChildren($vals, $i));
					} else $children[$tag][] = $this->rearrangeChildren($vals, $i);
					break;
				case 'close': return $children;
			}
		}
	}
	// Ommøbler data til å passe vårt formål, og returner
	private function rearrangeDataStruct($vals){
		//die('<pre>'.$this->retar($vals).'<\pre>');
		$tree = array();
		$i = 0;
		if (isset($vals[$i]['attributes'])) {
			$tree[$vals[$i]['tag']][]['ATTRIBUTES']=$vals[$i]['attributes'];
			$index=count($tree[$vals[$i]['tag']])-1;
			$tree[$vals[$i]['tag']][$index]=array_merge($tree[$vals[$i]['tag']][$index], $this->rearrangeChildren($vals, $i));
		} else $tree[$vals[$i]['tag']][] = $this->rearrangeChildren($vals, $i);
		//die("<pre>".retar($tree));
		//Hent ut det vi bryr oss om
		if(isset($tree['WEATHERDATA'][0]['FORECAST'][0]))return $tree['WEATHERDATA'][0]['FORECAST'][0];
		else return YrComms::getYrDataErrorMessage('Det oppstod en feil ved behandling av data fra yr.no. Vennligst gjør administrator oppmerksom på dette! Teknisk: data har feil format.');
	}

	// Hovedmetode. Laster XML fra en yr.no URI og parser denne
	public function getXMLTree($xml_url, $try_curl, $timeout){
		// Last inn XML fil og parse til et array hierarcki, ommøbler data til å passe vårt formål, og returner
		return $this->rearrangeDataStruct($this->parseXMLIntoStruct($this->loadXMLData($xml_url,$try_curl,$timeout)));
	}

	// Statisk hjelper for å parse ut tid i yr format
	public function parseTime($yr_time, $do24_00=false){
		$yr_time=str_replace(":00:00", "", $yr_time);
		if($do24_00)$yr_time=str_replace("00", "24", $yr_time);
		return $yr_time;
	}

	// Statisk hjelper for å besørge riktig encoding ved å oversette spesielle ISO-8859-1 karakterer til HTML/XHTML entiteter
	public function convertEncodingEntities($yrraw){
		$conv=str_replace("æ", "&aelig;", $yrraw);
		$conv=str_replace("ø", "&oslash;", $conv);
		$conv=str_replace("å", "&aring;", $conv);
		$conv=str_replace("Æ", "&AElig;", $conv);
		$conv=str_replace("Ø", "&Oslash;", $conv);
		$conv=str_replace("Å", "&Aring;", $conv);
		return $conv;
	}

	// Statisk hjelper for å besørge riktig encoding vedå oversette spesielle UTF karakterer til ISO-8859-1
	public function convertEncodingUTF($yrraw){
		$conv=str_replace("Ã¦", "æ", $yrraw);
		$conv=str_replace("Ã¸", "ø", $conv);
		$conv=str_replace("Ã¥", "å", $conv);
		$conv=str_replace("Ã†", "Æ", $conv);
		$conv=str_replace("Ã˜", "Ø", $conv);
		$conv=str_replace("Ã…", "Å", $conv);
		return $conv;
	}


	public function getXMLEntities($string){
		return preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '$this->_privateXMLEntities("$0")', $string);
	}

	private function _privateXMLEntities($num){
		$chars = array(
		128 => '&#8364;', 130 => '&#8218;',
		131 => '&#402;', 132 => '&#8222;',
		133 => '&#8230;', 134 => '&#8224;',
		135 => '&#8225;',136 => '&#710;',
		137 => '&#8240;',138 => '&#352;',
		139 => '&#8249;',140 => '&#338;',
		142 => '&#381;', 145 => '&#8216;',
		146 => '&#8217;',147 => '&#8220;',
		148 => '&#8221;',149 => '&#8226;',
		150 => '&#8211;',151 => '&#8212;',
		152 => '&#732;',153 => '&#8482;',
		154 => '&#353;',155 => '&#8250;',
		156 => '&#339;',158 => '&#382;',
		159 => '&#376;');
		$num = ord($num);
		return (($num > 127 && $num < 160) ? $chars[$num] : "&#".$num.";" );
	}
}
