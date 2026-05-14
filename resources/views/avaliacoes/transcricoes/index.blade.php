@extends('layouts.app')

@push('styles')
<style>
  .avaliacoes-transcricoes-actions-dropdown .dropdown-menu {
    position: fixed !important;
    z-index: 1080;
  }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h3 fw-bold text-engaja mb-0">Transcrições de avaliação</h1>
    <p class="text-muted mb-0">Fluxo para transcrever respostas de formulários em papel.</p>
  </div>
  <a href="{{ route('avaliacoes-transcricoes.create') }}" class="btn btn-engaja">Nova transcrição</a>
</div>

<form method="GET" action="{{ route('avaliacoes-transcricoes.index') }}" class="card shadow-sm mb-4">
  <div class="card-body">
    <div class="row g-1 align-items-end">
      <div class="col-lg-3 col-md-6">
        <label for="search" class="form-label">Buscar por momento, modelo ou descrição</label>
        <input type="text" class="form-control" id="search" name="search"
          value="{{ request('search') }}" placeholder="Digite para filtrar...">
      </div>
      <div class="col-lg-3 col-md-6">
        <label for="template_id" class="form-label">Modelo</label>
        <select id="template_id" name="template_id" class="form-select">
          <option value="">Todos</option>
          @foreach ($templatesDisponiveis as $id => $nome)
          <option value="{{ $id }}" @selected((string) request('template_id') === (string) $id)>{{ $nome }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-2 col-md-6">
        <label for="de" class="form-label">Registrada de</label>
        <input type="date" id="de" name="de" class="form-control" value="{{ request('de') }}">
      </div>
      <div class="col-lg-2 col-md-6">
        <label for="ate" class="form-label">Registrada até</label>
        <input type="date" id="ate" name="ate" class="form-control" value="{{ request('ate') }}">
      </div>
      <div class="col-2 d-flex gap-1">
        <input type="hidden" name="sort" value="{{ request('sort', 'created_at') }}">
        <input type="hidden" name="dir"
          value="{{ strtolower(request('dir', request('direction', 'desc'))) === 'asc' ? 'asc' : 'desc' }}">
        <button type="submit" class="btn btn-engaja">Aplicar</button>
        <a href="{{ route('avaliacoes-transcricoes.index') }}" class="btn btn-outline-secondary">Limpar</a>
      </div>
    </div>
  </div>
</form>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <colgroup>
        <col style="width: 20%;">
        <col style="width: 26%;">
        <col style="width: 20%;">
        <col style="width: 10%;">
        <col style="width: 14%;">
        <col style="width: 10%;">
      </colgroup>
      <thead class="table-light">
        @php
          function transcricao_sort_link($label, $key) {
            $currentSort = request('sort', 'created_at');
            $dirParam = request('dir', request('direction', 'desc'));
            $currentDir = strtolower((string) $dirParam) === 'asc' ? 'asc' : 'desc';
            $nextDir = ($currentSort === $key && $currentDir === 'asc') ? 'desc' : 'asc';
            $params = array_merge(request()->except('page'), ['sort' => $key, 'dir' => $nextDir]);
            $url = request()->url() . '?' . http_build_query($params);
            $isActive = $currentSort === $key;
            $arrow = $isActive ? ($currentDir === 'asc' ? '↑' : '↓') : '';

            return '<a href="' . $url . '" class="text-decoration-none text-nowrap">' . e($label) . ' <span class="text-muted">' . $arrow . '</span></a>';
          }
        @endphp
        <tr>
          <th class="ps-3">Descrição</th>
          <th>Momento</th>
          <th>{!! transcricao_sort_link('Modelo', 'template') !!}</th>
          <th>Submissões</th>
          <th>{!! transcricao_sort_link('Registrada em', 'created_at') !!}</th>
          <th class="pe-3">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($avaliacoes as $avaliacao)
        <tr>
          <td class="ps-3 fw-semibold">
            <a href="{{ route('avaliacao.formulario', $avaliacao) }}" class="text-decoration-none text-dark d-block">
              {{ $avaliacao->descricao_universal ?: '—' }}
              <div class="small text-muted">Clique para transcrever respostas.</div>
            </a>
          </td>
          <td>
            <span>{{ $avaliacao->atividade->descricao ?? 'Sem momento' }}</span>
            <small class="d-block text-muted">
              {{ $avaliacao->atividade?->dia ? \Illuminate\Support\Carbon::parse($avaliacao->atividade->dia)->format('d/m/Y') : '' }}
              {{ $avaliacao->atividade?->hora_inicio ?? '' }}
            </small>
          </td>
          <td>{{ $avaliacao->templateAvaliacao->nome ?? '—' }}</td>
          <td>{{ $avaliacao->respostas->pluck('submissao_avaliacao_id')->filter()->unique()->count() }}</td>
          <td>{{ $avaliacao->created_at ? $avaliacao->created_at->format('d/m/Y H:i') : '—' }}</td>
          <td class="pe-3">
            <div class="dropdown avaliacoes-transcricoes-actions-dropdown">
              <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                Gerenciar
              </button>
              <ul class="dropdown-menu shadow-sm">
                <li>
                  <a href="{{ route('avaliacoes-transcricoes.show', $avaliacao) }}" class="dropdown-item">Ver</a>
                </li>
                <li>
                  <a href="{{ route('avaliacoes-transcricoes.edit', $avaliacao) }}" class="dropdown-item">Editar</a>
                </li>
                <li>
                  <a href="{{ route('avaliacoes.respostas', $avaliacao) }}" class="dropdown-item">Respostas</a>
                </li>
                @hasanyrole('administrador|gerente|eq_pedagogica')
                <li><hr class="dropdown-divider"></li>
                <li>
                  <form action="{{ route('avaliacoes-transcricoes.destroy', $avaliacao) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger"
                      onclick="return confirm('Tem certeza que deseja excluir esta transcrição?')">Excluir</button>
                  </form>
                </li>
                @endhasanyrole
              </ul>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center text-muted py-4">Nenhuma transcrição registrada.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">
  {{ $avaliacoes->links() }}
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.avaliacoes-transcricoes-actions-dropdown').forEach((dropdown) => {
      const positionMenu = () => {
        const button = dropdown.querySelector('[data-bs-toggle="dropdown"]');
        const menu = dropdown.querySelector('.dropdown-menu');

        if (!button || !menu) {
          return;
        }

        const buttonRect = button.getBoundingClientRect();
        const menuWidth = menu.offsetWidth || 180;
        const menuHeight = menu.offsetHeight || menu.scrollHeight || 180;
        const gap = 6;
        const margin = 8;
        const opensUp = buttonRect.bottom + gap + menuHeight > window.innerHeight - margin;
        const top = opensUp
          ? Math.max(margin, buttonRect.top - gap - menuHeight)
          : Math.min(window.innerHeight - margin - menuHeight, buttonRect.bottom + gap);
        const left = Math.min(
          Math.max(margin, buttonRect.left),
          window.innerWidth - margin - menuWidth
        );

        menu.style.position = 'fixed';
        menu.style.inset = 'auto';
        menu.style.transform = 'none';
        menu.style.top = `${top}px`;
        menu.style.left = `${left}px`;
        menu.style.zIndex = '1080';
      };

      dropdown.addEventListener('shown.bs.dropdown', positionMenu);
      dropdown.addEventListener('hidden.bs.dropdown', () => {
        const menu = dropdown.querySelector('.dropdown-menu');

        if (!menu) {
          return;
        }

        menu.removeAttribute('style');
      });
    });
  });
</script>
@endpush
