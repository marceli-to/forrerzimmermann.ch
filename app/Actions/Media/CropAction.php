<?php

namespace App\Actions\Media;

use App\Models\Media;

class CropAction
{
    public function execute(Media $media, array $data): Media
    {
        $breakpoint = $data['breakpoint'];

        $allNull = is_null($data['x']) && is_null($data['y'])
            && is_null($data['w']) && is_null($data['h']);

        $crop = $media->crop ?? [];

        if ($allNull) {
            unset($crop[$breakpoint]);
        } else {
            $crop[$breakpoint] = [
                'x' => $data['x'],
                'y' => $data['y'],
                'w' => $data['w'],
                'h' => $data['h'],
            ];
        }

        $media->update([
            'crop' => empty($crop) ? null : $crop,
        ]);

        return $media;
    }
}
