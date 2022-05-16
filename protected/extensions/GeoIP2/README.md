This is modified GeoIP2 extension for yii 2.
Source can be found here: https://github.com/overals/yii2-geoIP2.


GeoIP2.phar is taken from https://github.com/maxmind/GeoIP2-php releases page.
Used version is 2.10.0 because it is a latest version which supports PHP 5.6 (which is used in some installations).


If no GeoLite2-City database is found, web request to GeoIP service is used.
To get latest GeoLite2-City database use MaxMind site. Steps to gather latest binary:

1. Register at https://www.maxmind.com/en/geolite2/signup if you haven't account yet. It's free
2. Auth to you accout: https://www.maxmind.com/en/account
3. Go to downloads page and download GeoLite2-City database in *binary format*
4. Extract database GetLite2-city.mmdb to /include directory

*OR*

1. Go to https://github.com/P3TERX/GeoLite.mmdb
2. Download GeoLite2-City.mmdb from any of two links
3. Upload file to /include directory
