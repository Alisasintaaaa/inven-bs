<?php

namespace App\Http\Controllers;

use App\CommodityInOut;
use App\Commodity;
use Illuminate\Http\Request;

class CommodityInOutController extends Controller
{
    /**
     * Menampilkan daftar barang masuk/keluar.
     */
    public function index()
    {
        $commodityInOuts = CommodityInOut::with('commodity')->latest()->paginate(10);
        $commodities = Commodity::all();
        return view('commodity_in_out.index', compact('commodityInOuts', 'commodities'));
    }

    /**
     * Menampilkan form tambah barang masuk/keluar.
     */
    public function create()
    {
        $commodities = Commodity::all();
        return view('commodity_in_out.create', compact('commodities'));
    }

    /**
     * Menyimpan barang masuk/keluar ke database dengan validasi.
     */
    public function store(Request $request)
    {
        $request->validate([
            'commodity_id' => 'required|exists:commodities,id',
            'stock' => 'required|integer|min:1',
            'is_in' => 'required|boolean',
        ], [
            'commodity_id.required' => 'Barang harus dipilih.',
            'commodity_id.exists' => 'Barang yang dipilih tidak valid.',
            'stock.required' => 'Stok tidak boleh kosong.',
            'stock.integer' => 'Stok harus berupa angka.',
            'stock.min' => 'Jumlah stok harus minimal 1.',
            'is_in.required' => 'Jenis transaksi (masuk/keluar) harus dipilih.',
            'is_in.boolean' => 'Format data tidak valid untuk status barang masuk/keluar.',
        ]);

        try {
            CommodityInOut::create([
                'commodity_id' => $request->commodity_id,
                'stock' => $request->stock,
                'is_in' => $request->is_in,
                'created_at' => now(),
            ]);

            return redirect()->route('commodity_in_out.index')->with('success', 'Data barang keluar/masuk berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data.'])->withInput();
        }
    }

    /**
     * Menampilkan form edit barang masuk/keluar.
     */
    public function edit(CommodityInOut $commodityInOut)
    {
        $commodities = Commodity::all();
        return view('commodity_in_out.edit', compact('commodityInOut', 'commodities'));
    }

    /**
     * Memperbarui data barang masuk/keluar dengan validasi.
     */
    public function update(Request $request, CommodityInOut $commodityInOut)
    {
        $request->validate([
            'commodity_id' => 'required|exists:commodities,id',
            'stock' => 'required|integer|min:1',
            'is_in' => 'required|boolean',
        ], [
            'commodity_id.required' => 'Barang harus dipilih.',
            'commodity_id.exists' => 'Barang yang dipilih tidak valid.',
            'stock.required' => 'Stok tidak boleh kosong.',
            'stock.integer' => 'Stok harus berupa angka.',
            'stock.min' => 'Jumlah stok harus minimal 1.',
            'is_in.required' => 'Jenis transaksi (masuk/keluar) harus dipilih.',
            'is_in.boolean' => 'Format data tidak valid untuk status barang masuk/keluar.',
        ]);

        try {
            $commodityInOut->update($request->all());

            return redirect()->route('commodity_in_out.index')->with('success', 'Data barang keluar/masuk berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data.'])->withInput();
        }
    }

    /**
     * Menghapus data barang masuk/keluar.
     */
    public function destroy(CommodityInOut $commodityInOut)
    {
        try {
            $commodityInOut->delete();
            return redirect()->route('commodity_in_out.index')->with('success', 'Data barang keluar/masuk berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus data.']);
        }
    }

    /**
     * Menampilkan laporan barang masuk/keluar berdasarkan filter.
     */
    public function report(Request $request)
    {
        $query = CommodityInOut::with('commodity');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('is_in')) {
            $query->where('is_in', $request->is_in);
        }

        $commodityInOuts = $query->latest()->get();
        return view('commodity_in_out.report', compact('commodityInOuts'));
    }
}
