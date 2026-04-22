<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\AdminReply;
use App\Models\Client;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'category' => 'nullable|string|in:Info,Devis,Reclamation',
            'message' => 'required|string',
        ]);

        $contact = Contact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'] ?? 'Message contact',
            'category' => $validated['category'] ?? 'Info',
            'status' => 'non_lu',
            'admin_replies' => [],
            'message' => $validated['message'],
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $contact], 201);
        }

        return redirect()->back()->with('success', 'Merci, votre message a ete envoye !');
    }

    public function index()
    {
        $contacts = Contact::latest()->get();

        return view('compteadmin', compact('contacts'));
    }

    public function apiIndex()
    {
        return response()->json(Contact::latest()->get());
    }

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'category' => 'nullable|string|in:Info,Devis,Reclamation',
            'message' => 'required|string',
        ]);

        $contact = Contact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'] ?? 'Message contact',
            'category' => $validated['category'] ?? 'Info',
            'status' => 'non_lu',
            'admin_replies' => [],
            'message' => $validated['message'],
        ]);

        return response()->json(['success' => true, 'data' => $contact], 201);
    }

    public function apiClients()
    {
        try {
            $clients = Client::select('clients.*')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('reservations')
                    ->whereColumn('reservations.email', 'clients.email');
            })
            ->addSelect(DB::raw('(SELECT MAX(r.created_at) FROM reservations r WHERE r.email = clients.email) as reservation_date'))
            ->addSelect(DB::raw('(SELECT CASE 
                    WHEN r.statut = \'acceptee\' THEN \'Accepté\'
                    WHEN r.statut = \'refusee\' THEN \'Refusé\'
                    WHEN r.statut = \'en_attente\' THEN \'En attente\'
                    ELSE \'En attente\' END 
                FROM reservations r WHERE r.email = clients.email ORDER BY r.created_at DESC LIMIT 1) as reservation_status'))
            ->orderByDesc(DB::raw('(SELECT MAX(r.created_at) FROM reservations r WHERE r.email = clients.email)'))
            ->get();

            return response()->json($clients);
        } catch (\Exception $e) {
            \Log::error('apiClients error: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    public function apiStoreClient(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'tel' => 'nullable|string|max:50',
            'date' => 'nullable|date',
            'statut' => 'nullable|string|max:80',
            'points' => 'nullable|integer',
            'niveau' => 'nullable|string|max:80',
            'depenses' => 'nullable|numeric',
        ]);

        $client = Client::create($validated);

        return response()->json(['success' => true, 'data' => $client], 201);
    }

    public function apiUpdateClient(Request $request, $id)
    {
        $client = Client::find($id);
        if (! $client) {
            return response()->json(['success' => false, 'message' => 'Client introuvable'], 404);
        }

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'tel' => 'nullable|string|max:50',
            'date' => 'nullable|date',
            'statut' => 'nullable|string|max:80',
            'points' => 'nullable|integer',
            'niveau' => 'nullable|string|max:80',
            'depenses' => 'nullable|numeric',
        ]);

        $client->update($validated);

        return response()->json(['success' => true, 'data' => $client->fresh()]);
    }

    public function apiDeleteClient($id)
    {
        $client = Client::find($id);
        if (! $client) {
            return response()->json(['success' => false, 'message' => 'Client introuvable'], 404);
        }

        $client->delete();

        return response()->json(['success' => true]);
    }

    public function apiReplyToContact(Request $request, $id)
    {
        $contact = Contact::find($id);
        if (! $contact) {
            return response()->json(['success' => false, 'message' => 'Contact introuvable'], 404);
        }

        $validated = $request->validate([
            'reply' => 'required|string',
        ]);

        Mail::to($contact->email)->send(new AdminReply($contact, $validated['reply']));

        $replies = $contact->admin_replies ?? [];
        $replies[] = [
            'message' => $validated['reply'],
            'sent_at' => now()->toDateTimeString(),
        ];

        $contact->update([
            'admin_replies' => $replies,
            'status' => 'repondu',
            'replied_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reponse envoyee avec succes',
            'data' => $contact->fresh(),
        ]);
    }

    public function apiUpdateContactStatus(Request $request, $id)
    {
        $contact = Contact::find($id);
        if (! $contact) {
            return response()->json(['success' => false, 'message' => 'Contact introuvable'], 404);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:non_lu,lu,repondu',
        ]);

        $contact->update([
            'status' => $validated['status'],
        ]);

        return response()->json(['success' => true, 'data' => $contact->fresh()]);
    }

    public function apiDeleteContact($id)
    {
        $contact = Contact::find($id);
        if (! $contact) {
            return response()->json(['success' => false, 'message' => 'Contact introuvable'], 404);
        }

        $contact->delete();

        return response()->json(['success' => true]);
    }
}

