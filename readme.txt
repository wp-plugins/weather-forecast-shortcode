=== Weather Forecast Shortcode ===
Contributors: knutsp, Egoist8
Donate link: http://om.yr.no/verdata/free-weather-data/
Tags: weather, shortcode, yr.no
Requires at least: 3.0.1
Tested up to: 3.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Registers a shortcode for fetching weather forecast from yr.no 

== Description ==

Weather forecasts from `yr.no`, delivered by the Norwegian Meteorological Institute (MI) and the Norwegian national public broadcasting company (NRK).
Registers a shortcode `[yr]` to display a weather forecast for a place (or city) as a table on any page or post, based on url found manually on <a href="http://om.yr.no/">yr.no</a>.
It covers more than <strong>9 million</strong> places all over the world, including about 900 000 places in Norway alone. Specific resorts and hotels included.
This plugin is enturely based on free and public weather data, but follows the terms and conditions required by <a href="http://om.yr.no/verdata/free-weather-data/">`yr.no`</a>.

It uses an updated edition of the original script <a href="http://www.yr.no/contentfile/file/1.5542944!php-varsel_versjon_2-5.zip">`yr.php`</a> v 2.6
by Lennart André Rolland at NRK in 2008.

For upgrade problems, see 'Installation' tab.

== Installation ==

= Manual =

1. Extract the contents of the zip file to a local folder
2. Upload `weather-forecast-shortcode` folder to the `/wp-content/plugins/` folder
3. Activate the plugin through the 'Plugins' menu in WordPress

= Upgrade =

<strong>IMPORTANT:</strong> If upgrade to new version from 1.2 or earlier fails, rename the folder `wp-content/plugins/weather-forecast-shortcode` to something else and reinstall the plugin.
This problem should never happen again. Then try setting permission for the old `cache` subfolder to `755` and delete it through FTP. Otherwise try shell access or support may delete it.

== Frequently Asked Questions ==

= Usage examples =

Visit <a href="http://www.yr.no/">`yr.no`</a>, select your language and <em>search for the desired place</em>. Copy the url. In your post/page type
`[yr url="paste the url here" name="Optional display name of the place"]`. Or like this:

* `[yr url="http://www.yr.no/place/Norway/Oslo/Oslo/Oslo"]`
* `[yr url="http://www.yr.no/place/United_States/New_York/Times_Square~5141023" name="Times Square"]`
* `[yr name="Ao Nang" url="http://www.yr.no/sted/Thailand/Krabi/Ao_Nang"]`

= Other parameters to include in [yr] when needed: =

* `banner` (0/1) (show logo/banner from yr.no)
* `text` (0/1) (only for Norway and in Norwegian language)
* `links` (0/1) (show links to other weather data for this place)
* `table` (1/0) (show the forecast table, default on)
* `maxage` (seconds, defaults to 20 minutes, for efficient caching)
* `timeout` (seconds, maximum time to wait for response from `yr.no`, defaults to 10)

Example: `[yr url="http://www.yr.no/place/Norway/Akershus/B%C3%A6rum/Sandvika" links="1" table="0" maxage="2" banner="1"]`

The only necessary parameter is `url` (but defaults to Oslo). If `name` is omitted it defaults to the last part of the url, like Sandvika in the above example.

= Languages fully supported =

* English [`en_US`]
* Norwegian (bokmål) [`nb_NO`]

Other languages available on `yr.no` will work, somewhat mixed with English text.

= Caching =

This plugin will create a subdirectory in the plugin folder named `cache` to store files between retrieves from yr.no. 

== Screenshots ==

1. English text (shown when the url is retrieved from the English edition at `yr.no`)
2. Norsk tekst (vises nå url er funnet på den norske utgaven av `yr.no`)

== Changelog ==

= 1.4 =

* Consolidating folder permissions and a few dosumentation typo fixes
* Apology for the upgrade problems due to failure to delete the plugin folder with the sticky `cache` subfolder

= 1.3 =

* Permissions on `cache` folder now set to `0764` to avoid upgrade problems

= 1.2 =

* Improved `links` separation for accessibility
* Links supports English fully
* Some `title`  attribute and spacing improvements

= 1.1 =

* Improved character set handling and conversion for UTF-8 versus ISO-8859-1.
* Added a non-breaking space between:
	* rain value and `mm` unit
	* wind value and `m/s` unit
	* temeprature value and `˚C` unit sign

= 1.0 =
* Initial release.

= 0.1 =
* Experimental.
