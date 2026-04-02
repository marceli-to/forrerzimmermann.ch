<?php

namespace App\Http\Controllers\Api;

use App\Actions\Settings\UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateSiteSettingRequest;
use App\Http\Resources\SiteSettingResource;
use App\Models\SiteSetting;

class SettingsController extends Controller
{
	public function show()
	{
		return new SiteSettingResource(SiteSetting::with('media')->firstOrFail());
	}

	public function update(UpdateSiteSettingRequest $request)
	{
		$settings = SiteSetting::firstOrFail();
		$settings = (new UpdateAction)->execute($settings, $request->validated());
		return new SiteSettingResource($settings->load('media'));
	}
}
