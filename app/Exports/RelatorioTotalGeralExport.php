<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\RelatorioQuantitativoController;
use App\Models\Evento;
use App\Models\Regiao;
use App\Models\Municipio;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class RelatorioTotalGeralExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    public function __construct(private Request $request)
    {
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $filtros = $this->getFiltersSummary();
                $row = 1;

                if (count($filtros) > 0) {
                    $sheet->setCellValue('A' . $row, 'Filtros Aplicados:');
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                    $row++;

                    foreach ($filtros as $filtro) {
                        $sheet->setCellValue('A' . $row, $filtro);
                        $row++;
                    }

                    // Adicionar linha em branco
                    $row++;

                    // Inserir 3 linhas vazias antes dos dados
                    $sheet->insertNewRowBefore($row, 3);
                }
            }
        ];
    }

    private function getFiltersSummary(): array
    {
        $filtros = [];

        if ($this->request->integer('evento_id')) {
            $evento = Evento::find($this->request->integer('evento_id'));
            if ($evento) $filtros[] = "Ação: " . $evento->nome;
        }

        if ($this->request->integer('regiao_id')) {
            $regiao = Regiao::find($this->request->integer('regiao_id'));
            if ($regiao) $filtros[] = "Região: " . $regiao->nome;
        }

        if ($this->request->integer('municipio_id')) {
            $municipio = Municipio::find($this->request->integer('municipio_id'));
            if ($municipio) $filtros[] = "Município: " . $municipio->nome;
        }

        if ($this->request->get('de') || $this->request->get('ate')) {
            $de = $this->request->get('de') ? \Carbon\Carbon::parse($this->request->get('de'))->format('d/m/Y') : '';
            $ate = $this->request->get('ate') ? \Carbon\Carbon::parse($this->request->get('ate'))->format('d/m/Y') : '';
            $intervalo = ($de && $ate) ? "$de até $ate" : ($de ? "a partir de $de" : "até $ate");
            $filtros[] = "Período: " . $intervalo;
        }

        return $filtros;
    }

    public function collection(): Collection
    {
        $controller = new RelatorioQuantitativoController();
        $totalGeral = $this->callBuildTotalGeralData($controller);

        return $totalGeral->filter(fn ($r) => !isset($r['_is_total']));
    }

    public function headings(): array
    {
        return [
            'Região',
            'Município',
            'Previstos',
            'Com CPF',
            'Sem CPF',
            '% Com CPF',
        ];
    }

    public function map($row): array
    {
        $totalCpf = $row['metricas']['cpf']['com'] + $row['metricas']['cpf']['sem'];
        $pctCpf = $totalCpf > 0 ? number_format($row['metricas']['cpf']['pct'], 2, ',', '.') . '%' : '—';

        return [
            $row['regiao'] ?? '—',
            $row['municipio_nome'] ?? '—',
            $row['previstos'] ?: '—',
            $row['metricas']['cpf']['com'],
            $row['metricas']['cpf']['sem'],
            $pctCpf,
        ];
    }

    private function callBuildTotalGeralData($controller): Collection
    {
        $reflection = new \ReflectionMethod(RelatorioQuantitativoController::class, 'buildTotalGeralData');
        $reflection->setAccessible(true);

        return $reflection->invoke($controller, $this->request);
    }
}
