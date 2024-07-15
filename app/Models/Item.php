<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    public function itemSpecialOffers()
    {
        return $this->hasManyThrough(SpecialOffer::class, ItemSpecialOffer::class, 'item_id', 'id', 'id', 'special_offer_id');
    }

    public function itemBundlesSpecialOffers()
    {
        return $this->hasManyThrough(SpecialOffer::class, ItemBundlesSpecialOffer::class, 'item_id', 'id', 'id', 'special_offer_id');
    }

    public function unitPrice()
    {
        return $this->unit_price / 100;
    }
}
