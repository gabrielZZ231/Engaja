<?php

namespace App\Repositories;

use App\Models\BiIndicador;
use App\Models\BiValor;
use Closure;
use Illuminate\Support\Facades\Cache;

class BiValorRepository
{
    private const CACHE_TAG = 'bi_valor';
    private const CACHE_VERSION_KEY = 'bi_valor:version';

    public function rankingMunicipios(string $codigoIndicador, int $ano, ?int $dimensaoValorId = null): array
    {
        return $this->setCache('rankingMunicipios', [
            'codigoIndicador' => $codigoIndicador,
            'ano' => $ano,
            'dimensaoValorId' => $dimensaoValorId,
        ], function () use ($codigoIndicador, $ano, $dimensaoValorId) {
            $indicador = BiIndicador::where('codigo', $codigoIndicador)->firstOrFail();

            $query = BiValor::query()
                ->join('municipios', 'municipios.id', '=', 'bi_valores.municipio_id')
                ->where('bi_valores.indicador_id', $indicador->id)
                ->where('bi_valores.ano', $ano)
                ->whereNotNull('bi_valores.valor');

            if ($dimensaoValorId !== null) {
                $query->where('bi_valores.dimensao_valor_id', $dimensaoValorId);
            }

            $dados = $query
                ->orderByDesc('bi_valores.valor')
                ->get([
                    'municipios.nome as municipio',
                    'bi_valores.valor as valor',
                ])
                ->map(fn ($item) => [
                    'municipio' => $item->municipio,
                    'valor' => (float) $item->valor,
                ])
                ->all();

            return [
                'tipo_valor' => $indicador->tipo_valor,
                'dados' => $dados,
            ];
        });
    }

    protected function setCache(string $method, array $params, Closure $callback)
    {
        $paramString = collect($params)
            ->map(fn ($value, $key) => "{$key}:" . ($value ?? 'null'))
            ->implode('|');

        $key = "bi_valor:{$method}:{$paramString}";

        if ($this->supportsCacheTags()) {
            return Cache::tags([self::CACHE_TAG])->rememberForever($key, $callback);
        }

        $version = (int) Cache::get(self::CACHE_VERSION_KEY, 1);
        $versionedKey = "bi_valor:v{$version}:{$method}:{$paramString}";

        return Cache::rememberForever($versionedKey, $callback);
    }

    public function clearCache(): void
    {
        if ($this->supportsCacheTags()) {
            Cache::tags([self::CACHE_TAG])->flush();
            return;
        }

        $currentVersion = (int) Cache::get(self::CACHE_VERSION_KEY, 1);
        Cache::forever(self::CACHE_VERSION_KEY, $currentVersion + 1);
    }

    protected function supportsCacheTags(): bool
    {
        return method_exists(Cache::getStore(), 'tags');
    }
}
