<?php

namespace App\Services;

use App\Models\Pokemons;

class PokemonSyncService
{
    /**
     * Persiste o pokémon baseado no retorno da PokeAPI.
     */
    public static function syncFromDetail(array $detail, array $fallback = []): bool
    {
        $externalId = $detail['id'] ?? null;

        if (!$externalId) {
            return false;
        }

        $name = $detail['name'] ?? $fallback['name'] ?? null;

        if (!$name) {
            return false;
        }

        Pokemons::updateOrCreate(
            ['external_id' => $externalId],
            [
                'nome' => ucfirst($name),
                'tipo' => collect($detail['types'] ?? [])
                    ->map(fn ($type) => $type['type']['name'] ?? null)
                    ->filter()
                    ->map(fn ($typeName) => ucfirst($typeName))
                    ->implode(', '),
                'altura' => (int) (($detail['height'] ?? 0) * 10),
                'peso' => ($detail['weight'] ?? 0) / 10,
                'foto' => $detail['sprites']['front_default'] ?? null,
                'ativo' => 1,
            ]
        );

        return true;
    }
}
