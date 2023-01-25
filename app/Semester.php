<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $table = 'V_SMT';

    protected $primaryKey = 'fak_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [];
}
