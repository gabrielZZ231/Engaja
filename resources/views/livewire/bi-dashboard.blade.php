<div class="container-fluid">

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0 text-engaja">
            Dashboard BI
        </h1>

        {{-- Filtro de período (futuro) --}}
        <div>
            <select class="form-select form-select-sm">
                <option value="7">Últimos 7 dias</option>
                <option value="30" selected>Últimos 30 dias</option>
                <option value="90">Últimos 90 dias</option>
            </select>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Pesquisas</div>
                    <div class="fs-3 fw-bold text-engaja">
                        0
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Respostas</div>
                    <div class="fs-3 fw-bold text-engaja">
                        0
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Última resposta</div>
                    <div class="fw-semibold">
                        —
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Gráfico --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h6 mb-3">
                Respostas por dia
            </h2>

            <div id="responsesChart" style="height: 300px;"></div>
        </div>
    </div>

</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:load', () => {
            window.initResponsesChart({
                labels: [],
                series: []
            });
        });
    </script>
@endpush
