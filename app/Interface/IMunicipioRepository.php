<?php

namespace App\Interface;

interface IMunicipioRepository
{
    public function GetByLongLat($joins = [], $columns = ['*'], $longitude, $latitude);
}