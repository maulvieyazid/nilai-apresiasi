<?php

namespace App;

use App\Traits\HasModelExtender;
use Illuminate\Database\Eloquent\Model;

class TrklklMf extends Model
{
    use HasModelExtender;

    protected $table = 'V_TRANSK';

    protected $primaryKey = '';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [];


    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'klkl_id', 'id');
    }
}
