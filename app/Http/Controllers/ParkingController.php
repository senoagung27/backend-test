<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use Illuminate\Http\Request;

class ParkingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function masuk(Request $request)
    {
        $request->validate([
            'nomor_polisi' => 'required|string|max:20',
        ]);
        $nomorPolisi = $request->input('nomor_polisi');

        // Cek apakah mobil sudah pernah masuk atau sudah keluar
        $parking = Parking::where('nomor_polisi', $nomorPolisi)
            ->whereNull('waktu_keluar')
            ->first();

        if (!$parking) {
            // Mobil belum pernah masuk atau sudah keluar, generate kode unik
            $parking = Parking::create(['nomor_polisi' => $nomorPolisi]);
        }

        return response()->json(['kode_unik' => $parking->id]);
    }
    public function keluar(Request $request)
    {
        $kodeUnik = $request->input('kode_unik');
        $parking = Parking::find($kodeUnik);

        if (!$parking || $parking->waktu_keluar) {
            return response()->json(['error' => 'Kode unik tidak valid']);
        }

        // Hitung biaya parkir
        $waktuMasuk = strtotime($parking->waktu_masuk);
        $waktuKeluar = time();
        $durasiJam = ceil(($waktuKeluar - $waktuMasuk) / 3600);
        $biaya = $durasiJam * 3000;

        // Update data parkir
        $parking->update([
            'waktu_keluar' => now(),
            'biaya' => $biaya,
        ]);

        return response()->json(['biaya' => $biaya]);
    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Parking  $parking
     * @return \Illuminate\Http\Response
     */
    public function show(Parking $parking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Parking  $parking
     * @return \Illuminate\Http\Response
     */
    public function edit(Parking $parking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Parking  $parking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Parking $parking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Parking  $parking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Parking $parking)
    {
        //
    }
}
