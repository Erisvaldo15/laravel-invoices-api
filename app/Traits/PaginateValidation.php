<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait PaginateValidation {

    private int $limit = 10;

    public function validation(Request $request): int {

        $queryLimit = $request->query("limit");

        if(isset($queryLimit)) {

            $validator = Validator::make(["limit" => $queryLimit], ["limit" => "required|numeric"]);   

            if($validator->fails()) {
                return $this->limit;
            }

            $validated = (object) $validator->validated();

            return $validated->limit;
        }

        return $this->limit;
    }

}