<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::truncate();

        Client::create([ 'nom' => 'RAKOTO', 'prenom' => 'Andry', 'email' => 'andry@mail.mg', 'tel' => '0340000001', 'date' => '2026-01-15', 'statut' => 'Actif', 'points' => 1250, 'niveau' => 'VIP Gold', 'depenses' => 2400 ]);
        Client::create([ 'nom' => 'MARTIN', 'prenom' => 'Sophie', 'email' => 's.martin@service.fr', 'tel' => '+33612345678', 'date' => '2026-02-10', 'statut' => 'Inactif', 'points' => 450, 'niveau' => 'Standard', 'depenses' => 850 ]);
    }
}
