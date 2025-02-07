<?php

/**
 * requirement PHP version 8.3
 */
namespace App\Models\Master;

use App\Models\Master\Provinsi;
use Illuminate\Database\Eloquent\Model;

class Kota extends Model
{

    use Model;

    protected $fillable = [
        'provinsi_id',
        'name'
    ];

    public function provinsi()
    {
        $this->belongsTo(Provinsi::class, 'provinsi_id', 'id');
    }
}
