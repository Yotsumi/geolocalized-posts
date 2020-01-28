<?php

declare(strict_types = 1);

class TimeDistanceConverter{

    //date latitudine e longitudine del post e del preset (Eg. CittaACaso avrà un preset di 50 e 40, per esempio)
    //calcolo la distanza tra post e preset in linea d'aria.
    //Radice quadrata della somma del quadrato delle differenze delle ascisse e delle ordinate
    function findDistance (float $latPost, float $lngPost, float $latPreset, float $lngPreset) {
        $quaddiffascisse = pow($latPost - $latPreset, 2);
        $quaddiffordinate = pow($lngPost - $lngPreset, 2);
        return sqrt($quaddiffascisse + $quaddiffordinate);
    }

    //data la data (ahah gioco di parole) di un post, calcolare da quanto tempo è online in minuti
    //converto le date in string, string in timestamp e la differenza di timestamp viene restituita in minuti
    function findTimeInMinutes (string $datapost) {
        $dataattuale = gmdate("Y-m-d H:i:s");
        $timestamp1 = strtotime($datapost);
        $timestamp2 = strtotime($dataattuale);
        $hour = abs($timestamp2 - $timestamp1)/(60);
        return $hour;
    }
}