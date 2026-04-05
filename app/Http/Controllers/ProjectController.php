<?php

namespace App\Http\Controllers;

class ProjectController extends Controller
{
	public function index()
	{
		return view('pages.projects.index');
	}

	public function worklist()
	{
		return view('pages.projects.worklist');
	}

	public function show($slug)
	{
		return view('pages.projects.show');
	}
}
