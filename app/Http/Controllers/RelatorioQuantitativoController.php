<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\Evento;
use App\Models\Municipio;
use Illuminate\Http\Request;

class RelatorioQuantitativoController extends Controller
{
    public function index(Request $request)
    {
        $eventoId    = $request->integer('evento_id');
        $descricao   = trim((string) $request->get('descricao', ''));
        $municipioId = $request->integer('municipio_id');
        $de          = $request->date('de');
        $ate         = $request->date('ate');
        $periodo     = $request->get('periodo', '');

        $sort = $request->get('sort', 'dia');
        $dir  = $request->get('dir', 'asc') === 'asc' ? 'asc' : 'desc';

        $sortable = [
            'acao'      => 'eventos.nome',
            'momento'   => 'atividades.descricao',
            'municipio' => 'municipios.nome',
            'dia'       => 'atividades.dia',
            'periodo'   => 'atividades.hora_inicio',
            'previstas' => 'atividades.publico_esperado',
            'presentes' => 'presentes_count',
            'avaliacoes' => 'avaliacoes_count',
        ];
        $orderByCol = $sortable[$sort] ?? 'atividades.dia';

        $query = Atividade::query()
            ->select([
                'atividades.id',
                'atividades.evento_id',
                'atividades.municipio_id',
                'atividades.descricao',
                'atividades.dia',
                'atividades.hora_inicio',
                'atividades.hora_fim',
                'atividades.publico_esperado',
                'eventos.nome as evento_nome',
                'municipios.nome as municipio_nome',
            ])
            ->leftJoin('eventos', 'eventos.id', '=', 'atividades.evento_id')
            ->leftJoin('municipios', 'municipios.id', '=', 'atividades.municipio_id')
            ->withCount([
                'presencas as presentes_count' => fn ($q) => $q->where('status', 'presente'),
                'presencas as avaliacoes_count' => fn ($q) => $q->where('status', 'presente')
                    ->where('avaliacao_respondida', true),
            ])
            ->whereNull('atividades.deleted_at')
            ->whereNotNull('atividades.evento_id');

        $query->when($eventoId,    fn ($q) => $q->where('atividades.evento_id',    $eventoId));
        $query->when($municipioId, fn ($q) => $q->where('atividades.municipio_id', $municipioId));
        $query->when($descricao,   fn ($q) => $q->where('atividades.descricao',    $descricao));

        $query->when($de && $ate,  fn ($q) => $q->whereBetween('atividades.dia', [$de, $ate]));
        $query->when($de && ! $ate, fn ($q) => $q->where('atividades.dia', '>=', $de));
        $query->when(! $de && $ate, fn ($q) => $q->where('atividades.dia', '<=', $ate));

        $query->when($periodo === 'manha', fn ($q) =>
            $q->whereRaw("TIME(atividades.hora_inicio) < '12:00:00'"));
        $query->when($periodo === 'tarde', fn ($q) =>
            $q->whereRaw("TIME(atividades.hora_inicio) >= '12:00:00'")
              ->whereRaw("TIME(atividades.hora_inicio) < '18:00:00'"));
        $query->when($periodo === 'noite', fn ($q) =>
            $q->whereRaw("TIME(atividades.hora_inicio) >= '18:00:00'"));

        $query->orderBy($orderByCol, $dir)
              ->orderBy('eventos.nome', 'asc')
              ->orderBy('atividades.dia', 'asc')
              ->orderBy('atividades.id', 'asc');

        $atividades = $query->get();

        $eventos = Evento::query()->orderBy('nome')->pluck('nome', 'id');

        $municipios = Municipio::query()
            ->with('estado:id,sigla')
            ->whereIn('id',
                Atividade::query()
                    ->whereNotNull('municipio_id')
                    ->distinct()
                    ->pluck('municipio_id')
            )
            ->orderBy('nome')
            ->get();

        $momentos = Atividade::query()
            ->select('descricao')
            ->whereNotNull('descricao')
            ->where('descricao', '!=', '')
            ->distinct()
            ->orderBy('descricao')
            ->pluck('descricao');

        return view('relatorio-quantitativo.index',
            compact('atividades', 'eventos', 'municipios', 'momentos', 'sort', 'dir'));
    }

    public function momentos(Request $request)
    {
        $eventoId = $request->integer('evento_id');

        $momentos = Atividade::query()
            ->select('descricao')
            ->when($eventoId, fn ($q) => $q->where('evento_id', $eventoId))
            ->whereNotNull('descricao')
            ->where('descricao', '!=', '')
            ->distinct()
            ->orderBy('descricao')
            ->pluck('descricao');

        $municipios = Municipio::query()
            ->with('estado:id,sigla')
            ->whereIn('id',
                Atividade::query()
                    ->select('municipio_id')
                    ->when($eventoId, fn ($q) => $q->where('evento_id', $eventoId))
                    ->whereNotNull('municipio_id')
                    ->distinct()
                    ->pluck('municipio_id')
            )
            ->orderBy('nome')
            ->get(['id', 'nome', 'estado_id'])
            ->map(fn ($m) => ['id' => $m->id, 'nome' => $m->nome_com_estado]);

        return response()->json(['momentos' => $momentos, 'municipios' => $municipios]);
    }
}
