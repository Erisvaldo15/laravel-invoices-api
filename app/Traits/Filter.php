<?php

namespace App\Traits;

use Illuminate\Support\Arr;

trait Filter {

    public array $where = [];

    public function where(array|string $value, string $typeOfFilter, string $fieldForFilter) {

        switch ($typeOfFilter) {

            case 'in':
                
                $this->where["whereIn"] = [
                    $fieldForFilter,
                    $this->filtersAssociation[$typeOfFilter],
                    $value,
                ];
        
                break;
            
            case "dt":

                $searchByTime = [
                    "d" => "whereDay",
                    "m" => "whereMonth",
                    "y" => "whereYear",
                ];

                $this->where[Arr::get($searchByTime, $value[0])] = [
                        $fieldForFilter,
                        $value[0],
                        $value[1],
                ];

                break;

            default:

                $this->where["where"][] = [
                    $fieldForFilter,
                    $this->filtersAssociation[$typeOfFilter],
                    $value,
                ];

                break;
        }

    }

}