<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LanguageableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Languageable $languageable */
        $languageable = $this->resource;

        return [
            'id' => $languageable->id,
            'menu' => $languageable->menu,
            'dubs' => $languageable->dubs,
            'subtitles' => $languageable->subtitles,
            'language' => LanguageResource::make($this->whenLoaded('language')),
        ];
    }
}
