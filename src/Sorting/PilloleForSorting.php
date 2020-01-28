<?php

declare(strict_types = 1);

class PilloleForSorting{
    
    public $id;
    public $lat;
    public $lng;
    public $date;

    function __construct(int $id, float $lat, float $lng, string $date)
    {
        $this -> id = $id;
        $this -> lat = $lat;
        $this -> lng = $lng;
        $this -> date = $date;
    }
}