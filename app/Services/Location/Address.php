<?php

namespace App\Services\Location;

class Address
{
    private $address;

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     * @return Address
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    public function hasAddress()
    {
        return !empty($this->address);
    }
}
