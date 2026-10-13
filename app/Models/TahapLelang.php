<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahapLelang extends Model
{
    protected $table = 'tahap_lelang';

    protected $primaryKey = 'id_tahap_lelang';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $guarded = [];

    public function daftarInformasiLelang()
    {
        return $this->belongsTo(
            DaftarInformasiLelang::class,
            'id_dil',
            'id_dil'
        );
    }

    public function dataTahapan()
    {
        return $this->belongsTo(
            DataTahapanLelang::class,
            'id_data_tahapan_lelang',
            'id_data_tahapan_lelang'
        );
    }
}