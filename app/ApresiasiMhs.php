<?php

namespace App;

use App\Traits\HasModelExtender;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent;
use Illuminate\Support\Facades\DB;

class ApresiasiMhs extends Model
{
    use HasModelExtender;

    protected $table = 'V_APRESIASI_MHS';

    protected $primaryKey = 'id_apresiasi';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'id_apresiasi',
        'smt',
        'nim',
        'jenis_kegiatan',
        'prestasi_kegiatan',
        'tingkat_kegiatan',
        'keterangan',
        'bukti_kegiatan',
    ];

    public function detil()
    {
        return $this->hasMany(ApresiasiDetil::class, 'id_apresiasi', 'id_apresiasi');
    }

    public function mhs()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim')->addSelect(['nim', 'nama']);
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

        $this->setAutoIncrementKey();

        $sql = <<<SQL
            BEGIN
                {$this->skema}INS_APRESIASI_MHS (
                    :id_apresiasi,
                    :smt,
                    :nim,
                    :jenis_kegiatan,
                    :prestasi_kegiatan,
                    :tingkat_kegiatan,
                    :keterangan,
                    :bukti_kegiatan
                );

            END;
        SQL;

        $stmt = DB::getPdo()->prepare($sql);
        $stmt->bindValue('id_apresiasi', $this->id_apresiasi);
        $stmt->bindValue('smt', $this->smt);
        $stmt->bindValue('nim', $this->nim);
        $stmt->bindValue('jenis_kegiatan', $this->jenis_kegiatan);
        $stmt->bindValue('prestasi_kegiatan', $this->prestasi_kegiatan);
        $stmt->bindValue('tingkat_kegiatan', $this->tingkat_kegiatan);
        $stmt->bindValue('keterangan', $this->keterangan);
        $stmt->bindValue('bukti_kegiatan', $this->bukti_kegiatan);
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
    protected function performUpdate(Eloquent\Builder $query)
    {
        // If the updating event returns false, we will cancel the update operation so
        // developers can hook Validation systems into their models and cancel this
        // operation if the model does not pass validation. Otherwise, we update.
        if ($this->fireModelEvent('updating') === false) {
            return false;
        }

        $sql = <<<SQL
            BEGIN
                {$this->skema}UPD_APRESIASI_MHS (
                    :id_apresiasi,
                    :smt,
                    :nim,
                    :jenis_kegiatan,
                    :prestasi_kegiatan,
                    :tingkat_kegiatan,
                    :keterangan,
                    :bukti_kegiatan
                );

            END;
        SQL;

        $stmt = DB::getPdo()->prepare($sql);
        $stmt->bindValue('id_apresiasi', $this->id_apresiasi);
        $stmt->bindValue('smt', $this->smt);
        $stmt->bindValue('nim', $this->nim);
        $stmt->bindValue('jenis_kegiatan', $this->jenis_kegiatan);
        $stmt->bindValue('prestasi_kegiatan', $this->prestasi_kegiatan);
        $stmt->bindValue('tingkat_kegiatan', $this->tingkat_kegiatan);
        $stmt->bindValue('keterangan', $this->keterangan);
        $stmt->bindValue('bukti_kegiatan', $this->bukti_kegiatan);
        $stmt->execute();


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
                {$this->skema}DEL_APRESIASI_MHS (
                    :id_apresiasi
                );

            END;
        SQL;

        $stmt = DB::getPdo()->prepare($sql);
        $stmt->bindValue('id_apresiasi', $this->id_apresiasi);
        $stmt->execute();

        $this->exists = false;
    }
}
