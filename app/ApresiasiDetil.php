<?php

namespace App;

use App\Traits\HasModelExtender;
use Illuminate\Database\Eloquent\Model;

class ApresiasiDetil extends Model
{
    use HasModelExtender;

    protected $table = 'V_APRESIASI_DETIL';

    protected $primaryKey = '';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id_apresiasi',
        'klkl_id',
        'nilai',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(ApresiasiMhs::class, 'id_apresiasi', 'id_apresiasi');
    }
}
