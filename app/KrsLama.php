<?php

namespace App;

use App\Traits\HasModelExtender;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent;
use Illuminate\Support\Facades\DB;

class KrsLama extends Model
{
    use HasModelExtender;

    protected $table = 'V_APRESIASI_KRSLAMA';

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
                {$this->skema}INS_APRESIASI_KRSLAMA (
                    :mhs_nim,
                    :jkul_klkl_id,
                    :jkul_kelas
                );

            END;
        SQL;

        $stmt = DB::getPdo()->prepare($sql);
        $stmt->bindValue('mhs_nim', $this->mhs_nim);
        $stmt->bindValue('jkul_klkl_id', $this->jkul_klkl_id);
        $stmt->bindValue('jkul_kelas', $this->jkul_kelas);
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
                {$this->skema}DEL_APRESIASI_KRSLAMA (
                    :mhs_nim,
                    :jkul_klkl_id,
                    :jkul_kelas
                );

            END;
        SQL;

        $stmt = DB::getPdo()->prepare($sql);
        $stmt->bindValue('mhs_nim', $this->mhs_nim);
        $stmt->bindValue('jkul_klkl_id', $this->jkul_klkl_id);
        $stmt->bindValue('jkul_kelas', $this->jkul_kelas);
        $stmt->execute();

        $this->exists = false;
    }
}
