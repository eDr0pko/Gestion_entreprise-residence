<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogAdminController extends Controller
{
    public function deleteBefore(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);
        $date = $request->input('date');
        // On supprime tous les logs antérieurs à la date (inclus toute la journée)
        $deleted = DB::table('logs')->where('created_at', '<', $date . ' 00:00:00')->delete();
        return response()->json(['success' => true, 'deleted' => $deleted]);
    }
}
