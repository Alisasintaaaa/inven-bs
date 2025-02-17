<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommodityInOut extends Model
{
    use HasFactory;

    protected $table = 'commodities_in_out';

    protected $fillable = [
        'commodity_id',
        'stock',
        'is_in',
        'created_at',
    ];

    public $timestamps = false;

    /**
     * Relasi ke model Commodity.
     */
    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }

    /**
     * Scope untuk mendapatkan barang yang masuk.
     */
    public function scopeIncoming($query)
    {
        return $query->where('is_in', true);
    }

    /**
     * Scope untuk mendapatkan barang yang keluar.
     */
    public function scopeOutgoing($query)
    {
        return $query->where('is_in', false);
    }
}
