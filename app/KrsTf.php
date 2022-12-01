<?php

namespace App;

use App\Traits\HasModelExtender;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent;

// Karena Model KrsTf ini bukan untuk dibaca datanya, melainkan hanya insert dan delete
// Maka tidak disertakan data2 attribut yang lengkap
class KrsTf extends Model
{
    use HasModelExtender;

    const DEFAULT_JKUL_KELAS = 'PR';

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
        $stmt->bindValue('nilai_huruf', '');
        $stmt->bindValue('sts_mk', '');
        $stmt->execute();

        $this->exists = false;
    }
}
