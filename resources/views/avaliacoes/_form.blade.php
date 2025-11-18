@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
  <div class="col-xl-8">

    <div class="d-flex justify-content-center align-items-center mb-4">
      <h1 class="h3 fw-bold text-engaja mb-1">
        Avaliação - {{ $atividade->descricao }}
      </h1>
    </div>

    @php
      $inscricaoExibida = $avaliacao->inscricao ?? $avaliacao->respostas->first()?->inscricao;
      $participanteNome = $inscricaoExibida?->participante?->user?->name;
      $eventoNome = $inscricaoExibida?->evento?->nome;
      $respostas = $avaliacao->respostas->pluck('resposta', 'avaliacao_questao_id');
    @endphp

    <div class="card shadow-sm">
      <div class="card-body">

        <form method="POST" action="">
          @csrf

          <ol class="list-group list-group-numbered list-group-flush">
            @forelse ($avaliacao->avaliacaoQuestoes as $questao)
              <li class="list-group-item px-0">
                <p class="fw-semibold mb-1">{{ $questao->texto }}</p>
                <p class="text-muted small mb-1">
                  Indicador: {{ $questao->indicador->descricao ?? '-' }}
                  @if ($questao->indicador && $questao->indicador->dimensao)
                    &bull; Dimensão: {{ $questao->indicador->dimensao->descricao ?? '-' }}
                  @endif
                </p>
                <p class="text-muted small mb-2">
                  Tipo: {{ $tiposQuestao[$questao->tipo] ?? ucfirst($questao->tipo) }}
                  @if ($questao->evidencia)
                    &bull; Evidência: {{ $questao->evidencia->descricao }}
                  @endif
                  @if ($questao->tipo === 'escala' && $questao->escala)
                    &bull; Escala: {{ $questao->escala->descricao }}
                  @endif
                </p>

                {{-- Campo de resposta dinâmico --}}
                <div class="mt-2">
                  @php $resposta = old("respostas.{$questao->id}", $respostas[$questao->id] ?? null); @endphp

                  @switch($questao->tipo)
                    @case('texto')
                    @case('texto_aberto')
                      <textarea name="respostas[{{ $questao->id }}]" class="form-control" rows="2" placeholder="">{{ $resposta }}</textarea>
                      @break

                    @case('numero')
                      <input type="number" name="respostas[{{ $questao->id }}]" class="form-control" value="{{ $resposta }}" placeholder="">
                      @break

                    @case('escala')
                      @if($questao->escala && $questao->escala->valores)
                        <div class="d-flex gap-3 align-items-center flex-wrap">
                          @foreach($questao->escala->valores as $valor)
                            <div class="form-check">
                              <input class="form-check-input" type="radio" 
                                     name="respostas[{{ $questao->id }}]" 
                                     value="{{ $valor }}"
                                     id="q{{ $questao->id }}_{{ $valor }}"
                                     {{ $resposta == $valor ? 'checked' : '' }}>
                              <label class="form-check-label" for="q{{ $questao->id }}_{{ $valor }}">
                                {{ $valor }}
                              </label>
                            </div>
                          @endforeach
                        </div>
                      @else
                        <p class="text-muted small">Escala não configurada.</p>
                      @endif
                      @break

                    @default
                      <input type="text" name="respostas[{{ $questao->id }}]" class="form-control" value="{{ $resposta }}" placeholder="">
                  @endswitch
                </div>
              </li>
            @empty
              <li class="list-group-item px-0 text-muted">Nenhuma questão cadastrada.</li>
            @endforelse
          </ol>

          <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary">
              Avaliar
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
@endsection
