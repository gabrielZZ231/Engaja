<div>
    <div wire:loading class="text-center p-4">
        Carregando dados...
    </div>
    <div wire:loading.remove>
        @if (empty($dados))
            <div class="alert alert-warning text-center my-4">Nenhum dado disponível para este gráfico.</div>
        @else
            <div class="card-grafico-bi" data-chart="ranking-municipios"
                data-dados='@json($dados, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE)'
                data-titulo="{{ e($titulo) }}" data-tipo-valor="{{ e($tipoValor) }}">
            </div>
        @endif
    </div>
</div>
