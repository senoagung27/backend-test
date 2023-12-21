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
            'nomor_polisi' => 'required|string',
        ]);

        // Cek apakah mobil sudah pernah masuk atau keluar
        $parkir = Parking::where('nomor_polisi', $request->nomor_polisi)
            ->whereNull('waktu_keluar')
            ->first();

        if (!$parkir) {
            // Mobil belum pernah masuk atau sudah keluar, generate kode unik dan catat waktu masuk
            $parkir = Parking::create([
                'kode_unik' => uniqid(),
                'nomor_polisi' => $request->nomor_polisi,
                'waktu_masuk' => now(),
            ]);
        }

        return response()->json(['kode_unik' => $parkir->kode_unik]);
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

    public function laporan(Request $request)
    {
        $dari = $request->get('dari', date('Y-m-d'));
        $sampai = $request->get('sampai', date('Y-m-d'));

        $laporan = Parking::whereBetween('waktu_masuk', [$dari, $sampai])->get();

        return response()->json([
            'message' => 'Laporan berhasil dimuat',
            'data' => $laporan,
        ], 200);
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
