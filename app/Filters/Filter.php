<?php

namespace App\Filters;

use App\Traits\Filter as TraitsFilter;
use Illuminate\Http\Request;

use Exception;
use Illuminate\Support\Arr;

abstract class Filter {

    use TraitsFilter;

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
    
}