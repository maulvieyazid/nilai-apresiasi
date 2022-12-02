<?php

namespace App;

use App\Traits\HasModelExtender;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent;
use Illuminate\Support\Facades\DB;

// Karena Model KrsTf ini bukan untuk dibaca datanya, melainkan hanya insert dan delete
// Maka tidak disertakan data2 attribut yang lengkap
class Jdwkul extends Model
{
    use HasModelExtender;

    public $incrementing = false;

    public $timestamps = false;

    protected $guarded = [];



    /**
     * Perform a model insert operation.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return bool
     */
    protected function performInsert(Eloquent\Builder $query)
    {
        if ($this->fireModelEvent('creating') === false) {
            return false;
        }

        $sql = <<<SQL
            BEGIN
                {$this->skema}INS_APRESIASI_JDWKUL (
                    :mhs_nim,
                    :klkl_id,
                    :sks,
                    :kelas
                );

            END;
        SQL;

        $stmt = DB::getPdo()->prepare($sql);
        $stmt->bindValue('mhs_nim', $this->mhs_nim);
        $stmt->bindValue('klkl_id', $this->klkl_id);
        $stmt->bindValue('sks', $this->sks);
        $stmt->bindValue('kelas', KrsTf::DEFAULT_JKUL_KELAS);
        $stmt->execute();

        // We will go ahead and set the exists property to true, so that it is set when
        // the created event is fired, just in case the developer tries to update it
        // during the event. This will allow them to do so and run an update here.
        $this->exists = true;

        $this->wasRecentlyCreated = true;

        $this->fireModelEvent('created', false);

        return true;
    }


    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    protected function performDeleteOnModel()
    {
        $sql = <<<SQL
            BEGIN
                {$this->skema}DEL_APRESIASI_JDWKUL (
                    :klkl_id,
                    :kelas,
                    :prodi
                );

            END;
        SQL;

        $stmt = DB::getPdo()->prepare($sql);
        $stmt->bindValue('klkl_id', $this->klkl_id);
        $stmt->bindValue('kelas', KrsTf::DEFAULT_JKUL_KELAS);
        $stmt->bindValue('prodi', $this->prodi);
        $stmt->execute();

        $this->exists = false;
    }
}
