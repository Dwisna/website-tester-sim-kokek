<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HasilLelang extends Model
{
    use SoftDeletes;

    protected $table = 'hasil_lelang';

    protected $primaryKey = 'id_hasil_lelang';

    public $incrementing = true;

    protected $keyType = 'int';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $guarded = [];

    public function daftarInformasiLelang()
    {
        return $this->belongsTo(
            DaftarInformasiLelang::class,
            'id_dil',
            'id_dil'
        );
    }
}