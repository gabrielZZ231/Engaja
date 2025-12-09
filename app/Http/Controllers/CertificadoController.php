<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Models\Evento;
use App\Models\ModeloCertificado;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CertificadoController extends Controller
{
    public function emitir(Request $request)
    {
        $data = $request->validate([
            'modelo_id' => ['required', 'exists:modelo_certificados,id'],
            'eventos'   => ['required'],
        ]);

        $eventosIds = $data['eventos'];
        if (is_string($eventosIds)) {
            $eventosIds = array_filter(explode(',', $eventosIds));
        }
        if (is_array($eventosIds)) {
            $eventosIds = array_map('intval', $eventosIds);
        } else {
            $eventosIds = [];
        }
        $eventosIds = array_unique(array_filter($eventosIds));
        if (empty($eventosIds)) {
            return back()->with('error', 'Selecione ao menos uma ação pedagógica.');
        }

        $modelo = ModeloCertificado::findOrFail($data['modelo_id']);

        $eventos = Evento::with(['inscricoes.participante.user'])
            ->whereIn('id', $eventosIds)
            ->get();

        $created = 0;
        foreach ($eventos as $evento) {
            foreach ($evento->inscricoes as $inscricao) {
                $participante = $inscricao->participante;
                if (!$participante || !$participante->user) {
                    continue;
                }

                $map = [
                    '%participante%' => $participante->user->name,
                    '%acao%'         => $evento->nome,
                ];

                $textoFrente = $this->renderPlaceholders($modelo->texto_frente ?? '', $map);
                $textoVerso  = $this->renderPlaceholders($modelo->texto_verso ?? '', $map);

                Certificado::create([
                    'modelo_certificado_id' => $modelo->id,
                    'participante_id'       => $participante->id,
                    'codigo_validacao'      => Str::uuid()->toString(),
                    'ano'                   => (int)($evento->data_inicio ? date('Y', strtotime($evento->data_inicio)) : date('Y')),
                    'texto_frente'          => $textoFrente,
                    'texto_verso'           => $textoVerso,
                ]);
                $created++;
            }
        }

        return redirect()
            ->back()
            ->with('success', "{$created} certificado(s) emitidos com sucesso.");
    }

    private function renderPlaceholders(string $texto, array $map): string
    {
        return strtr($texto, $map);
    }
}
