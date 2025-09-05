<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class SintaMPeringkat extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'sinta_m_peringkat';

    protected $fillable = [
        'nama_universitas',
        'sinta_score_3_yr',
        'sinta_score_overall',
    ];

    protected $casts = [
        'sinta_score_3_yr' => 'integer',
        'sinta_score_overall' => 'integer',
    ];
}
