@extends('layouts.app')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
        <div>
            <p class="text-uppercase text-muted small mb-1">Administração</p>
            <h1 class="h4 fw-bold text-engaja mb-2">Notificações de Agendamento</h1>
            <p class="text-muted small mb-0">Selecione quais usuários devem receber e-mail quando um novo agendamento for criado.</p>
        </div>
        <a href="{{ route('usuarios.index') }}" class="btn btn-light border">Voltar</a>
    </div>

    <div class="filter-bar shadow-sm">
        <form action="{{ route('usuarios.notificacoes-agendamento.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Buscar nome ou e-mail..." value="{{ $search }}">
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-engaja w-100">Filtrar</button>
                @if($search)
                    <a href="{{ route('usuarios.notificacoes-agendamento.index') }}" class="btn btn-light border w-100">Limpar</a>
                @endif
            </div>
        </form>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($users->isEmpty())
    <div class="alert alert-info">
        @if (!empty($search))
            Nenhum usuário encontrado para "{{ $search }}".
        @else
            Não há usuários cadastrados.
        @endif
    </div>
@else
    <div class="table-responsive shadow-sm">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th class="text-center">Recebe notificação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td class="text-center">
                            <form method="POST" action="{{ route('usuarios.notificacoes-agendamento.toggle', ['managedUser' => $user, 'q' => $search]) }}">
                                @csrf
                                <input type="checkbox"
                                    class="form-check-input"
                                    onchange="this.form.submit()"
                                    {{ $user->hasPermissionTo('agendamento.notificar') ? 'checked' : '' }}>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $users->links() }}
    </div>
@endif
@endsection
