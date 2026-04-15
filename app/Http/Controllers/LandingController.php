<?php

namespace App\Http\Controllers;

use App\Models\LandingSlide;

class LandingController extends Controller
{
	public function __invoke()
	{
		$slides = LandingSlide::published()
			->with(['media', 'desktopMedia', 'mobileMedia'])
			->orderBy('sort_order')
			->get();

		return view('pages.landing', compact('slides'));
	}
}
