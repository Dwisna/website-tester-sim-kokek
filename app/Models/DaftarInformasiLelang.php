<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DaftarInformasiLelang extends Model
{
    use SoftDeletes;

    protected $table = 'daftar_informasi_lelang';

    protected $primaryKey = 'id_dil';

    public $incrementing = true;

    protected $keyType = 'int';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $guarded = [];

    public function hasilLelang()
    {
        return $this->hasMany(
            HasilLelang::class,
            'id_dil',
            'id_dil'
        );
    }

    public function tahapLelang()
    {
        return $this->hasMany(
            TahapLelang::class,
            'id_dil',
            'id_dil'
        );
    }
}