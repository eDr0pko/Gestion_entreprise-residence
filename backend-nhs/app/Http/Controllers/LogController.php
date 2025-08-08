<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\Personne;

class LogController extends Controller
{
    public function index(Request $request)
    {
        // Jointure pour récupérer l'email de l'utilisateur
        $logs = Log::leftJoin('personne', 'logs.user_id', '=', 'personne.id_personne')
            ->select(
                'logs.id',
                'logs.action',
                'logs.message',
                'logs.ip_address',
                'logs.created_at',
                'personne.email as user_email'
            )
            ->orderBy('logs.created_at', 'desc')
            ->limit(500)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [ 'data' => $logs ]
        ]);
    }
}
