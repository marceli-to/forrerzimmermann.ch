<?php

namespace App\Http\Controllers\Api;

use App\Actions\Team\DeleteAction;
use App\Actions\Team\ReorderAction;
use App\Actions\Team\StoreAction;
use App\Actions\Team\UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Resources\TeamMemberResource;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamController extends Controller
{
	public function index()
	{
		return TeamMemberResource::collection(
			TeamMember::with('media')->orderBy('sort_order')->get()
		);
	}

	public function store(StoreTeamRequest $request)
	{
		$member = (new StoreAction)->execute($request->validated());
		return new TeamMemberResource($member->load('media'));
	}

	public function show(TeamMember $member)
	{
		return new TeamMemberResource($member->load('media'));
	}

	public function update(UpdateTeamRequest $request, TeamMember $member)
	{
		$member = (new UpdateAction)->execute($member, $request->validated());
		return new TeamMemberResource($member->load('media'));
	}

	public function toggle(TeamMember $member)
	{
		$member->update(['publish' => !$member->publish]);
		return new TeamMemberResource($member);
	}

	public function destroy(TeamMember $member)
	{
		(new DeleteAction)->execute($member);
		return response()->json(null, 204);
	}

	public function reorder(Request $request)
	{
		(new ReorderAction)->execute($request->items);
		return response()->json(['message' => 'ok']);
	}
}
