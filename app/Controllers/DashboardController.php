<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index()
    {
        // Vérifier que l'opérateur est connecté
        $idOperateur = session()->get('operateur_id');
        if (!$idOperateur) {
            return redirect()->to('/operateur/auth')->with('error', 'Veuillez vous connecter.');
        }

        $db = \Config\Database::connect();
        $operateurNom = session()->get('operateur_nom');

        // 1. Nombre total d'utilisateurs pour cet opérateur
        $totalUsers = $db->query(
            "SELECT COUNT(*) as total FROM utilisateur WHERE id_operateur = ?", [$idOperateur]
        )->getRow()->total;

        // 2. Nombre de transactions aujourd'hui
        $today = date('Y-m-d');
        $todayOps = $db->query("
            SELECT COUNT(*) as total FROM (
                SELECT date_depot as date_op FROM depot d
                JOIN utilisateur u ON d.id_utilisateur_depot = u.id_utilisateur
                WHERE u.id_operateur = ? AND d.date_depot LIKE '$today%'
                UNION ALL
                SELECT date_retrait FROM retrait r
                JOIN utilisateur u ON r.id_utilisateur_retrait = u.id_utilisateur
                WHERE u.id_operateur = ? AND r.date_retrait LIKE '$today%'
                UNION ALL
                SELECT date_transfert FROM transfert t
                JOIN utilisateur u ON t.envoyeur_transfert = u.id_utilisateur
                WHERE u.id_operateur = ? AND t.date_transfert LIKE '$today%'
            ) ops
        ", [$idOperateur, $idOperateur, $idOperateur])->getRow()->total;

        // 3. Total des gains (tous types confondus)
        $totalGainsArr = $db->query("
            SELECT COALESCE(SUM(montant_gain), 0) as total FROM gain g
            JOIN retrait r ON g.id_retrait = r.id_retrait
            JOIN utilisateur u ON r.id_utilisateur_retrait = u.id_utilisateur
            WHERE u.id_operateur = ?
            UNION ALL
            SELECT COALESCE(SUM(montant_gain), 0) FROM gain g
            JOIN transfert t ON g.id_transfert = t.id_transfert
            JOIN utilisateur u ON t.envoyeur_transfert = u.id_utilisateur
            WHERE u.id_operateur = ?", [$idOperateur, $idOperateur]
        )->getResultArray();
        $totalGains = array_sum(array_column($totalGainsArr, 'total'));

        // 4. Solde total des utilisateurs
        $totalSolde = $db->query(
            "SELECT COALESCE(SUM(solde_utilisateur), 0) as total FROM utilisateur WHERE id_operateur = ?", [$idOperateur]
        )->getRow()->total;

        // 5. Transactions par type (pour les graphiques)
        $depotsCount = $db->query(
            "SELECT COUNT(*) as total FROM depot d
             JOIN utilisateur u ON d.id_utilisateur_depot = u.id_utilisateur
             WHERE u.id_operateur = ?", [$idOperateur]
        )->getRow()->total;

        $retraitsCount = $db->query(
            "SELECT COUNT(*) as total FROM retrait r
             JOIN utilisateur u ON r.id_utilisateur_retrait = u.id_utilisateur
             WHERE u.id_operateur = ?", [$idOperateur]
        )->getRow()->total;

        $transfertsCount = $db->query(
            "SELECT COUNT(*) as total FROM transfert t
             JOIN utilisateur u ON t.envoyeur_transfert = u.id_utilisateur
             WHERE u.id_operateur = ?", [$idOperateur]
        )->getRow()->total;

        // 6. Évolution des gains sur les 7 derniers jours
        $gains7j = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $rows = $db->query("
                SELECT COALESCE(SUM(montant_gain), 0) as total FROM gain g
                JOIN retrait r ON g.id_retrait = r.id_retrait
                JOIN utilisateur u ON r.id_utilisateur_retrait = u.id_utilisateur
                WHERE u.id_operateur = ? AND g.date_gain LIKE '$date%'
                UNION ALL
                SELECT COALESCE(SUM(montant_gain), 0) FROM gain g
                JOIN transfert t ON g.id_transfert = t.id_transfert
                JOIN utilisateur u ON t.envoyeur_transfert = u.id_utilisateur
                WHERE u.id_operateur = ? AND g.date_gain LIKE '$date%'
            ", [$idOperateur, $idOperateur])->getResultArray();
            $gain = array_sum(array_column($rows, 'total'));
            $gains7j[] = [
                'date' => date('d/m', strtotime($date)),
                'montant' => (float)$gain
            ];
        }

        // 7. Dernières opérations récentes
        $recentOps = $db->query("
            (SELECT 'Dépôt' as type, d.montant_depot as montant, d.date_depot as date, u.nom_utilisateur as nom
             FROM depot d JOIN utilisateur u ON d.id_utilisateur_depot = u.id_utilisateur
             WHERE u.id_operateur = ?
             ORDER BY d.date_depot DESC LIMIT 5)
            UNION ALL
            (SELECT 'Retrait' as type, r.montant_retrait as montant, r.date_retrait as date, u.nom_utilisateur as nom
             FROM retrait r JOIN utilisateur u ON r.id_utilisateur_retrait = u.id_utilisateur
             WHERE u.id_operateur = ?
             ORDER BY r.date_retrait DESC LIMIT 5)
            UNION ALL
            (SELECT 'Transfert' as type, t.montant_transfert as montant, t.date_transfert as date, 
             (SELECT nom_utilisateur FROM utilisateur WHERE id_utilisateur = t.envoyeur_transfert) as nom
             FROM transfert t JOIN utilisateur u ON t.envoyeur_transfert = u.id_utilisateur
             WHERE u.id_operateur = ?
             ORDER BY t.date_transfert DESC LIMIT 5)
            ORDER BY date DESC LIMIT 10
        ", [$idOperateur, $idOperateur, $idOperateur])->getResultArray();

        $data = [
            'operateur_nom'  => $operateurNom,
            'totalUsers'     => $totalUsers,
            'todayOps'       => $todayOps,
            'totalGains'     => $totalGains,
            'totalSolde'     => $totalSolde,
            'depotsCount'    => $depotsCount,
            'retraitsCount'  => $retraitsCount,
            'transfertsCount'=> $transfertsCount,
            'gains7j'        => $gains7j,
            'recentOps'      => $recentOps,
        ];

        return view('operateur/dashboard/index', $data);
    }
}
