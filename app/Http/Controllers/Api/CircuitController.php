<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Circuit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CircuitController extends Controller
{
    public function index()
    {
        $query = Circuit::query()->latest();

        if ($this->shouldReturnOnlyPublished(request())) {
            $query->whereIn('statut', ['Publie', 'Actif']);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        try {
            $parsedData = $this->parseNestedFormData($request);
            $validated = validator($parsedData, $this->rules(false))->validate();

            $validated['statut'] = $validated['statut'] ?? 'Brouillon';
            $validated['programme'] = $this->normalizeProgramme($validated['programme'] ?? [], $request);
            $validated['detail'] = $this->normalizeDetail($validated['detail'] ?? [], $request);
            $validated['image'] = $this->storeImage($request);
            $validated['type'] = $validated['type'] ?? 'Circuit Sejour';

            $circuit = Circuit::create($validated);
            return response()->json($circuit, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Circuit store error: ' . $e->getMessage(), ['input' => $request->all(), 'files' => $request->file()]);
            return response()->json(['message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function show(Circuit $circuit)
    {
        return response()->json($circuit);
    }

    public function showBySlug($slug)
    {
        $circuit = Circuit::where('slug', $slug)->firstOrFail();
        return response()->json($circuit);
    }

    public function update(Request $request, Circuit $circuit)
    {
        try {
            $parsedData = $this->parseNestedFormData($request);
            $validated = validator($parsedData, $this->rules(true))->validate();

            if (array_key_exists('programme', $validated) || $request->hasFile('programme')) {
                $validated['programme'] = $this->normalizeProgramme($validated['programme'] ?? [], $request, $circuit->programme ?? []);
            }

            if (array_key_exists('detail', $validated)) {
                $validated['detail'] = $this->normalizeDetail($validated['detail'] ?? [], $request, $circuit->detail ?? []);
            } elseif ($request->hasFile('detail_image_itineraire')) {
                $validated['detail'] = $this->normalizeDetail($circuit->detail ?? [], $request, $circuit->detail ?? []);
            }

            if ($request->hasFile('image')) {
                $this->deleteImage($circuit->image);
                $validated['image'] = $this->storeImage($request);
            }

            $circuit->update($validated);
            return response()->json($circuit);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Circuit update error: ' . $e->getMessage(), ['input' => $request->all(), 'files' => $request->file()]);
            return response()->json(['message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Circuit $circuit)
    {
        $this->deleteImage($circuit->image);
        $this->deleteProgrammeImages($circuit->programme ?? []);
        $this->deleteImage(data_get($circuit->detail, 'image_itineraire'));
        $circuit->delete();
        return response()->json(['message' => 'Circuit deleted']);
    }

    private function rules(bool $isUpdate): array
    {
        $prefix = $isUpdate ? 'sometimes|' : '';

        return [
            'nom' => $prefix . 'required|string',
            'slug' => 'nullable|string|max:255',
            'destination' => $prefix . 'required|string',
            'depart' => $prefix . 'required|date',
            'retour' => $prefix . 'required|date|after:depart',
            'prix' => $prefix . 'required|numeric|min:0',
            'places' => $prefix . 'required|integer|min:1',
            'description' => 'nullable|string',
            'programme' => 'nullable|array',
            'programme.*.jour' => 'nullable|integer|min:1',
            'programme.*.titre' => 'nullable|string',
            'programme.*.destination' => 'nullable|string',
            'programme.*.hebergement' => 'nullable|string',
            'programme.*.repas' => 'nullable|string',
            'programme.*.activites' => 'nullable|string',
            'programme.*.transport' => 'nullable|string',
            'programme.*.notes' => 'nullable|string',
            'programme.*.image_path' => 'nullable|string',
            'programme.*.image' => 'nullable|image|max:5120',
            'detail' => 'nullable|array',
            'detail.breadcrumb' => 'nullable|string',
            'detail.titre' => 'nullable|string',
            'detail.sous_titre' => 'nullable|string',
            'detail.code' => 'nullable|string',
            'detail.badge' => 'nullable|string',
            'detail.note' => 'nullable|string',
            'detail.nombre_avis' => 'nullable|string',
            'detail.description' => 'nullable|string',
            'detail.image_itineraire' => 'nullable|string',
            'detail.image_itineraire_legende' => 'nullable|string',
            'detail_image_itineraire' => 'nullable|image|max:5120',
            'detail.prix_label' => 'nullable|string',
            'detail.prix_details' => 'nullable|string',
            'detail.bouton_prix' => 'nullable|string',
            'detail.privatisation_titre' => 'nullable|string',
            'detail.privatisation_description' => 'nullable|string',
            'detail.privatisation_bouton' => 'nullable|string',
            'detail.points_forts' => 'nullable|array',
            'detail.points_forts.*' => 'nullable|string',
            'detail.specifications' => 'nullable|array',
            'detail.specifications.duree' => 'nullable|string',
            'detail.specifications.niveau' => 'nullable|string',
            'detail.specifications.activite' => 'nullable|string',
            'detail.specifications.groupe' => 'nullable|string',
            'detail.prix_inclus' => 'nullable|array',
            'detail.prix_inclus.*' => 'nullable|string',
            'detail.prix_exclus' => 'nullable|array',
            'detail.prix_exclus.*' => 'nullable|string',
            'detail.fiche_technique' => 'nullable|array',
            'detail.fiche_technique.*.cle' => 'nullable|string',
            'detail.fiche_technique.*.titre' => 'nullable|string',
            'detail.fiche_technique.*.description' => 'nullable|string',
            'detail.fiche_technique.*.elements' => 'nullable|array',
            'detail.fiche_technique.*.elements.*' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'type' => $prefix . 'required|string|in:' . implode(',', Circuit::TYPES),
            'statut' => ($isUpdate ? 'sometimes|' : 'nullable|') . 'string|in:Brouillon,Publie,Actif,Inactif',
        ];
    }

    private function normalizeProgramme(array $programme, Request $request, array $existingProgramme = []): array
    {
        $normalized = collect($programme)
            ->map(function ($item, $index) use ($request, $existingProgramme) {
                $existingImage = data_get($existingProgramme, "{$index}.image");
                $imagePath = $item['image_path'] ?? $existingImage;

                if ($request->hasFile("programme.{$index}.image")) {
                    $this->deleteImage($existingImage);
                    $imagePath = $request->file("programme.{$index}.image")->store('circuits/programme', 'public');
                }

                return [
                    'jour' => $item['jour'] ?? null,
                    'titre' => $item['titre'] ?? '',
                    'destination' => $item['destination'] ?? '',
                    'hebergement' => $item['hebergement'] ?? '',
                    'repas' => $item['repas'] ?? '',
                    'activites' => $item['activites'] ?? '',
                    'transport' => $item['transport'] ?? '',
                    'notes' => $item['notes'] ?? '',
                    'image' => $imagePath,
                ];
            })
            ->filter(function ($item) {
                return filled($item['jour'])
                    || filled($item['titre'])
                    || filled($item['destination'])
                    || filled($item['hebergement'])
                    || filled($item['repas'])
                    || filled($item['activites'])
                    || filled($item['transport'])
                    || filled($item['notes'])
                    || filled($item['image']);
            })
            ->values()
            ->all();

        $keptImages = collect($normalized)->pluck('image')->filter()->all();

        collect($existingProgramme)
            ->pluck('image')
            ->filter()
            ->reject(fn ($path) => in_array($path, $keptImages, true))
            ->each(fn ($path) => $this->deleteImage($path));

        return $normalized;
    }

    private function normalizeDetail(array $detail, Request $request, array $existingDetail = []): array
    {
        $imageItineraire = $detail['image_itineraire'] ?? ($existingDetail['image_itineraire'] ?? '');

        if ($request->hasFile('detail_image_itineraire')) {
            $this->deleteImage($existingDetail['image_itineraire'] ?? null);
            $imageItineraire = $request->file('detail_image_itineraire')->store('circuits/details', 'public');
        }

        return [
            'breadcrumb' => $detail['breadcrumb'] ?? '',
            'titre' => $detail['titre'] ?? '',
            'sous_titre' => $detail['sous_titre'] ?? '',
            'code' => $detail['code'] ?? '',
            'badge' => $detail['badge'] ?? '',
            'note' => $detail['note'] ?? '',
            'nombre_avis' => $detail['nombre_avis'] ?? '',
            'description' => $detail['description'] ?? '',
            'image_itineraire' => $imageItineraire,
            'image_itineraire_legende' => $detail['image_itineraire_legende'] ?? '',
            'prix_label' => $detail['prix_label'] ?? '',
            'prix_details' => $detail['prix_details'] ?? '',
            'bouton_prix' => $detail['bouton_prix'] ?? '',
            'privatisation_titre' => $detail['privatisation_titre'] ?? '',
            'privatisation_description' => $detail['privatisation_description'] ?? '',
            'privatisation_bouton' => $detail['privatisation_bouton'] ?? '',
            'points_forts' => array_values(array_filter($detail['points_forts'] ?? [], fn ($value) => filled($value))),
            'specifications' => [
                'duree' => data_get($detail, 'specifications.duree', ''),
                'niveau' => data_get($detail, 'specifications.niveau', ''),
                'activite' => data_get($detail, 'specifications.activite', ''),
                'groupe' => data_get($detail, 'specifications.groupe', ''),
            ],
            'prix_inclus' => array_values(array_filter($detail['prix_inclus'] ?? [], fn ($value) => filled($value))),
            'prix_exclus' => array_values(array_filter($detail['prix_exclus'] ?? [], fn ($value) => filled($value))),
            'fiche_technique' => collect($detail['fiche_technique'] ?? [])
                ->map(function ($item) {
                    return [
                        'cle' => $item['cle'] ?? '',
                        'titre' => $item['titre'] ?? '',
                        'description' => $item['description'] ?? '',
                        'elements' => array_values(array_filter($item['elements'] ?? [], fn ($value) => filled($value))),
                    ];
                })
                ->filter(fn ($item) => filled($item['cle']) || filled($item['titre']) || filled($item['description']) || !empty($item['elements']))
                ->values()
                ->all(),
        ];
    }

    private function shouldReturnOnlyPublished(Request $request): bool
    {
        return filter_var($request->query('published', false), FILTER_VALIDATE_BOOLEAN);
    }

    private function storeImage(Request $request): ?string
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('circuits', 'public');
    }

    private function deleteImage(?string $path): void
    {
        if (!$path || str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function deleteProgrammeImages(array $programme): void
    {
        collect($programme)
            ->pluck('image')
            ->filter()
            ->each(fn ($path) => $this->deleteImage($path));
    }

    /**
     * Parse nested FormData into PHP arrays for deep validation
     */
    private function parseNestedFormData(Request $request): array
    {
        $data = $request->all();
        $files = $request->allFiles();

        // Parse nested arrays from FormData (e.g. programme[0][jour] => value)
        foreach ($data as $key => $value) {
            if (str_contains($key, '[') && str_contains($key, ']')) {
                $this->setNestedValue($data, $key, $value);
                unset($data[$key]);
            }
        }

        // Handle nested files (programme[0][image])
        foreach ($files as $key => $file) {
            if (str_contains($key, '[') && str_contains($key, ']')) {
                data_set($data, $key, $file);
            }
        }

        // Ensure arrays for programme and detail
        $data['programme'] = $data['programme'] ?? [];
        if (!is_array($data['programme'])) {
            $data['programme'] = [];
        }

        $data['detail'] = $data['detail'] ?? [];
        if (!is_array($data['detail'])) {
            $data['detail'] = [];
        }

        // Parse deep nested fields
        $data['detail']['points_forts'] = $data['detail']['points_forts'] ?? [];
        $data['detail']['prix_inclus'] = $data['detail']['prix_inclus'] ?? [];
        $data['detail']['prix_exclus'] = $data['detail']['prix_exclus'] ?? [];
        $data['detail']['fiche_technique'] = $data['detail']['fiche_technique'] ?? [];
        $data['detail']['specifications'] = $data['detail']['specifications'] ?? [];

        return $data;
    }

    private function setNestedValue(array &$array, string $key, $value): void
    {
        $keys = $this->explodeKeys($key);
        $target = &$array;

        foreach ($keys as $innerKey) {
            if (!isset($target[$innerKey]) || !is_array($target[$innerKey])) {
                $target[$innerKey] = [];
            }
            $target = &$target[$innerKey];
        }

        $target = $value;
    }

    private function explodeKeys(string $key): array
    {
        preg_match_all('/(?:\[([^\]]+)\])/m', $key, $matches);
        return array_map(fn($m) => $m ?: '', $matches[1]);
    }
}

