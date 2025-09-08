<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UIGMMPeringkat extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'uigm_m_peringkat';

    protected $fillable = [
        'nama_universitas',
        'id_metriks',
        'skor',
        'peringkat_dunia',
    ];

    protected $casts = [
        'skor' => 'integer',
        'peringkat_dunia' => 'integer',
    ];

    public function metriks()
    {
        return $this->belongsTo(UIGMRMetriks::class, 'id_metriks', 'id');
    }
}
