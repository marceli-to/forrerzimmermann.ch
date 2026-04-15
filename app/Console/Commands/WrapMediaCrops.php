<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;

class WrapMediaCrops extends Command
{
    protected $signature = 'app:wrap-media-crops';
    protected $description = 'Wrap existing flat crop data into {desktop: {...}} structure';

    public function handle(): void
    {
        $media = Media::whereNotNull('crop')->get();

        $count = 0;
        foreach ($media as $item) {
            $crop = $item->crop;

            // Skip if already in new format
            if (isset($crop['desktop'])) {
                continue;
            }

            // Wrap flat {x,y,w,h} into {desktop: {x,y,w,h}}
            if (isset($crop['x'], $crop['y'], $crop['w'], $crop['h'])) {
                $item->update([
                    'crop' => ['desktop' => $crop],
                ]);
                $count++;
            }
        }

        $this->info("Wrapped {$count} crop(s) into desktop format.");
    }
}
