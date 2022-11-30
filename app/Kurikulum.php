<?php

namespace App;

use App\Traits\HasModelExtender;
use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    use HasModelExtender;

    protected $table = 'KURLKL_MF';

    protected $primaryKey = '';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [];
}
