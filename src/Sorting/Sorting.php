<?php   

declare(strict_types = 1);

include(plugin_dir_path( __FILE__ ) . 'TimeAndDistanceConverter.php');
include(plugin_dir_path( __FILE__ ) . 'Pillole.php');
include(plugin_dir_path( __FILE__ ) . 'PilloleWeight.php');



    class pilloleSorting{

        public function SortingByWeight(array $pilloleList, float $userLat, float $userLng, float $distanceWeight = 5, float $timeWeight = 5) 
        {
            if($pilloleList == [])
                return [];

            $converter = new TimeDistanceConverter();
            $pillole = [];
            
            $pilloleSize = sizeof($pilloleList);
            for ($i=0; $i < $pilloleSize; $i++) { 
                array_push($pillole, new PillolaData(
                    $pilloleList[$i]->id,
                    $converter->findDistance($pilloleList[$i]->lat, $pilloleList[$i]->lng, $userLat, $userLng),
                    $converter->findTimeInMinutes($pilloleList[$i]->date)
                ));
            }

            $pilloleIdOrderByWeight = $this->GetIdByWaight($pillole, $distanceWeight, $timeWeight);

            return $pilloleIdOrderByWeight;
        }

        private function GetIdByWaight(array $pillole, float $distanceWeight, float $timeWeight)
        {
            $maxdistance = 0;
            $maxTimeInMinutes = 0;

            $this->GetMaxValues($pillole, $maxdistance, $maxTimeInMinutes);
            
            $pilloleSize = sizeof($pillole);

            for ($i=0; $i < $pilloleSize; $i++) { 
                $pillole[$i]->distance = $this->NormalizeValue($pillole[$i]->distance, $maxdistance);
                $pillole[$i]->timeDistanceInMinutes = $this->NormalizeValue($pillole[$i]->timeDistanceInMinutes, $maxTimeInMinutes);
            }

            $pilloleWeight = [];
            for ($i=0; $i < $pilloleSize; $i++) { 
                $weight = ($pillole[$i]->distance * $distanceWeight) + ($pillole[$i]->timeDistanceInMinutes * $timeWeight);
                array_push($pilloleWeight, new PillolaWeight(
                    $pillole[$i]->id,
                    $weight,
                ));
            }

            usort($pilloleWeight, "pilloleSorting::cmp");
            
            $pilloleIdOrdered = [];
            for ($i=0; $i < $pilloleSize; $i++) { 
                array_push($pilloleIdOrdered, $pilloleWeight[$i]->id);
            }

            $pilloleIdOrdered = array_unique($pilloleIdOrdered);
            return $pilloleIdOrdered;
        }

        private function GetMaxValues(array $pillole, float &$maxDistance, float &$maxTimeInMinutes)
        {
            $pilloleSize = sizeof($pillole);
            for ($i=0; $i < $pilloleSize; $i++) { 

                if($pillole[$i]->distance > $maxDistance)
                    $maxDistance = $pillole[$i]->distance;

                if($pillole[$i]->timeDistanceInMinutes > $maxTimeInMinutes)
                    $maxTimeInMinutes = $pillole[$i]->timeDistanceInMinutes;
            }
        }

        private function NormalizeValue(float $firstValue, float $maxValue)
        {
            return $firstValue / $maxValue;
        }

        private static function cmp($a, $b) {
            return ($a->weight > $b->weight) ? +1 : -1;
        }
    }