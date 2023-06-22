<?php

namespace App\Models;

use App\Filters\InvoiceFilter;
use App\Http\Resources\V1\InvoiceResource;
use App\Traits\PaginateValidation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class Invoice extends Model
{
    use PaginateValidation;
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function filter(Request $request) {

        $filter = (new InvoiceFilter)->filter($request);

        if(!$filter) {
            return (InvoiceResource::collection(Invoice::with('user')->paginate($this->validation($request))));
        }

        $data = Invoice::with('user');

        foreach ($filter as $typeOfwhere => $value) {

            if(!empty($typeOfwhere)) {

                if(in_array($typeOfwhere, ["whereIn","whereDay","whereMonth", "whereYear"])) {
                    $data->$typeOfwhere($value[0], $value[2]); 
                }

            }

        }

        $resource = $data->where($filter['where'])->get();

        return InvoiceResource::collection($resource);
    }
}
