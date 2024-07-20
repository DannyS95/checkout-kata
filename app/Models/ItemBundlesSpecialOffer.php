<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemBundlesSpecialOffer extends Model
{
    use HasFactory;

    public function bundleItemId() {
        return $this->bundle_item_id;
    }

    public function itemId() {
        return $this->bundle_item_id;
    }
}
