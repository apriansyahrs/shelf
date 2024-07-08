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
        'business_entity_id',
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
        'status',
        'upload_bast',
    ];

    private function formatDiff($value, $unit)
    {
        return $value . ' ' . $unit;
    }

    public function getItemAgeAttribute()
    {
        $purchaseDate = Carbon::parse($this->attributes['purchase_date']);
        $now = Carbon::now();

        $diffInDays = $purchaseDate->diffInDays($now);
        $diffInMonths = $purchaseDate->diffInMonths($now);
        $diffInYears = $purchaseDate->diffInYears($now);

        if ($diffInYears > 0) {
            return $this->formatDiff($diffInYears, 'tahun');
        } elseif ($diffInMonths > 0) {
            return $this->formatDiff($diffInMonths, 'bulan');
        } else {
            return $this->formatDiff($diffInDays, 'hari');
        }
    }

    public function businessEntity()
    {
        return $this->belongsTo(BusinessEntity::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
