<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Read-only model untuk tabel `master` (sudah ada di database, dibuat manual).
 *
 * @property int $id
 * @property string $idpel
 * @property string $nama
 * @property string $alamat
 * @property string $trf
 * @property string $daya
 * @property string $kddk
 */
class Master extends Model
{
    protected $table = 'master';

    public $timestamps = false;
}
