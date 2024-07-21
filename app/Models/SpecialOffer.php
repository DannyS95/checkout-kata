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

    public function specialOfferDescription(): string {
        $discount = $this->discountPrice();
        $units = $this->requiredUnits();

        if ($this->discountPrice() == 0.00) {
            return "Buy {$units}, get one Free ";
        }

        if ($this->requiredUnits()) {
            return "Buy {$units}, for {$discount}£";
        }

        $itemBundle = $this->itemBundle()->first()->bundleItemId();
        $item = $this->itemBundle()->first()->itemId();

        return "Buy {$item} and {$itemBundle} for {$discount}£";
    }

}
