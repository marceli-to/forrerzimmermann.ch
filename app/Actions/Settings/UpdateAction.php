<?php

namespace App\Actions\Settings;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\SiteSetting;

class UpdateAction
{
	public function execute(SiteSetting $settings, array $data): SiteSetting
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$settings->update($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $settings);
		}

		return $settings;
	}
}
