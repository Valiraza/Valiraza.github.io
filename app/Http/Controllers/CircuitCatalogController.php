<?php

namespace App\Http\Controllers;

use App\Models\Circuit;

class CircuitCatalogController extends Controller
{
    private const TYPE_CONFIG = [
        'randonnee-trek' => [
            'type' => 'Randonnee & Trek',
            'title' => 'Randonnée',
            'hero_title' => 'Randonnée',
            'hero_subtitle' => "Découvrez nos randonnées exclusives à travers l'île Rouge.",
            'heading' => 'Randonnées & voyages à pied',
            'description' => "Parcourir Madagascar à pied est l'unique façon de s'imprégner de l'âme de l'île Rouge. De la terre ocre des Hautes Terres aux sanctuaires de pierre des Tsingy, nous avons dessiné des itinéraires où chaque pas raconte une histoire.",
        ],
        'classe-verte' => [
            'type' => 'Classe Verte',
            'title' => 'Classe Verte',
            'hero_title' => 'Classe Verte',
            'hero_subtitle' => 'Découvrez nos circuits éducatifs et immersifs à travers Madagascar.',
            'heading' => 'Classe Verte',
            'description' => 'Des programmes pensés pour apprendre autrement, sur le terrain, au plus près de la biodiversité, des paysages et des communautés malgaches.',
        ],
        'team-building' => [
            'type' => 'Team Building',
            'title' => 'Team Building',
            'hero_title' => 'Team Building',
            'hero_subtitle' => "Découvrez nos expériences de cohésion et d'aventure sur l'île Rouge.",
            'heading' => "Team Building & cohésion d'équipe",
            'description' => "Nous concevons des expériences collectives qui renforcent l'esprit d'équipe, la confiance et la collaboration dans des cadres naturels mémorables.",
        ],
        'circuit-sejour' => [
            'type' => 'Circuit Sejour',
            'title' => 'Circuit Séjour',
            'hero_title' => 'Circuit Séjour',
            'hero_subtitle' => 'Découvrez nos séjours exclusifs à travers les plus belles régions de Madagascar.',
            'heading' => 'Circuits Séjour',
            'description' => 'Des voyages axés sur le rythme, le confort et la découverte, pour profiter pleinement de Madagascar avec une approche souple et inspirée.',
        ],
    ];

    public function show(string $slug)
    {
        abort_unless(isset(self::TYPE_CONFIG[$slug]), 404);

        $config = self::TYPE_CONFIG[$slug];

        $circuits = Circuit::whereIn('statut', ['Publie', 'Actif'])
            ->where('type', $config['type'])
            ->latest()
            ->get();

        return view('circuits.index', [
            'catalog' => $config,
            'catalogSlug' => $slug,
            'catalogTypes' => self::TYPE_CONFIG,
            'circuits' => $circuits,
        ]);
    }
}
