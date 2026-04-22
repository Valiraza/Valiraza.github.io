<?php

namespace Database\Seeders;

use App\Models\Reservation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        Reservation::truncate();

        // Matching client emails
        Reservation::create([
            'nom' => 'Rakoto',
            'email' => 'andry@mail.mg',
            'telephone' => '0340000001',
            'voyageurs' => '2 adultes',
            'places' => 2,
            'circuit_id' => 1,
            'circuit_nom' => 'Test Circuit',
            'statut' => 'acceptee',
        ]);

        Reservation::create([
            'nom' => 'Martin',
            'email' => 's.martin@service.fr',
            'telephone' => '+33612345678',
            'voyageurs' => '1 adulte',
            'places' => 1,
            'circuit_id' => 1,
            'circuit_nom' => 'Test Circuit',
            'statut' => 'en_attente',
        ]);
    }
}

