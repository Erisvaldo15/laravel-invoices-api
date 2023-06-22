<?php

namespace App\Filters;

class InvoiceFilter extends Filter {

    protected array $allowedOperatorForEachField = [
        "value" => ["eq", "ne", "gt", "gte", "lt", "lte"],
        "type" => ["in", "eq", "ne"],
        "paid" => ["eq", "ne"],
        "payment_date" => ["dt"],
    ]; 

}