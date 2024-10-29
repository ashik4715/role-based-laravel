<?php

namespace App\Services\Location;

class Geo
{
    private $lat;
    private $lng;

    public function __construct($lat = null, $lng = null)
    {
        $this->setLat($lat);
        $this->setLng($lng);
    }

    public function setLat($lat)
    {
        $this->lat = $lat;
        return $this;
    }

    public function setLng($lng)
    {
        $this->lng = $lng;
        return $this;
    }

    public function getLat()
    {
        return $this->lat ? (double)$this->lat : null;
    }

    public function getLng()
    {
        return $this->lng ? (double)$this->lng : null;
    }

    public function isZero()
    {
        return $this->getLat() == 0 || $this->getLng() == 0;
    }

    public function isNull()
    {
        return is_null($this->getLat()) || is_null($this->getLng());
    }

    public function isEmpty()
    {
        return $this->isNull() || $this->isZero();
    }

    public function isNotNull()
    {
        return !$this->isNull();
    }

    /**
     * @return float[]|null[]
     */
    public function toArray()
    {
        return [
            'lat' => $this->getLat(),
            'lng' => $this->getLng()
        ];
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return \stdClass
     */
    public function toStdObject()
    {
        return json_decode($this->toJson());
    }

    public function isSameTo(Geo $geo)
    {
        return $this->getLat() === $geo->getLat() && $this->getLng() === $geo->getLng();
    }

    public function isDifferentWith(Geo $geo)
    {
        return !$this->isSameTo($geo);
    }
}
