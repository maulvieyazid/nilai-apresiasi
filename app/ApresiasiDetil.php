<?php

namespace App;

use App\Traits\HasModelExtender;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent;
use Illuminate\Support\Facades\DB;

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
        'persen_kehadiran',
        'sts_presensi',
        'uas_lama',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(ApresiasiMhs::class, 'id_apresiasi', 'id_apresiasi');
    }


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
                {$this->skema}INS_APRESIASI_DETIL (
                    :id_apresiasi,
                    :klkl_id,
                    :nilai,
                    :persen_kehadiran,
                    :sts_presensi,
                    :uas_lama
                );

            END;
        SQL;

        $stmt = DB::getPdo()->prepare($sql);
        $stmt->bindValue('id_apresiasi', $this->id_apresiasi);
        $stmt->bindValue('klkl_id', $this->klkl_id);
        $stmt->bindValue('nilai', $this->nilai);
        $stmt->bindValue('persen_kehadiran', $this->persen_kehadiran);
        $stmt->bindValue('sts_presensi', $this->sts_presensi);
        $stmt->bindValue('uas_lama', $this->uas_lama);
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
     * Perform a model update operation.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return bool
     */
    /* protected function performUpdate(Eloquent\Builder $query)
    {
        // If the updating event returns false, we will cancel the update operation so
        // developers can hook Validation systems into their models and cancel this
        // operation if the model does not pass validation. Otherwise, we update.
        if ($this->fireModelEvent('updating') === false) {
            return false;
        }

        $sql = <<<SQL
            BEGIN
                {$this->skema}UPD_APRESIASI_DETIL (
                    :id_apresiasi,
                    :klkl_id,
                    :nilai
                );

            END;
        SQL;

        $stmt = DB::getPdo()->prepare($sql);
        $stmt->bindValue('id_apresiasi', $this->id_apresiasi);
        $stmt->bindValue('klkl_id', $this->klkl_id);
        $stmt->bindValue('nilai', $this->nilai);
        $stmt->execute();


        return true;
    } */


    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    protected function performDeleteOnModel()
    {
        $sql = <<<SQL
            BEGIN
                {$this->skema}DEL_APRESIASI_DETIL (
                    :id_apresiasi,
                    :klkl_id
                );

            END;
        SQL;

        $stmt = DB::getPdo()->prepare($sql);
        $stmt->bindValue('id_apresiasi', $this->id_apresiasi);
        $stmt->bindValue('klkl_id', $this->klkl_id);
        $stmt->execute();

        $this->exists = false;
    }
}
