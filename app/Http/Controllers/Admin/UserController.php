<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * A autorização "manage-users" já é aplicada no grupo de rotas (routes/web.php), mas
     * cada ação também consulta a UserPolicy para reforço (Bloco 2: nunca usar `if`
     * espalhado para checar permissão, sempre Policy/Gate).
     */
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::orderBy('name')->paginate(15);

        return view('admin.users.index', ['users' => $users]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:50'],
            'access_level' => ['required', Rule::in(['administrator', 'lawyer', 'staff'])],
            'can_view_all_cases' => ['nullable', 'boolean'],
            'can_access_financial' => ['nullable', 'boolean'],
        ]);

        User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
            'can_view_all_cases' => $request->boolean('can_view_all_cases'),
            'can_access_financial' => $request->boolean('can_access_financial'),
            'active' => true,
        ]);

        return redirect()->route('admin.users.index')->with('status', 'Usuário criado com sucesso.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:50'],
            'access_level' => ['required', Rule::in(['administrator', 'lawyer', 'staff'])],
            'can_view_all_cases' => ['nullable', 'boolean'],
            'can_access_financial' => ['nullable', 'boolean'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'access_level' => $validated['access_level'],
            'can_view_all_cases' => $request->boolean('can_view_all_cases'),
            'can_access_financial' => $request->boolean('can_access_financial'),
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'Usuário atualizado com sucesso.');
    }

    /**
     * Ativa/desativa o usuário. Nunca apagamos definitivamente (Bloco 2 — decisão do
     * usuário), por isso não existe destroy().
     */
    public function toggleActive(User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $user->update(['active' => ! $user->active]);

        return redirect()->route('admin.users.index')
            ->with('status', $user->active ? 'Usuário ativado.' : 'Usuário desativado.');
    }
}
