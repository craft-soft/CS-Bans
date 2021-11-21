<?php

require_once("geoip2.phar");
use GeoIp2\Database\Reader;

require_once("Result.php");

/**
 * Class GeoIP2
 */
class GeoIP2 extends CApplicationComponent {
    /**
     * @var string
     */
    public $mmdbCity = ROOTPATH . '/include/GeoLite2-City.mmdb';

    /**
     * @var string
     */
    public $mmdbCountry = ROOTPATH . '/include/GeoLite2-Country.mmdb';

    /**
     * @var string
     */
    public $lng = 'ru';

    /**
     * @var bool
     */
    private $useCityDB;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var array
     */
    private $availableLng = ['de', 'en', 'es', 'ja', 'ru', 'zh-CN'];

    /**
     * @inheritDoc
     */
    public function init() {
        if(file_exists($this->mmdbCity)) {
            $mmdb = $this->mmdbCity;
            $this->useCityDB = true;
        } else {
            $mmdb = $this->mmdbCountry;
        }

        $this->reader = new Reader($mmdb);
        if(!in_array($this->lng, $this->availableLng)) throw new Exception("Unknown language");

        parent::init();
    }

    /**
     * // @param string|null $ip
     * // @return Result
     */
    public function getInfoByIP($ip = null) {
        if ($ip === null) {
            $ip = Yii::$app->request->getUserIP();
        }

        if($this->useCityDB) {
            $result = $this->reader->city($ip);
            return new Result($result, $this->lng);

        } else {
            $context = stream_context_create(['http' => ['timeout' => 3]]);
            $get = @file_get_contents("http://ip-api.com/json/$ip?lang=$this->lng&fields=status,message,country,regionName,city,district,lat,lon,query", false, $context);
            if($get) {
                $json = json_decode($get, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    // woking as wrapper to provide data same way as GeoLite2 without translations (in 'names' arrays)
                    $result = [
                        'city' => ['name' => $json['city']],
                        'subdivisions' => [['name' => $json['regionName']]],
                        'country' => ['name' => $json['country']],
                        'location' => [
                            'latitude' => $json['lat'],
                            'longitude' => $json['lon']
                        ]
                    ];
                    return new Result(json_decode(json_encode($result)), $this->lng);
                }
            }
        }

    }

    /**
     * @param string|null $ip
     * @return string
     */
    public function getCountryByIP($ip = null) {
        if ($ip === null) {
            $ip = Yii::$app->request->getUserIP();
        }

        if($this->useCityDB)
            $result = $this->reader->city($ip);
        else
            $result = $this->reader->country($ip);

        return $result->country->isoCode;
    }
}

