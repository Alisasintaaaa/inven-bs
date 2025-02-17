<?php

namespace App\Imports;

use App\Commodity;
use App\CommodityAcquisition;
use App\CommodityLocation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class CommoditiesImport implements ToModel, WithHeadingRow, WithUpserts
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $commodity_location = CommodityLocation::where('name', $row['lokasi'])->first();
        $commodity_acquisition = CommodityAcquisition::where('name', $row['asal_perolehan'])->first();

        return new Commodity([
            'item_code' => $row['kode_barang'] ?? null,
            'name' => $row['nama_barang'] ?? null,
            'brand' => $row['merek'] ?? null,
            'material' => $row['bahan'] ?? null,
            'commodity_acquisition_id' => $commodity_acquisition->id ?? null,
            'commodity_location_id' => $commodity_location->id ?? null,
            'year_of_purchase' => $row['tahun_pembelian'] ?? null,
            'condition' => $this->translateConditionNameToNumber($row['kondisi'] ?? ''),
            'quantity' => $row['kuantitas'] ?? 0,
            'price' => $row['harga'] ?? 0,
            'price_per_item' => $row['harga_satuan'] ?? 0,
            'note' => $row['keterangan'] ?? '',
        ]);
    }

    /**
     * Translate condition name to the corresponding number.
     */
    public function translateConditionNameToNumber($conditionName)
    {
        return match (strtolower($conditionName)) {
            'baik' => 1,
            'kurang baik' => 2,
            'rusak berat' => 3,
            default => 0,
        };
    }

    /**
     * Specify the unique column used for upsert operations.
     */
    public function uniqueBy()
    {
        return 'item_code';
    }
}
