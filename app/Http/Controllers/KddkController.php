<?php

namespace App\Http\Controllers;

use App\Models\Master;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KddkController extends Controller
{
    public function show(Request $request, string $kode)
    {
        // Format kddk: prefix + 5 digit suffix (contoh: BLBBBEA09200)
        if (strlen($kode) < 6) {
            abort(404);
        }

        $prefix = substr($kode, 0, -5);

        $target = Master::query()
            ->where('kddk', $kode)
            ->first();

        $list = Master::query()
            ->where('kddk', 'like', $prefix.'%')
            ->orderByRaw('CAST(SUBSTRING(kddk, -5) AS UNSIGNED) asc')
            ->get();

        return Inertia::render('Kddk/Index', [
            'kode' => $kode,
            'prefix' => $prefix,
            'list' => $list,
            'targetId' => $target?->id,
        ]);
    }
}
