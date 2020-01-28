<?php   

declare(strict_types = 1);

class PillolaData {

    public $id;
    public $distance;
    public $timeDistanceInMinutes;

    function __construct(int $id, float $distance, float $timeDistanceInMinutes)
    {
        $this -> id = $id;
        $this -> distance = $distance;
        $this -> timeDistanceInMinutes = $timeDistanceInMinutes;
    }
}