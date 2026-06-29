<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendamentoNotificacaoController extends Controller
{
    private const PERMISSION = 'agendamento.notificar';

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->whereNotNull('email')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'ilike', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->appends(['q' => $search]);

        return view('usuarios.notificacoes-agendamento', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function toggle(Request $request, User $managedUser): RedirectResponse
    {
        if ($managedUser->hasPermissionTo(self::PERMISSION)) {
            $managedUser->revokePermissionTo(self::PERMISSION);
        } else {
            $managedUser->givePermissionTo(self::PERMISSION);
        }

        return redirect()
            ->route('usuarios.notificacoes-agendamento.index', ['q' => $request->query('q')])
            ->with('success', 'Preferência de notificação atualizada com sucesso.');
    }
}
