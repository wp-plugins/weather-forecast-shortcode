=== Weather forecast shortcode ===
Contributors: knutsp, ist@kreasjoner.com
Donate link: http://om.yr.no/verdata/free-weather-data/
Tags: weather, shortcode
Requires at least: 3.0.1
Tested up to: 3.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Registers a shortcode for fetching weather forecast from yr.no 

== Description ==

Weather forecast from yr.no, delivered by the Norwegian Meteorological Institute and the NRK.
Creates a shortcode to display a weather forecast for a place (or city) as a table on any page or post.
It covers over <strong>9 million places</strong> all over the world, including 900 000 places in Norway.
Based on the free weather data and follows the terms and conditions stated by yr.no.
More info at <a href="http://om.yr.no/verdata/free-weather-data/">yr.no</a>.

Uses `yr.php` version 2.6 by <a href="mailto:lennart.andre.rolland@nrk.no">Lennart André Rolland</a>.

== Installation ==

= Manual =

1. Extract the contents of the zip file to a local folder
2. Upload `weather-forecast-shortcode` folder to the `/wp-content/plugins/` folder
3. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Usage examples =

Visit <a href="http://www.yr.no/">yr.no</a> and search for the desired place. Copy the url. In your post/page type

* [yr url="http://www.yr.no/place/Norway/Oslo/Oslo/Oslo" name="Oslo"]
* [yr url="http://www.yr.no/place/United_States/New_York/Times_Square~5141023" name="Times Square"]
* [yr name="Ao Nang" url="http://www.yr.no/sted/Thailand/Krabi/Ao_Nang"]

== Screenshots ==

1. English display (shown when the url is retrieved on the English edition at yr.no)
2. Norwegian display (vises nå url er funnet på den norske utgaven av yr.no)

== Changelog ==

= 1.0 =
* Initial release.

= 0.1 =
* Experimental.
