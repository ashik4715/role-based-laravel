<?php

namespace App\Services\Location;

class BarikoiAddress extends Address
{
    private $houseNo;
    private $roadNo;
    private $area = '';

    public function handleFullAddress($place): BarikoiAddress
    {
        $this->setAddress($place->address . ', ' . $place->area . ', ' . $place->city);
        $this->setHouseNo($place->address_components->house);
        $this->setRoadNo($place->address_components->road);
        $this->setArea($place->area_components);
        return $this;
    }

    public function setHouseNo($houseNo)
    {
        $houseNo = trim($houseNo);
        if (substr($houseNo, 0, 5) === 'House') $this->houseNo = trim(substr($houseNo, 5));
        else $this->houseNo = $houseNo;
    }

    public function setRoadNo($roadNo)
    {
        $roadNo = trim($roadNo);
        if (substr($roadNo, 0, 4) === 'Road') $this->roadNo = trim(substr($roadNo, 4));
        else $this->roadNo = $roadNo;
    }

    public function setArea($areaComponents)
    {
        if (!is_null($areaComponents->sub_area)) $this->area = $areaComponents->sub_area . ', ';

        $this->area .= trim($areaComponents->area);
    }

    public function getRoadNo()
    {
        return $this->roadNo;
    }

    public function getHouseNo()
    {
        return $this->houseNo;
    }

    public function getArea()
    {
        return $this->area;
    }
}
