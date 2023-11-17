<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [

        "user_id",
        "order_code",
        "status",

    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')->withPivot(['quantity', 'amount']);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
