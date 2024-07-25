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

    public function specialOfferDescription(?string $itemName = ''): string {
        $discount = $this->discountPrice();
        $units = $this->requiredUnits();
        if ($itemName !== null) {
            $itemName = "of Item $itemName";
        }

        if ($this->discountPrice() == 0.00) {
            return "Buy {$units} {$itemName}, get one Free";
        }

        if ($this->requiredUnits()) {
            return "Buy {$units} {$itemName}, for {$discount}£";
        }

        $itemBundle = $this->itemBundle()->first()->bundleItem()->first()->name;
        $item = $this->itemBundle()->first()->item()->first()->name;

        return "Buy {$item} and {$itemBundle} for {$discount}£";
    }
}
