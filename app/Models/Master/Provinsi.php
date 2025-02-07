<?php

namespace App\Models\Master;

use App\Models\Master\Kota;
use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    use Model;

    protected $Fillable = [
        'name'
    ];

    public function kota()
    {
        $this->hasMany(Kota::class, 'provinsi_id', 'id');
    }
}
