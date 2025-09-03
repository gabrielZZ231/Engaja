@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h4 fw-bold text-engaja mb-0">Momentos — {{ $evento->nome }}</h1>
      <small class="text-muted">Gerencie a programação da ação pedagógica</small>
    </div>
    <a href="{{ route('eventos.atividades.create', $evento) }}" class="btn btn-engaja">+ Novo momento</a>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>Dia</th>
          <th>Hora início</th>
          <th>Carga horária</th>
          <th class="text-end">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse($atividades as $at)
          <tr>
            <td>{{ \Carbon\Carbon::parse($at->dia)->format('d/m/Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($at->hora_inicio)->format('H:i') }}</td>
            <td>{{ $at->carga_horaria }} min</td>
            <td class="text-end">
              <a href="{{ route('atividades.edit', $at) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
              <form class="d-inline" method="POST" action="{{ route('atividades.destroy', $at) }}" onsubmit="return confirm('Remover momento?');">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Excluir</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center text-muted py-4">Nenhum momento cadastrada.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{ $atividades->links() }}

  <div class="mt-3">
    <a href="{{ route('eventos.show', $evento) }}" class="btn btn-outline-secondary">Voltar à ação pedagógica</a>
  </div>
</div>
@endsection
