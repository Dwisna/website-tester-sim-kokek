<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataTahapanLelang extends Model
{
    use SoftDeletes;

    protected $table = 'data_tahapan_lelang';

    protected $primaryKey = 'id_data_tahapan_lelang';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $guarded = [];

    public function tahapLelang()
    {
        return $this->hasMany(
            TahapLelang::class,
            'id_data_tahapan_lelang',
            'id_data_tahapan_lelang'
        );
    }
}