<?php

namespace App\Http\Controllers;

use App\Models\Pokemons;
use App\Services\PokemonSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class PokemonsController extends Controller
{
    /**
     * Exibe a listagem de pokémons para o front-end Inertia.
     */
    public function index(): Response
    {
        $pokemons = Pokemons::query()
            ->orderBy('nome')
            ->where('ativo', 1)
            ->paginate(10, [
                'id',
                'external_id',
                'nome',
                'tipo',
                'altura',
                'peso',
                'foto',
                'ativo',
                'created_at',
            ])
            ->withQueryString();

        $pokemons->getCollection()->transform(function (Pokemons $pokemon) {
            return [
                'id' => $pokemon->id,
                'external_id' => $pokemon->external_id,
                'nome' => $pokemon->nome,
                'tipo' => $pokemon->tipo,
                'altura' => $pokemon->altura,
                'peso' => $pokemon->peso,
                'foto' => $pokemon->foto,
                'ativo' => (bool) $pokemon->ativo,
                'created_at' => $pokemon->created_at,
            ];
        });

        return Inertia::render('Pokemons/Index', [
            'pokemons' => $pokemons,
        ]);
    }

    /**
     * Exibe detalhes de um pokémon específico.
     */
    public function show(Pokemons $pokemon): Response
    {
        return Inertia::render('Pokemons/Show', [
            'pokemon' => [
                'id' => $pokemon->id,
                'external_id' => $pokemon->external_id,
                'nome' => $pokemon->nome,
                'tipo' => $pokemon->tipo,
                'altura' => $pokemon->altura,
                'peso' => $pokemon->peso,
                'foto' => $pokemon->foto,
                'ativo' => (bool) $pokemon->ativo,
                'created_at' => $pokemon->created_at,
                'updated_at' => $pokemon->updated_at,
            ],
        ]);
    }

    /**
     * Formulário de edição.
     */
    public function edit(Pokemons $pokemon): Response
    {
        return Inertia::render('Pokemons/Edit', [
            'pokemon' => [
                'id' => $pokemon->id,
                'external_id' => $pokemon->external_id,
                'nome' => $pokemon->nome,
                'tipo' => $pokemon->tipo,
                'altura' => $pokemon->altura,
                'peso' => $pokemon->peso,
                'foto' => $pokemon->foto,
                'ativo' => (bool) $pokemon->ativo,
            ],
        ]);
    }

    /**
     * Atualiza os dados do pokémon.
     */
    public function update(Request $request, Pokemons $pokemon): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'tipo' => ['nullable', 'string', 'max:255'],
            'altura' => ['nullable', 'integer', 'min:0'],
            'peso' => ['nullable', 'numeric', 'min:0'],
            'foto' => ['nullable', 'string', 'max:255'],
            'ativo' => ['required', 'boolean'],
        ]);

        $pokemon->update([
            'nome' => $validated['nome'],
            'tipo' => $validated['tipo'] ?? null,
            'altura' => $validated['altura'],
            'peso' => $validated['peso'],
            'foto' => $validated['foto'] ?? null,
            'ativo' => $validated['ativo'],
        ]);

        return redirect()
            ->route('pokemons.show', $pokemon)
            ->with('success', 'Pokémon atualizado com sucesso!');
    }

    /**
     * Remove um pokémon do sistema.
     */
    public function destroy(Pokemons $pokemon): RedirectResponse
    {
        $pokemon->update(['ativo' => 0]);

        return redirect()
            ->route('pokemons.index')
            ->with('success', 'Pokémon marcado como inativo!');
    }

    /**
     * Sincroniza os pokémons a partir da PokeAPI.
     */
    public function sync(Request $request): JsonResponse|RedirectResponse
    {
        $baseUrl = 'https://pokeapi.co/api/v2/pokemon?limit=10';
        $sourceUrl = $request->input('next_url') ?: $baseUrl;
        $response = Http::get($sourceUrl);

        if ($response->failed()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Não foi possível sincronizar os pokémons no momento.',
                ], 422);
            }

            return redirect()
                ->route('pokemons.index')
                ->with('error', 'Não foi possível sincronizar os pokémons no momento.');
        }

        $payload = $response->json();
        $results = $payload['results'] ?? [];
        $synced = 0;

        foreach ($results as $result) {
            if (empty($result['url'])) {
                continue;
            }

            $detailResponse = Http::get($result['url']);

            if ($detailResponse->failed()) {
                continue;
            }

            $detail = $detailResponse->json();

            if (PokemonSyncService::syncFromDetail($detail, $result)) {
                $synced++;
            }
        }

        $next = $payload['next'] ?? null;

        if ($request->expectsJson()) {
            return response()->json([
                'synced' => $synced,
                'count' => $payload['count'] ?? null,
                'next_url' => $next,
                'results' => $results,
            ]);
        }

        $message = $next
            ? "Sincronização inicial concluída ({$synced} pokémons). Continue a importação pela interface."
            : "Sincronização concluída: {$synced} pokémons importados.";

        return redirect()
            ->route('pokemons.index')
            ->with('success', $message)
            ->with('sync', [
                'count' => $payload['count'] ?? 0,
                'next' => $next,
                'results' => $results,
            ]);
    }
}
