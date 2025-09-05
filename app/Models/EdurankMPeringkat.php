<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EdurankMPeringkat extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'edurank_m_peringkat';
    protected $fillable = [
        'nama_universitas',
        'peringkat_asia',
        'peringkat_dunia',
    ];
    protected $casts = [
        'peringkat_asia' => 'integer',
        'peringkat_dunia' => 'integer',
    ];
}
