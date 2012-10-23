=== Weather forecast shortcode ===
Contributors: knutsp, Egoist8
Donate link: http://om.yr.no/verdata/free-weather-data/
Tags: weather, shortcode
Requires at least: 3.0.1
Tested up to: 3.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Registers a shortcode for fetching weather forecast from yr.no 

== Description ==

Weather forecasts from yr.no, delivered by the Norwegian Meteorological Institute (MI) and the Norwegian national public broadcasting company (NRK).
Registers a shortcode [yr] to display a weather forecast for a place (or city) as a table on any page or post, based on url found manually on <a href="http://om.yr.no/">yr.no</a>.
It covers more than <strong>9 million</strong> places all over the world, including about 900 000 places in Norway alone. Specific resorts and hotels included.
This plugin is enturely based on free and public weather data, but follows the terms and conditions required by <a href="http://om.yr.no/verdata/free-weather-data/">yr.no</a>.

It uses an updated edition of the original script <a href="http://www.yr.no/contentfile/file/1.5542944!php-varsel_versjon_2-5.zip">`yr.php`</a> v 2.6
by <a href="mailto:lennart.andre.rolland@nrk.no">Lennart André Rolland</a> at NRK.

== Installation ==

= Manual =

1. Extract the contents of the zip file to a local folder
2. Upload `weather-forecast-shortcode` folder to the `/wp-content/plugins/` folder
3. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Usage examples =

Visit <a href="http://www.yr.no/">yr.no</a>, select your language and <em>search for the desired place</em>. Copy the url. In your post/page type
[yr url="paste the url here" name="Optional display name of the place"]. Like this:

* [yr url="http://www.yr.no/place/Norway/Oslo/Oslo/Oslo"]
* [yr url="http://www.yr.no/place/United_States/New_York/Times_Square~5141023" name="Times Square"]
* [yr name="Ao Nang" url="http://www.yr.no/sted/Thailand/Krabi/Ao_Nang"]

= Other parameters to include in [yr] when needed =

* banner (0/1) (show logo/banner from yr.no)
* text (0/1) (only for Norway and in Norwegian language)
* links (0/1) (show links to other weather data for this place)
* table (1/0) (show the forecast table, default on)
* maxage (seconds, defaults to 20 minutes, for efficient caching)
* timeout (max seconds to wait for yr.no, defaults to 10)

= Caching =

This plugin will create a subdirectory in the plugin folder named `cache` to store files between retrieves from yr.no

== Screenshots ==

1. English text (shown when the url is retrieved from the English edition at yr.no)
2. Norsk tekst (vises nå url er funnet på den norske utgaven av yr.no)

== Changelog ==

= 1.0 =
* Initial release.

= 0.1 =
* Experimental.
