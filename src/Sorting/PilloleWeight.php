<?php   

declare(strict_types = 1);

class PillolaWeight {

    public $id;
    public $weight;

    function __construct(int $id, float $w)
    {
        $this -> id = $id;
        $this -> weight = $w;
    }
    
}