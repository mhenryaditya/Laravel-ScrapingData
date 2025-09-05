<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UIGMRMetriksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_universitas' => $this->nama_universitas,
            'id_metriks' => $this->id_metriks,
            'skor' => $this->skor,
            'peringkat_dunia' => $this->peringkat_dunia,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deteled_at
        ];
    }
}