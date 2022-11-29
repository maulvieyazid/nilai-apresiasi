<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = 'V_MHS';

    protected $primaryKey = 'nim';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [];
}
