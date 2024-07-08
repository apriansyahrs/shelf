<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'letter_number',
        'purchase_date',
        'business_entity',
        'item_name',
        'category_id',
        'brand',
        'type',
        'serial_number',
        'imei1',
        'imei2',
        'item_price',
        'inventory_holder_name',
        'inventory_holder_position',
        'item_location',
        'item_age',
        'status',
        'upload_bast',
    ];

    public function getItemAgeAttribute()
    {
        $purchaseDate = Carbon::parse($this->attributes['purchase_date']);
        return $purchaseDate->diffInDays(Carbon::now());
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
