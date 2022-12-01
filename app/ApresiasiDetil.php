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
                    :nilai
                );

            END;
        SQL;

        $stmt = DB::getPdo()->prepare($sql);
        $stmt->bindValue('id_apresiasi', $this->id_apresiasi);
        $stmt->bindValue('klkl_id', $this->klkl_id);
        $stmt->bindValue('nilai', $this->nilai);
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
