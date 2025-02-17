<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Commodity;
use PDF;

class LaporanBarangMasukController extends Controller
{
    public function index()
    {
        // Pastikan model Commodity sudah diimport dengan namespace yang benar
        $commodities = Commodity::with(['commodity_location', 'commodity_acquisition'])
            ->latest()
            ->get();

        return view('laporan.barang_masuk', [
            'title' => 'Laporan Barang Masuk',
            'page_heading' => 'Laporan Barang Masuk',
            'commodities' => $commodities
        ]);
    }

    public function cetak(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        $tgl_awal = $request->tanggal_awal;
        $tgl_akhir = $request->tanggal_akhir;

        // Ambil data berdasarkan rentang tanggal yang valid
        $commodities = Commodity::with(['commodity_location', 'commodity_acquisition'])
            ->whereBetween('created_at', [$tgl_awal, $tgl_akhir])
            ->orderBy('id', 'DESC')
            ->get();

        if ($commodities->isEmpty()) {
            return back()->with('error', 'Tidak ada data dalam rentang tanggal yang dipilih.');
        }

        $pdf = PDF::loadView('laporan.cetak_barang_masuk', compact('commodities', 'tgl_awal', 'tgl_akhir'));
        return $pdf->stream('laporan_barang_masuk.pdf');
    }
}