<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\InvoiceResource;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller {

    use HttpResponse;

    public function __construct()
    {
        $this->middleware("auth:sanctum")->only(["store", "update", "destroy"]);
    }

    public function index(Request $request)
    {
        return (new Invoice())->filter($request);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "type" => [
                "required",
                "max:1",
                Rule::in(["B", "C", "P"])
            ], 
            "paid" => "required|numeric|between:0,1",
            "payment_date" => "nullable",
            "value" => "required|numeric",
        ]);

        if($validator->fails()) {
            return $this->error("Data invalid",  422, $validator->errors());
        }

        if(!User::find($validator->validated()['user_id'])) {
            return $this->error("Argument invalid", 404, ["user_id" => "Id invalid"]);
        }

        $created = Invoice::create($validator->validated());

        if(!$created) {
            return $this->error("Error at to register", 400);
        }

        return $this->success("Invoice created with success", 200, new InvoiceResource($created->load('user')));
    }

    public function show($id)
    {
        $validator = Validator::make(["id" => $id], [
            "id" => "required|numeric"
        ]);

        if($validator->fails()) {
            return $this->error("Data invalid", 422, $validator->errors());
        }

        $invoice = Invoice::find($id);

        if(!$invoice) {
            return $this->error("Invoice not found", 404, ["id" => "Id invalid"]);
        }

        return new InvoiceResource($invoice);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|numeric",
            "type" => [
                "required",
                "max:1",
                Rule::in(["B", "C", "P"])
            ], 
            "paid" => "required|numeric|between:0,1",
            "payment_date" => "nullable|date_format:Y-m-d H:i:s",
            "value" => "nullable|numeric",
        ]);

        if($validator->fails()) {
            return $this->error("Data invalid", 422, $validator->errors());
        }

        $validated = (object) $validator->validated();

        if(!User::find($validated->user_id)) {
            return $this->error("Updated failed", 404, ["user_id" => "User id not found"]);
        }

        $invoice = Invoice::find($id);

        if(!$invoice) {      
            return $this->error("Updated failed", 404, ["id" => "Invoice id not found"]);
        }
    
        $updated = $invoice->update([
            "user_id" => $validated->user_id,
            "type" => $validated->type,
            "paid" => $validated->paid,
            "value" => $validated->value ?? $invoice->value,
            "payment_date" => $validated->paid ? $validated->payment_date : null,
        ]);

        if($updated) {
            return $this->success("Invoice updated with success", 200, new InvoiceResource($invoice->load('user')));
        }

        return $this->error("Updated failed", 400);
    }

    public function destroy($id)
    {
        $validator = Validator::make(["id" => $id], [
            "id" => "required|numeric"
        ]);

        if($validator->fails()) {
            return $this->error("Data invalid", 422, $validator->errors());
        }

        if(Invoice::find($id)) {
            return $this->success("Invoice deleted with success");
        }

        return $this->error("Deleted failed", 404, ["id" => "Id invalid"]);
    }
}
