<?php

class Result {
    /**
     * @var array
     */
    private $data;
    /**
     * @var string
     */
    private $lng;

    public function __construct($data, $lng) {
        $this->data = $data;
        $this->lng = $lng;
    }

    public function __get($name) {
        $getter = 'get' . ucfirst($name);

        if (method_exists($this, $getter)) {
            return $this->$getter($this->data);
        }

        throw new Exception("Unknown property");
    }

    protected function getCity() {
        $value = null;

        if (isset($this->data->city->names[$this->lng])) {
            $value = $this->data->city->names[$this->lng];
        } else if (isset($this->data->city->name)) {
            $value = $this->data->city->name;
        }
        return $value;
    }

    protected function getCountry() {
        $value = null;

        if (isset($this->data->country->names[$this->lng])) {
            $value = $this->data->country->names[$this->lng];
        } else if (isset($this->data->country->name)) {
            $value = $this->data->country->name;
        }

        return $value;
    }

    protected function getContinent() {
        $value = null;

        if (isset($this->data->continent->names[$this->lng])) {
            $value = $this->data->continent->names[$this->lng];
        }

        return $value;
    }

    protected function getLocation() {
        $value = new Location();

        if (isset($this->data->location)) {
            $latitude = $this->data->location->latitude;
            $longitude = $this->data->location->longitude;
            $value = new Location($latitude, $longitude);
        }

        return $value;
    }

    protected function getTimeZone() {
        $value = new Location();

        if (isset($this->data->location)) {
            $value = $this->data->location->time_zone;
        }

        return $value;
    }

    protected function getSubdivisions() {
        $value = null;

        if(isset($this->data->subdivisions) && is_array($this->data->subdivisions)) {
            $arr = [];
            foreach($this->data->subdivisions as $subdivision){
                if (isset($subdivision->names[$this->lng])) {
                    $arr[] = $subdivision->names[$this->lng];
                } else if (isset($subdivision->name)) {
                    $arr[] = $subdivision->name;
                }
            }
            $value = implode(', ', $arr);
        }

        return $value;
    }

    protected function getIsoCode() {
        $value = null;
        if (isset($this->data->country->iso_code)) {
            $value = $this->data->country->iso_code;
        }
        return $value;
    }

}

class Location {

    /**
     * @var float|null
     */
    public $latitude;

    /**
     * @var float|null
     */
    public $longitude;

    public function __construct($latitude = null, $longitude = null) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }
}
