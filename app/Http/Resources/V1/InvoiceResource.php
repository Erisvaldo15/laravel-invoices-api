<?php

namespace App\Http\Resources\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    
    private array $typesOfPayments = [
        "C" => "Cartão",
        "B" => "Boleto",
        "P" => "Pix",
    ];

    public function toArray(Request $request): array
    {
        $carbon = Carbon::parse($this->payment_date, "America/Sao_Paulo");

        return [
            "user" => [
                "name" => $this->user->name,
                "email" => $this->user->email,
            ],
            "type" => $this->typesOfPayments[strtoupper($this->type)],
            "value" => "R$".number_format($this->value, 2, ",", "."),
            "situation" => $this->paid ? "Paid" : "Pendent",
            "payment_date" => $this->paid ? $carbon->format("d-m-Y H:i:s") : null,
            "payment_since" => $this->paid ? $carbon->diffForHumans() : null, 
        ];
    }
}
