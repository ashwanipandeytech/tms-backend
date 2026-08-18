<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PackageResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'destination_id' => $this->destination_id,
            'category_id'    => $this->category_id,
            'nights'         => $this->nights,
            'days'           => $this->days,
            'price'          => (float) $this->price,
            'gst_applicable' => $this->gst_applicable,
            'gst_percent'    => (float) $this->gst_percent,
            'inclusions'     => $this->inclusions,
            'exclusions'     => $this->exclusions,
            'terms'          => $this->terms,
            'status'         => $this->status?->value ?? $this->status,
            'created_at'     => $this->created_at?->toIso8601String(),
        ];
    }
}
