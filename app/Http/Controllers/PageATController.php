<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Circuit;

class PageATController extends Controller
{
    public function index()
    {
        $circuits = Circuit::whereIn('statut', ['Publie', 'Actif'])
            ->latest()
            ->get();

        return view('pageAT', compact('circuits'));
    }
}
