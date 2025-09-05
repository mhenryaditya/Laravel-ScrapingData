<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UIGMRMetriks extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'uigm_r_metriks';

    public function metriksOwner()
    {
        return $this->hasMany(UIGMMPeringkat::class, 'id_metriks', 'id');
    }
}
