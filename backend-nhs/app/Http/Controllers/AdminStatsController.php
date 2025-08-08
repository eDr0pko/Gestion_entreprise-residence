<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Personne;
use App\Models\Admin;
use App\Models\Gardien;
use App\Models\Resident;
use App\Models\GroupeMessage;
use App\Models\Message;
use App\Models\Ban;
use App\Models\Invite;

class AdminStatsController extends Controller
{
    public function index()
    {
        // Utilisateurs
        $totalUsers = Personne::count();
        $totalAdmins = Admin::count();
        $totalGardiens = Gardien::count();
        $totalResidents = Resident::count();
        $totalInvites = Invite::count();
        $recentUsers = Personne::where('id_personne', '>', DB::raw('(SELECT MAX(id_personne) - 10 FROM personne)'))->get(['id_personne','email','nom','prenom']);

        // Groupes & messages
        $totalGroups = GroupeMessage::count();
        $totalMessages = Message::count();
        $mostActiveGroups = DB::table('message')
            ->select('id_groupe_message', DB::raw('COUNT(*) as count'))
            ->groupBy('id_groupe_message')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
        $mostActiveUsers = DB::table('message')
            ->select('id_auteur', DB::raw('COUNT(*) as count'))
            ->groupBy('id_auteur')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
        $recentMessages = Message::orderByDesc('date_envoi')->limit(10)->get(['id_message','id_auteur','contenu_message','date_envoi']);

        // Réactions
        $totalReactions = DB::table('message_reaction')->count();
        $topEmojis = DB::table('message_reaction')
            ->select('emoji', DB::raw('COUNT(*) as count'))
            ->groupBy('emoji')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Fichiers
        $totalFiles = DB::table('message_fichier')->count();
        $largestFiles = DB::table('message_fichier')
            ->orderByDesc('taille_fichier')
            ->limit(5)
            ->get(['nom_fichier','taille_fichier','date_upload']);

        // Visites & invitations
        $visitsByStatus = DB::table('visite')
            ->select('statut_visite', DB::raw('COUNT(*) as count'))
            ->groupBy('statut_visite')
            ->get();
        $activeInvites = DB::table('invite')->where('actif', true)->count();
        $inactiveInvites = DB::table('invite')->where('actif', false)->count();

        // Bans
        $totalBans = Ban::count();
        $banMotifs = DB::table('ban')
            ->select('motif', DB::raw('COUNT(*) as count'))
            ->groupBy('motif')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Logs
        $totalLogs = DB::table('logs')->count();
        $topActions = DB::table('logs')
            ->select('action', DB::raw('COUNT(*) as count'))
            ->groupBy('action')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
        $topUsersByLogs = DB::table('logs')
            ->select('user_id', DB::raw('COUNT(*) as count'))
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
        $recentLogs = DB::table('logs')->orderByDesc('created_at')->limit(10)->get();

        return response()->json([
            'users' => [
                'total' => $totalUsers,
                'admins' => $totalAdmins,
                'gardiens' => $totalGardiens,
                'residents' => $totalResidents,
                'invites' => $totalInvites,
                'recent' => $recentUsers,
            ],
            'groups' => [
                'total' => $totalGroups,
                'most_active' => $mostActiveGroups,
            ],
            'messages' => [
                'total' => $totalMessages,
                'most_active_users' => $mostActiveUsers,
                'recent' => $recentMessages,
            ],
            'reactions' => [
                'total' => $totalReactions,
                'top_emojis' => $topEmojis,
            ],
            'files' => [
                'total' => $totalFiles,
                'largest' => $largestFiles,
            ],
            'visits' => [
                'by_status' => $visitsByStatus,
            ],
            'invites' => [
                'active' => $activeInvites,
                'inactive' => $inactiveInvites,
            ],
            'bans' => [
                'total' => $totalBans,
                'motifs' => $banMotifs,
            ],
            'logs' => [
                'total' => $totalLogs,
                'top_actions' => $topActions,
                'top_users' => $topUsersByLogs,
                'recent' => $recentLogs,
            ],
        ]);
    }
}
