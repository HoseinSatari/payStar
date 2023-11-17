<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "order_id",
        "gateway_name",
        "tracking_code",
        "card_number",
        "order_amount",
        "final_amount",
        "token",
        "is_paid",
        "reserved_transaction",
        "pay_time",
        "transaction_id",
        "status_code_response_gateway",
        "ref_num"


    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
