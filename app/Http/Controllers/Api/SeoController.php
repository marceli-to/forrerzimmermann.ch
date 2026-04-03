<?php

namespace App\Http\Controllers\Api;

use App\Actions\Seo\UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seo\UpdateSeoSettingRequest;
use App\Http\Resources\SeoSettingResource;
use App\Models\SeoSetting;

class SeoController extends Controller
{
    public function show()
    {
        return new SeoSettingResource(SeoSetting::with('media')->firstOrFail());
    }

    public function update(UpdateSeoSettingRequest $request)
    {
        $seo = SeoSetting::firstOrFail();
        $seo = (new UpdateAction)->execute($seo, $request->validated());
        return new SeoSettingResource($seo->load('media'));
    }
}
