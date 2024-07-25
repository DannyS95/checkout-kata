<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ItemBundlesSpecialOffer extends Model
{
    use HasFactory;

    public function bundleItemId() {
        return $this->bundle_item_id;
    }

    public function itemId() {
        return $this->bundle_item_id;
    }

    public function bundleItem() {
        return $this->belongsTo(Item::class, 'bundle_item_id', 'id');
    }

    public function item() {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
