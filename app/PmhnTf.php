<?php

namespace App;

use App\Traits\HasModelExtender;
use Illuminate\Database\Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PmhnTf extends Model
{
    use HasModelExtender;

    const DEFAULT_JNS_PMHN = '5';

    protected $table = 'V_PMHN';

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
                {$this->skema}INS_PMHN_TF (
                    :semester,
                    :jns_pmhn,
                    :klkl_id,
                    :mhs_nim,
                    TO_DATE(:tanggal, 'YYYY-MM-DD HH24:MI:SS')
                );

            END;
        SQL;

        $stmt = DB::getPdo()->prepare($sql);
        $stmt->bindValue('semester', $this->semester);
        $stmt->bindValue('jns_pmhn', self::DEFAULT_JNS_PMHN);
        $stmt->bindValue('klkl_id', $this->klkl_id);
        $stmt->bindValue('mhs_nim', $this->mhs_nim);
        $stmt->bindValue('tanggal', $this->tanggal->format('Y-m-d H:i:s'));
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
                {$this->skema}DEL_PMHN_TF (
                    :semester,
                    :jns_pmhn,
                    :klkl_id,
                    :mhs_nim
                );

            END;
        SQL;

        $stmt = DB::getPdo()->prepare($sql);
        $stmt->bindValue('semester', $this->semester);
        $stmt->bindValue('jns_pmhn', self::DEFAULT_JNS_PMHN);
        $stmt->bindValue('klkl_id', $this->klkl_id);
        $stmt->bindValue('mhs_nim', $this->mhs_nim);
        $stmt->execute();

        $this->exists = false;
    }
}
