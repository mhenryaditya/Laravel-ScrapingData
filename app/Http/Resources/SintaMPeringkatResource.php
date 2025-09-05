<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SintaMPeringkatResource extends JsonResource
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
            'sinta_score_3_yr' => $this->sinta_score_3_yr,
            'sinta_score_overall' => $this->sinta_score_overall,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deteled_at
        ];
    }
}