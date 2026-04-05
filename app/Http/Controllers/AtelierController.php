<?php

namespace App\Http\Controllers;

class AtelierController extends Controller
{
	public function profile()
	{
		return view('pages.atelier.profile');
	}

	public function team()
	{
		return view('pages.atelier.team');
	}

	public function jobs()
	{
		return view('pages.atelier.jobs');
	}
}
