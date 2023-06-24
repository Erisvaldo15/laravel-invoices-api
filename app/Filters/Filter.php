<?php

namespace App\Filters;

use Illuminate\Http\Request;

use Exception;
use Illuminate\Support\Arr;

abstract class Filter {

    private array $where = [];

    protected array $allowedOperatorForEachField = [];

    private array $filtersAssociation = [
        "gt" => ">",
        "gte" => ">=",
        "lt" => "<",
        "lte" => "<=",
        "eq" => "=",
        "ne" => "!=",
        "in" => "in",
        "dt" => ["d", "m", "y"],
    ];

    public function filter(Request $request) {

        if(empty($this->allowedOperatorForEachField)) {
            throw new Exception("allowedOperatorsForEachField property is empty"); 
        }

        foreach ($this->allowedOperatorForEachField as $fieldForFilter => $allowedTypesOffilter) {
            
            $queryOperator = $request->query($fieldForFilter);

            if($queryOperator) {

                foreach ($queryOperator as $typeOfFilter => $value) {

                    if(!in_array($typeOfFilter, $allowedTypesOffilter)) {
                        throw new Exception("Filter '{$typeOfFilter}' does not allowed for this field!");
                    }

                    $formattedValue = $value;

                    if(in_array($typeOfFilter, ["in", "dt"])) {
                        $formattedValue = explode(',', str_replace(["[", "]"],"",$value));
                    }

               
                    $this->where($formattedValue, $typeOfFilter, $fieldForFilter);
                }

            }

        }

        if(!Arr::hasAny($this->where, ["where", "whereDay", "whereMonth", "whereYear", "whereIn"])) {
            return false;
        }

        return $this->where;
    }
    
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