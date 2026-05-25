@extends('layouts.app')

@section('content')
{{-- Flatpickr CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<div class="container py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3 gap-2">
        <div>
            <p class="text-uppercase small text-muted mb-1">Relatórios</p>
            <h1 class="h4 mb-0">Quantidade de participação e avaliação por encontro</h1>
        </div>
    </div>


    {{-- Abas --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <ul class="nav nav-tabs" role="tablist" style="flex: 1;">
            <li class="nav-item" role="presentation">
                <a class="nav-link @if($tab === 'momento') active @endif" href="{{ route('relatorio-quantitativo.index') }}?{{ http_build_query(array_merge(request()->query(), ['tab' => 'momento'])) }}" role="tab">
                    Relatório por Momento
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link @if($tab === 'total-geral') active @endif" href="{{ route('relatorio-quantitativo.index') }}?{{ http_build_query(array_merge(request()->query(), ['tab' => 'total-geral'])) }}" role="tab">
                    Total Geral de Participantes
                </a>
            </li>
        </ul>

        {{-- Botões de Exportação --}}
        <div class="ms-3 d-flex gap-2">
            @if($tab === 'momento')
                <a href="{{ route('relatorio-quantitativo.exportar-momento') }}?{{ http_build_query(array_merge(request()->query(), ['formato' => 'pdf'])) }}" class="btn btn-sm btn-outline-danger" title="Exportar como PDF">
                    <i class="bi bi-filetype-pdf"></i> PDF
                </a>
                <a href="{{ route('relatorio-quantitativo.exportar-momento') }}?{{ http_build_query(array_merge(request()->query(), ['formato' => 'xlsx'])) }}" class="btn btn-sm btn-outline-success" title="Exportar como Excel">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Excel
                </a>
            @else
                <a href="{{ route('relatorio-quantitativo.exportar-total-geral') }}?{{ http_build_query(array_merge(request()->query(), ['formato' => 'pdf'])) }}" class="btn btn-sm btn-outline-danger" title="Exportar como PDF">
                    <i class="bi bi-filetype-pdf"></i> PDF
                </a>
                <a href="{{ route('relatorio-quantitativo.exportar-total-geral') }}?{{ http_build_query(array_merge(request()->query(), ['formato' => 'xlsx'])) }}" class="btn btn-sm btn-outline-success" title="Exportar como Excel">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Excel
                </a>
            @endif
        </div>
    </div>

    {{-- Tabela Momento --}}
    @if($tab === 'momento')
    {{-- Filtros Aba Momento --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('relatorio-quantitativo.index') }}" class="row g-2 align-items-end">

                <div class="col-md-3 col-lg-2">
                    <label class="form-label mb-1 small fw-semibold">Ação</label>
                    <select name="evento_id" id="filter-evento-momento" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($eventos as $id => $nome)
                            <option value="{{ $id }}" @selected(request('evento_id') == $id)>{{ $nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-lg-2">
                    <label class="form-label mb-1 small fw-semibold">Região</label>
                    <select name="regiao_id" id="filter-regiao-momento" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($regioes as $regiao)
                            <option value="{{ $regiao->id }}" @selected(request('regiao_id') == $regiao->id)>{{ $regiao->nome }}</option>
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
                    <select name="municipio_id" id="filter-municipio-momento" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($municipios as $municipio)
                            <option value="{{ $municipio->id }}" @selected(request('municipio_id') == $municipio->id)>
                                {{ $municipio->nome_com_estado }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-lg-2">
                    <label class="form-label mb-1 small fw-semibold">Intervalo</label>
                    <input type="text" id="filter-daterange-momento" class="form-control form-control-sm" placeholder="De ... até">
                    <input type="hidden" name="de" id="filter-de-momento">
                    <input type="hidden" name="ate" id="filter-ate-momento">
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

                <input type="hidden" name="tab" value="momento">
                <input type="hidden" name="sort" value="{{ request('sort', 'dia') }}">
                <input type="hidden" name="dir"  value="{{ request('dir', 'asc') }}">

                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-sm text-white" style="background-color:#421944;">Filtrar</button>
                    <a href="{{ route('relatorio-quantitativo.index') }}?tab=momento" class="btn btn-outline-secondary btn-sm">Limpar</a>
                </div>
            </form>
        </div>
    </div>

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
    @endif

    {{-- Tabela Total Geral --}}
    @if($tab === 'total-geral')
    {{-- Filtros Aba Total Geral --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('relatorio-quantitativo.index') }}" class="row g-2 align-items-end">

                <div class="col-md-3 col-lg-2">
                    <label class="form-label mb-1 small fw-semibold">Ação</label>
                    <select name="evento_id" id="filter-evento-total" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($eventos as $id => $nome)
                            <option value="{{ $id }}" @selected(request('evento_id') == $id)>{{ $nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-lg-2">
                    <label class="form-label mb-1 small fw-semibold">Região</label>
                    <select name="regiao_id" id="filter-regiao-total" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($regioes as $regiao)
                            <option value="{{ $regiao->id }}" @selected(request('regiao_id') == $regiao->id)>{{ $regiao->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-lg-2">
                    <label class="form-label mb-1 small fw-semibold">Município</label>
                    <select name="municipio_id" id="filter-municipio-total" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($municipios as $municipio)
                            <option value="{{ $municipio->id }}" @selected(request('municipio_id') == $municipio->id)>
                                {{ $municipio->nome_com_estado }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-lg-2">
                    <label class="form-label mb-1 small fw-semibold">Intervalo</label>
                    <input type="text" id="filter-daterange-total" class="form-control form-control-sm" placeholder="De ... até">
                    <input type="hidden" name="de" id="filter-de-total">
                    <input type="hidden" name="ate" id="filter-ate-total">
                </div>

                <input type="hidden" name="tab" value="total-geral">
                <input type="hidden" name="sort" value="{{ request('sort', 'regiao') }}">
                <input type="hidden" name="dir"  value="{{ request('dir', 'asc') }}">

                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-sm text-white" style="background-color:#421944;">Filtrar</button>
                    <a href="{{ route('relatorio-quantitativo.index') }}?tab=total-geral" class="btn btn-outline-secondary btn-sm">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            @php
                function tg_sort_link(string $label, string $key): string {
                    $curr   = request('sort', 'regiao');
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

            @if($totalGeral->filter(fn($r) => !isset($r['_is_total']))->isEmpty())
                <div class="p-4 text-center text-muted">Nenhum dado encontrado com os filtros aplicados.</div>
            @else
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{!! tg_sort_link('Região', 'regiao') !!}</th>
                            <th>{!! tg_sort_link('Município', 'municipio') !!}</th>
                            <th class="text-end">{!! tg_sort_link('Previstos', 'previstos') !!}</th>
                            <th class="text-end">{!! tg_sort_link('Com CPF', 'com_cpf') !!}</th>
                            <th class="text-end">{!! tg_sort_link('Sem CPF', 'sem_cpf') !!}</th>
                            <th class="text-end">{!! tg_sort_link('% Com CPF', 'pct_cpf') !!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($totalGeral as $row)
                            @if(isset($row['_is_total']) || isset($row['_is_unidentified']))
                                @if(isset($row['_is_unidentified']))
                                <tr style="background-color:#f8f5f0;">
                                    <td colspan="2">{{ $row['municipio_nome'] }}</td>
                                    <td class="text-end">{{ $row['previstos'] ?: '—' }}</td>
                                    <td class="text-end">{{ $row['metricas']['cpf']['com'] }}</td>
                                    <td class="text-end">{{ $row['metricas']['cpf']['sem'] }}</td>
                                    <td class="text-end">{{ ($row['metricas']['cpf']['com'] + $row['metricas']['cpf']['sem']) > 0 ? number_format($row['metricas']['cpf']['pct'], 2, ',', '.') . '%' : '—' }}</td>
                                </tr>
                                @else
                                <tr style="background-color:#e8daea; font-weight:700;">
                                    <td colspan="2" class="text-end pe-3">{{ $row['municipio_nome'] }}</td>
                                    <td class="text-end">{{ $row['previstos'] ?: '—' }}</td>
                                    <td class="text-end">{{ $row['metricas']['cpf']['com'] }}</td>
                                    <td class="text-end">{{ $row['metricas']['cpf']['sem'] }}</td>
                                    <td class="text-end">{{ ($row['metricas']['cpf']['com'] + $row['metricas']['cpf']['sem']) > 0 ? number_format($row['metricas']['cpf']['pct'], 2, ',', '.') . '%' : '—' }}</td>
                                </tr>
                                @endif
                            @else
                            <tr>
                                <td>{{ $row['regiao'] }}</td>
                                <td>{{ $row['municipio_nome'] }}</td>
                                <td class="text-end">{{ $row['previstos'] ?: '—' }}</td>
                                <td class="text-end">{{ $row['metricas']['cpf']['com'] }}</td>
                                <td class="text-end">{{ $row['metricas']['cpf']['sem'] }}</td>
                                <td class="text-end">{{ ($row['metricas']['cpf']['com'] + $row['metricas']['cpf']['sem']) > 0 ? $row['metricas']['cpf']['pct'] . '%' : '—' }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>

<script>
(function () {
    // Cascata de filtros apenas para a aba Momento
    var eventoSelect    = document.getElementById('filter-evento-momento');
    var momentoSelect   = document.getElementById('filter-momento');
    var municipioSelect = document.getElementById('filter-municipio-momento');

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

{{-- Mutual exclusivity para aba Momento --}}
<script>
(function () {
    var municipioSelect = document.getElementById('filter-municipio-momento');
    var regiaoSelect = document.getElementById('filter-regiao-momento');

    if (!municipioSelect || !regiaoSelect) return;

    // Desmarcar região quando município for selecionado (aba Momento)
    municipioSelect.addEventListener('change', function () {
        if (this.value) {
            regiaoSelect.value = '';
        }
    });

    // Desmarcar município quando região for selecionada (aba Momento)
    regiaoSelect.addEventListener('change', function () {
        if (this.value) {
            municipioSelect.value = '';
        }
    });
})();
</script>

{{-- Script para desmarcar região/município na aba Total Geral --}}
<script>
(function () {
    var municipioSelectTotal = document.getElementById('filter-municipio-total');
    var regiaoSelectTotal = document.getElementById('filter-regiao-total');

    if (!municipioSelectTotal || !regiaoSelectTotal) return;

    // Desmarcar região quando município for selecionado (aba Total Geral)
    municipioSelectTotal.addEventListener('change', function () {
        if (this.value) {
            regiaoSelectTotal.value = '';
        }
    });

    // Desmarcar município quando região for selecionada (aba Total Geral)
    regiaoSelectTotal.addEventListener('change', function () {
        if (this.value) {
            municipioSelectTotal.value = '';
        }
    });
})();
</script>

{{-- Flatpickr JS --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/rangePlugin.js"></script>

<script>
(function () {
    // Formatar data para YYYY-MM-DD
    function formatDate(date) {
        return new Date(date).toISOString().split('T')[0];
    }

    // Função para inicializar Flatpickr para um formulário específico
    function initializeDatePicker(daterangeId, deInputId, ateInputId) {
        var daterangeInput = document.getElementById(daterangeId);
        var deInput = document.getElementById(deInputId);
        var ateInput = document.getElementById(ateInputId);

        if (!daterangeInput) return;

        flatpickr(daterangeInput, {
            mode: 'range',
            dateFormat: 'd/m/Y',
            locale: 'pt',
            placeholder: 'De ... até',
            defaultDate: [
                '{{ request('de') ? request('de') : '' }}',
                '{{ request('ate') ? request('ate') : '' }}'
            ],
            onChange: function (selectedDates) {
                if (selectedDates.length === 2) {
                    deInput.value = formatDate(selectedDates[0]);
                    ateInput.value = formatDate(selectedDates[1]);
                } else if (selectedDates.length === 1) {
                    deInput.value = formatDate(selectedDates[0]);
                    ateInput.value = '';
                }
            }
        });

        // Set initial values if already selected
        if ('{{ request('de') }}') {
            deInput.value = '{{ request('de') }}';
        }
        if ('{{ request('ate') }}') {
            ateInput.value = '{{ request('ate') }}';
        }
    }

    // Initialize date pickers para ambas as abas
    initializeDatePicker('filter-daterange-momento', 'filter-de-momento', 'filter-ate-momento');
    initializeDatePicker('filter-daterange-total', 'filter-de-total', 'filter-ate-total');
})();
</script>

@endsection
