<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialOffer extends Model
{
    use HasFactory;

    public function discountPrice()
    {
        return $this->offer_discount_price;
    }

    public function requiredUnits()
    {
        return $this->required_units;
    }

    public function itemBundle()
    {
        return $this->belongsTo(ItemBundlesSpecialOffer::class, 'id', 'special_offer_id');
    }
}
