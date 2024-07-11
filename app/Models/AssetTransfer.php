<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetTransfer extends Model
{
    use HasFactory;

    protected $fillable = ['business_entity_id', 'letter_number', 'from_user_id', 'to_user_id', 'upload_bast'];

    // Relasi ke tabel assets
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // Relasi ke tabel users untuk from_user_id
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    // Relasi ke tabel users untuk to_user_id
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    // Relasi ke tabel asset_transfer_details
    public function details(): HasMany
    {
        return $this->hasMany(AssetTransferDetail::class);
    }
}
