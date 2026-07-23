<?php

namespace App\Support;

use App\Models\Aliment;
use App\Models\MiseBas;
use App\Models\Saillie;
use App\Models\SanteIntervention;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * Requêtes partagées pour les alertes du tableau de bord et le badge
 * de notifications de la topbar, afin d'éviter de dupliquer la logique.
 */
class AlertesElevage
{
    public static function diagnosticsAFaire(): Collection
    {
        return Saillie::where('diagnostic_gestation', 'en_attente')
            ->where('date_saillie', '<=', Carbon::today()->subDays(Saillie::JOURS_AVANT_DIAGNOSTIC))
            ->with(['femelle', 'male'])
            ->orderBy('date_saillie')
            ->get();
    }

    public static function misesBasImminentes(): Collection
    {
        return Saillie::where('diagnostic_gestation', 'positif')
            ->whereDoesntHave('miseBas')
            ->where('date_mise_bas_prevue', '<=', Carbon::today()->addDays(5))
            ->with(['femelle', 'male'])
            ->orderBy('date_mise_bas_prevue')
            ->get();
    }

    public static function sevragesImminents(): Collection
    {
        return MiseBas::whereDoesntHave('sevrage')
            ->where('date_sevrage_prevue', '<=', Carbon::today()->addDays(5))
            ->with('femelle')
            ->orderBy('date_sevrage_prevue')
            ->get();
    }

    public static function stocksBas(): Collection
    {
        return Aliment::whereNotNull('seuil_alerte')
            ->whereColumn('stock_actuel', '<=', 'seuil_alerte')
            ->get();
    }

    public static function suivisSanteEnCours(): Collection
    {
        return SanteIntervention::where('statut', 'en_cours')
            ->with('lapin')
            ->orderBy('date_debut')
            ->get();
    }

    public static function total(): int
    {
        return self::diagnosticsAFaire()->count()
            + self::misesBasImminentes()->count()
            + self::sevragesImminents()->count()
            + self::stocksBas()->count();
    }
}
