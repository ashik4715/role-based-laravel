<?php

namespace App\Services\Location;

interface Client
{
    public function getAddressFromGeo(Geo $geo): Address;
}
