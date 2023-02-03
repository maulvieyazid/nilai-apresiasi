<?php

namespace App;

use App\Traits\HasModelExtender;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent;
use Illuminate\Support\Facades\DB;


class KrsTf extends Model
{
    use HasModelExtender;

    // const DEFAULT_JKUL_KELAS = 'PR';
    const DEFAULT_PRO_HDR = 100;
    const DEFAULT_STS_PRE = null;

    const JENIS_SIMPAN = 1;
    const JENIS_HAPUS = 2;

    // protected $table = 'AAK_MAN.KRS_TF';
    protected $table = 'V_KRST';

    public $incrementing = false;

    public $timestamps = false;

    protected $guarded = [];

    protected $appends = ['klkl_id'];


    // ACCESSOR
    public function getKlklIdAttribute()
    {
        return $this->jkul_klkl_id;
    }

    public function getNilaiHurufAttribute()
    {
        $nilai_huruf = DB::select("select nilai_huruf(?) AS nilai_huruf from dual", [$this->n_uas])[0]->nilai_huruf;

        return $nilai_huruf;
    }


    // RELATIONSHIP
    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'jkul_klkl_id', 'id');
    }


    /**
     * Perform a model insert operation.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return bool
     */
    /* protected function performInsert(Eloquent\Builder $query)
    {
        if ($this->fireModelEvent('creating') === false) {
            return false;
        }

        $sql = <<<SQL
            BEGIN
                {$this->skema}INS_APRESIASI_KRS (
                    :jkul_kelas,
                    :jkul_klkl_id,
                    :mhs_nim,
                    :nilai_akhir,
                    :nilai_huruf,
                    :sts_mk
                );

            END;
        SQL;

        $stmt = DB::getPdo()->prepare($sql);
        $stmt->bindValue('jkul_kelas', self::DEFAULT_JKUL_KELAS);
        $stmt->bindValue('jkul_klkl_id', $this->jkul_klkl_id);
        $stmt->bindValue('mhs_nim', $this->mhs_nim);
        $stmt->bindValue('nilai_akhir', $this->nilai_akhir);
        $stmt->bindValue('nilai_huruf', $this->nilai_huruf);
        $stmt->bindValue('sts_mk', $this->sts_mk);
        $stmt->execute();

        // We will go ahead and set the exists property to true, so that it is set when
        // the created event is fired, just in case the developer tries to update it
        // during the event. This will allow them to do so and run an update here.
        $this->exists = true;

        $this->wasRecentlyCreated = true;

        $this->fireModelEvent('created', false);

        return true;
    } */


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
                {$this->skema}UPD_APRESIASI_KRS (
                    :mhs_nim,
                    :jkul_klkl_id,
                    :jkul_kelas,
                    :nilai,
                    :jenis
                );

            END;
        SQL;

        $stmt = DB::getPdo()->prepare($sql);
        $stmt->bindValue('mhs_nim', $this->mhs_nim);
        $stmt->bindValue('jkul_klkl_id', $this->jkul_klkl_id);
        $stmt->bindValue('jkul_kelas', $this->jkul_kelas);
        $stmt->bindValue('nilai', $this->nilai);
        $stmt->bindValue('jenis', $this->jenis);
        $stmt->execute();

        return true;
    }



    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    /*  protected function performDeleteOnModel()
    {
        $sql = <<<SQL
            BEGIN
                {$this->skema}DEL_APRESIASI_KRS (
                    :jkul_kelas,
                    :jkul_klkl_id,
                    :mhs_nim,
                    :nilai_akhir,
                    :nilai_huruf,
                    :sts_mk
                );

            END;
        SQL;

        $stmt = DB::getPdo()->prepare($sql);
        $stmt->bindValue('jkul_kelas', self::DEFAULT_JKUL_KELAS);
        $stmt->bindValue('jkul_klkl_id', $this->jkul_klkl_id);
        $stmt->bindValue('mhs_nim', $this->mhs_nim);
        $stmt->bindValue('nilai_akhir', 0);
        $stmt->bindValue('nilai_huruf', null);
        $stmt->bindValue('sts_mk', null);
        $stmt->execute();

        $this->exists = false;
    } */
}
