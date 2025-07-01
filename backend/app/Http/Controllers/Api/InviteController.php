<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    public function index()
    {
        try {
            $invites = Invite::all();
            return response()->json($invites);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $invite = Invite::find($id);
        if (!$invite) {
            return response()->json(['message' => 'Invitation non trouvée'], 404);
        }
        return response()->json($invite);
    }

    public function store(Request $request)
    {
        $invite = Invite::create($request->all());
        return response()->json($invite, 201);
    }

    public function update(Request $request, $id)
    {
        $invite = Invite::findOrFail($id);
        $invite->update($request->all());
        return response()->json($invite);
    }

    public function destroy($id)
    {
        Invite::destroy($id);
        return response()->json(null, 204);
    }
}
