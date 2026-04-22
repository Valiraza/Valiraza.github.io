<?php

namespace App\Http\Controllers;

use App\Mail\ReservationAccepted;
use App\Models\Circuit;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function index()
    {
        try {
            $query = Reservation::query()->latest();

            if ($this->supportsCircuitColumns()) {
                $query->with('circuit:id,nom,slug,destination');
            }

            $reservations = $query->get();

            return response()->json($reservations);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la recuperation des reservations',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'nom' => 'required|string|min:2|max:255',
                'email' => 'required|email|max:255',
                'telephone' => 'required|string|min:5|max:20',
                'voyageurs' => 'required|string|max:255',
                'places' => 'nullable|string|max:500',
            ];

            if ($this->supportsCircuitColumns()) {
                $rules['circuit_id'] = 'nullable|exists:circuits,id';
                $rules['circuit_nom'] = 'nullable|string|max:255';
            }

            if ($this->supportsStatusColumn()) {
                $rules['statut'] = 'nullable|string|in:' . implode(',', [
                    Reservation::STATUS_PENDING,
                    Reservation::STATUS_ACCEPTED,
                    Reservation::STATUS_REFUSED,
                ]);
            }

            $validated = $request->validate($rules, [
                'telephone.min' => 'Le telephone doit contenir au moins 5 chiffres',
            ]);

            if ($this->supportsCircuitColumns() && !empty($validated['circuit_id'])) {
                $circuit = Circuit::find($validated['circuit_id']);
                $validated['circuit_nom'] = $circuit?->nom ?? ($validated['circuit_nom'] ?? null);
            }

            if ($this->supportsStatusColumn()) {
                $validated['statut'] = $validated['statut'] ?? Reservation::STATUS_PENDING;
            }

            $requestedSeats = $this->parsePlaces($validated['places'] ?? null);

            if (
                $this->supportsCircuitColumns()
                && !empty($validated['circuit_id'])
                && !empty($requestedSeats)
                && (!$this->supportsStatusColumn() || ($validated['statut'] ?? Reservation::STATUS_PENDING) === Reservation::STATUS_ACCEPTED)
            ) {
                $occupiedSeats = $this->occupiedSeatsForCircuit((int) $validated['circuit_id']);
                $duplicates = array_values(array_intersect($requestedSeats, $occupiedSeats));

                if (!empty($duplicates)) {
                    throw ValidationException::withMessages([
                        'places' => ['Les places suivantes sont deja reservees : ' . implode(', ', $duplicates)],
                    ]);
                }
            }

            if (!$this->supportsCircuitColumns()) {
                unset($validated['circuit_id'], $validated['circuit_nom']);
            }

            if (!$this->supportsStatusColumn()) {
                unset($validated['statut']);
            }

            $reservation = Reservation::create($validated);

            return response()->json([
                'message' => 'Reservation creee avec succes',
                'reservation_id' => $reservation->id,
                'data' => $this->supportsCircuitColumns()
                    ? $reservation->load('circuit:id,nom,slug,destination')
                    : $reservation,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Reservation error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Erreur lors de la creation de la reservation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Reservation $reservation)
    {
        return response()->json(
            $this->supportsCircuitColumns()
                ? $reservation->load('circuit:id,nom,slug,destination')
                : $reservation
        );
    }

    public function update(Request $request, Reservation $reservation)
    {
        try {
            \Log::info('Reservation update called', [
                'reservation_id' => $reservation->id,
                'request_data' => $request->all(),
                'supports_status' => $this->supportsStatusColumn()
            ]);

            if (!$this->supportsStatusColumn()) {
                return response()->json([
                    'message' => 'La mise a jour du statut n est pas disponible. Veuillez executer la migration de la colonne statut.',
                ], 422);
            }

            $previousStatus = $reservation->statut;
            $rules = [];

            if ($this->supportsStatusColumn()) {
                $rules['statut'] = 'required|string|in:' . implode(',', [
                    Reservation::STATUS_PENDING,
                    Reservation::STATUS_ACCEPTED,
                    Reservation::STATUS_REFUSED,
                ]);
            }

            if (empty($rules)) {
                return response()->json([
                    'message' => 'Aucune mise a jour disponible.',
                ]);
            }

            $validated = $request->validate($rules);

            $reservation->update($validated);

            $reservation = $reservation->fresh();

            if (
                $this->supportsStatusColumn()
                && $previousStatus !== Reservation::STATUS_ACCEPTED
                && $reservation->statut === Reservation::STATUS_ACCEPTED
            ) {
                Mail::to($reservation->email)->send(
                    new ReservationAccepted(
                        $this->supportsCircuitColumns()
                            ? $reservation->load('circuit:id,nom,slug,destination')
                            : $reservation
                    )
                );
            }

            return response()->json([
                'message' => 'Reservation mise a jour avec succes',
                'data' => $this->supportsCircuitColumns()
                    ? $reservation->load('circuit:id,nom,slug,destination')
                    : $reservation,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Reservation update error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Erreur lors de la mise a jour de la reservation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function occupied(Circuit $circuit)
    {
        return response()->json([
            'circuit_id' => $circuit->id,
            'occupied_seats' => $this->supportsCircuitColumns()
                ? $this->occupiedSeatsForCircuit($circuit->id)
                : [],
        ]);
    }

    public function destroy(Reservation $reservation)
    {
        try {
            $reservation->delete();

            return response()->json([
                'message' => 'Reservation supprimee avec succes',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la suppression',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    private function occupiedSeatsForCircuit(int $circuitId, ?int $ignoreReservationId = null): array
    {
        $query = Reservation::query()->where('circuit_id', $circuitId);

        if ($this->supportsStatusColumn()) {
            $query->where('statut', Reservation::STATUS_ACCEPTED);
        }

        if ($ignoreReservationId) {
            $query->whereKeyNot($ignoreReservationId);
        }

        return $query
            ->pluck('places')
            ->flatMap(fn ($places) => $this->parsePlaces($places))
            ->map(fn ($seat) => (int) $seat)
            ->filter(fn ($seat) => $seat > 0)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function supportsCircuitColumns(): bool
    {
        return Schema::hasColumns('reservations', ['circuit_id', 'circuit_nom']);
    }

    private function supportsStatusColumn(): bool
    {
        return Schema::hasColumn('reservations', 'statut');
    }

    private function parsePlaces(?string $places): array
    {
        if (!$places) {
            return [];
        }

        return collect(explode(',', $places))
            ->map(fn ($place) => trim($place))
            ->filter(fn ($place) => $place !== '' && is_numeric($place))
            ->map(fn ($place) => (int) $place)
            ->unique()
            ->values()
            ->all();
    }
}
