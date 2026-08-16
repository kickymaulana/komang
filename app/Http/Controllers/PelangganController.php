<?php

namespace App\Http\Controllers;

use App\Models\Master;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PelangganController extends Controller
{
    /**
     * Kolom yang diizinkan untuk pencarian (whitelist agar aman).
     */
    private const ALLOWED_COLUMNS = ['idpel', 'nama', 'alamat', 'trf', 'daya', 'kddk'];

    public function index(Request $request)
    {
        $query = trim((string) $request->string('q'));
        $kolom = (string) $request->string('kolom');

        $results = null;

        if ($query !== '' && in_array($kolom, self::ALLOWED_COLUMNS, true)) {
            $results = Master::query()
                ->whereRaw('LOWER(`'. $kolom .'`) LIKE ?', ['%'. mb_strtolower($query) .'%'])
                ->orderBy('id')
                ->paginate(25)
                ->withQueryString();
        }

        return Inertia::render('Pelanggan/Index', [
            'results' => $results,
            'kriteria' => $kolom,
            'query' => $query,
            'kolomOptions' => self::ALLOWED_COLUMNS,
        ]);
    }

    public function show(Master $master)
    {
        return Inertia::render('Pelanggan/Detail', [
            'pelanggan' => $master,
        ]);
    }
}
