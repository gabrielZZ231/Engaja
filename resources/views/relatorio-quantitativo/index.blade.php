@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3 gap-2">
        <div>
            <p class="text-uppercase small text-muted mb-1">Relatórios</p>
            <h1 class="h4 mb-0">Quantidade de participação e avaliação por encontro</h1>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('relatorio-quantitativo.index') }}" class="row g-2 align-items-end">

                <div class="col-md-3 col-lg-2">
                    <label class="form-label mb-1 small fw-semibold">Ação</label>
                    <select name="evento_id" id="filter-evento" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($eventos as $id => $nome)
                            <option value="{{ $id }}" @selected(request('evento_id') == $id)>{{ $nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-lg-2">
                    <label class="form-label mb-1 small fw-semibold">Momento</label>
                    <select name="descricao" id="filter-momento" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($momentos as $m)
                            <option value="{{ $m }}" @selected(request('descricao') == $m)>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-lg-2">
                    <label class="form-label mb-1 small fw-semibold">Município</label>
                    <select name="municipio_id" id="filter-municipio" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($municipios as $municipio)
                            <option value="{{ $municipio->id }}" @selected(request('municipio_id') == $municipio->id)>
                                {{ $municipio->nome_com_estado }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2 col-lg-1">
                    <label class="form-label mb-1 small fw-semibold">De</label>
                    <input type="date" name="de" value="{{ request('de') }}" class="form-control form-control-sm">
                </div>

                <div class="col-6 col-md-2 col-lg-1">
                    <label class="form-label mb-1 small fw-semibold">Até</label>
                    <input type="date" name="ate" value="{{ request('ate') }}" class="form-control form-control-sm">
                </div>

                <div class="col-md-2 col-lg-2">
                    <label class="form-label mb-1 small fw-semibold">Período</label>
                    <select name="periodo" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="manha" @selected(request('periodo') == 'manha')>Manhã</option>
                        <option value="tarde" @selected(request('periodo') == 'tarde')>Tarde</option>
                        <option value="noite" @selected(request('periodo') == 'noite')>Noite</option>
                    </select>
                </div>

                <input type="hidden" name="sort" value="{{ request('sort', 'dia') }}">
                <input type="hidden" name="dir"  value="{{ request('dir', 'asc') }}">

                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-sm text-white" style="background-color:#421944;">Filtrar</button>
                    <a href="{{ route('relatorio-quantitativo.index') }}" class="btn btn-outline-secondary btn-sm">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabela --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @php
                function rq_sort_link(string $label, string $key): string {
                    $curr   = request('sort', 'dia');
                    $curDir = request('dir', 'asc') === 'asc' ? 'asc' : 'desc';
                    $next   = ($curr === $key && $curDir === 'asc') ? 'desc' : 'asc';
                    $params = array_merge(request()->except('page'), ['sort' => $key, 'dir' => $next]);
                    $url    = request()->url() . '?' . http_build_query($params);
                    $arrow  = ($curr === $key) ? ($curDir === 'asc' ? ' ↑' : ' ↓') : '';
                    return '<a href="' . e($url) . '" class="text-decoration-none text-dark">'
                         . e($label)
                         . '<span class="text-muted small">' . $arrow . '</span></a>';
                }
            @endphp

            @if($atividades->isEmpty())
                <div class="p-4 text-center text-muted">Nenhum encontro encontrado com os filtros aplicados.</div>
            @else
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{!! rq_sort_link('Ação', 'acao') !!}</th>
                            <th>{!! rq_sort_link('Momento', 'momento') !!}</th>
                            <th>{!! rq_sort_link('Município', 'municipio') !!}</th>
                            <th>{!! rq_sort_link('Data', 'dia') !!}</th>
                            <th>{!! rq_sort_link('Período', 'periodo') !!}</th>
                            <th class="text-end">{!! rq_sort_link('Qtd Previstas', 'previstas') !!}</th>
                            <th class="text-end">{!! rq_sort_link('Qtd Presentes', 'presentes') !!}</th>
                            <th class="text-end">Presentes / Previstas</th>
                            <th class="text-end">{!! rq_sort_link('Qtd Avaliações', 'avaliacoes') !!}</th>
                            <th class="text-end">Avaliações / Presentes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($atividades->groupBy('evento_nome') as $nomeAcao => $grupo)

                            @foreach($grupo as $a)
                            @php
                                $horaStr = substr($a->hora_inicio ?? '', 0, 5);
                                $hora    = (int) substr($horaStr, 0, 2);
                                $periodoLabel = $hora < 12 ? 'Manhã' : ($hora < 18 ? 'Tarde' : 'Noite');

                                $previstas  = (int) $a->publico_esperado;
                                $presentes  = (int) $a->presentes_count;
                                $avaliacoes = (int) $a->avaliacoes_count;

                                $propPres = $previstas > 0 ? round($presentes  / $previstas  * 100, 1) : 0;
                                $propAval = $presentes > 0 ? round($avaliacoes / $presentes  * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td>{{ $a->evento_nome ?? '—' }}</td>
                                <td>{{ $a->descricao ?? '—' }}</td>
                                <td>{{ $a->municipio_nome ?? '—' }}</td>
                                <td>{{ $a->dia ? \Carbon\Carbon::parse($a->dia)->format('d/m/Y') : '—' }}</td>
                                <td>{{ $horaStr ? $periodoLabel . ' (' . $horaStr . ')' : '—' }}</td>
                                <td class="text-end">{{ $previstas ?: '—' }}</td>
                                <td class="text-end">{{ $presentes }}</td>
                                <td class="text-end">{{ $previstas > 0 ? $propPres . '%' : '—' }}</td>
                                <td class="text-end">{{ $avaliacoes }}</td>
                                <td class="text-end">{{ $presentes > 0 ? $propAval . '%' : '—' }}</td>
                            </tr>
                            @endforeach

                            @php
                                $totalPrevistas  = $grupo->sum('publico_esperado');
                                $totalPresentes  = $grupo->sum('presentes_count');
                                $totalAvaliacoes = $grupo->sum('avaliacoes_count');
                                $propTotPres = $totalPrevistas > 0
                                    ? round($totalPresentes  / $totalPrevistas  * 100, 1) : 0;
                                $propTotAval = $totalPresentes > 0
                                    ? round($totalAvaliacoes / $totalPresentes  * 100, 1) : 0;
                            @endphp
                            <tr style="background-color:#e8daea; font-weight:700;">
                                <td colspan="5" class="text-end pe-3">
                                    Subtotal — {{ $nomeAcao ?? 'Sem ação' }}
                                </td>
                                <td class="text-end">{{ $totalPrevistas ?: '—' }}</td>
                                <td class="text-end">{{ $totalPresentes }}</td>
                                <td class="text-end">{{ $totalPrevistas > 0 ? $propTotPres . '%' : '—' }}</td>
                                <td class="text-end">{{ $totalAvaliacoes }}</td>
                                <td class="text-end">{{ $totalPresentes > 0 ? $propTotAval . '%' : '—' }}</td>
                            </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

</div>

<script>
(function () {
    var eventoSelect    = document.getElementById('filter-evento');
    var momentoSelect   = document.getElementById('filter-momento');
    var municipioSelect = document.getElementById('filter-municipio');

    if (!eventoSelect) return;

    var endpointBase      = '{{ route('relatorio-quantitativo.momentos') }}';
    var selectedMomento   = '{{ addslashes(request('descricao', '')) }}';
    var selectedMunicipio = '{{ request('municipio_id', '') }}';

    function rebuildStringSelect(selectEl, items, currentVal) {
        var first = selectEl.options[0];
        selectEl.innerHTML = '';
        selectEl.appendChild(first);
        items.forEach(function (item) {
            var opt = document.createElement('option');
            opt.value = item;
            opt.text  = item;
            if (item === currentVal) opt.selected = true;
            selectEl.appendChild(opt);
        });
    }

    function rebuildObjectSelect(selectEl, items, currentVal) {
        var first = selectEl.options[0];
        selectEl.innerHTML = '';
        selectEl.appendChild(first);
        items.forEach(function (item) {
            var opt = document.createElement('option');
            opt.value = item.id;
            opt.text  = item.nome;
            if (String(item.id) === String(currentVal)) opt.selected = true;
            selectEl.appendChild(opt);
        });
    }

    eventoSelect.addEventListener('change', function () {
        var eventoId = this.value;
        var url      = endpointBase + (eventoId ? '?evento_id=' + encodeURIComponent(eventoId) : '');

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                rebuildStringSelect(momentoSelect,   data.momentos,   selectedMomento);
                rebuildObjectSelect(municipioSelect, data.municipios, selectedMunicipio);
            })
            .catch(function (err) {
                console.error('Erro ao carregar filtros:', err);
            });
    });
})();
</script>
@endsection
