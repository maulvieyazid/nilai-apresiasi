<?php

namespace App\Traits;

use Illuminate\Database\Eloquent;

trait HasModelExtender
{
    protected $skema;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->skema = config('oracle.oracle.prefix_schema') == '' ? '' : config('oracle.oracle.prefix_schema') . ".";
    }

    // Function ini digunakan untuk mendapatkan sekaligus mengeset primary key selanjutnya pada model
    protected function setAutoIncrementKey()
    {
        $keyName = $this->getKeyName();

        // Mengambil Id selanjutnya, berdasarkan class yang memanggil
        // $class = (new \ReflectionClass($this))->getShortName();
        $class = '\\' . get_class($this);
        $id = $class::max($keyName);
        (int) $id++;

        $this->setAttribute($keyName, $id);
    }


    /* INI FUNCTION SEMENTARA, SEBELUM CRUD NYA DIJADIKAN PROCEDURE
       SAAT SUDAH MENJADI PROCEDURE, FUNCTION INI BISA DIHAPUS / DICOMMENT
    */

    // Function ini di override agar bisa men generate primary key tabel yang auto increment secara otomatis
    // Sehingga di Controller tidak perlu untuk mengecek primary key nya lagi
    // Syaratnya untuk menggunakan function ini adalah mengeset attribut incrementing menjadi true pada model
    // public $incrementing = true;
    /* protected function insertAndSetId(Eloquent\Builder $query, $attributes)
    {
        $this->setAutoIncrementKey();

        $query->insert($this->getAttributes());
    }

    // Function ini di override agar fungsi delete / update bisa bekerja
    protected function setKeysForSaveQuery(Eloquent\Builder $query)
    {
        // Kalau Primary Key nya gk ada, maka gunakan semua key sebagai where
        if (!$this->getKeyName()) {
            foreach ($this->getAttributes() as $key => $value) {
                $query->where($key, '=', $value);
            }
        }
        // Kalau ada primary key nya, maka gunakan primary key nya
        else {
            $query->where($this->getKeyName(), '=', $this->getKeyForSaveQuery());
        }

        return $query;
    } */
}
