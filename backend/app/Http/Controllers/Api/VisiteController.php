<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use App\Models\Visite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class VisiteController extends Controller
{
    public function index()
    {
        return Visite::all();
    }

    public function updateStatut(Request $request, $id)
    {
        $visite = \App\Models\Visite::findOrFail($id);
        $visite->statut_visite = $request->input('statut_visite');
        $visite->save();

        return response()->json($visite);
    }
}
