<div>
    <x-input-label for="name" :value="__('Nome')" />
    <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $user?->name)" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="email" :value="__('Email')" />
    <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email', $user?->email)" required />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<div>
    <x-input-label for="phone" :value="__('Telefone')" />
    <x-text-input id="phone" name="phone" type="text" class="block mt-1 w-full" :value="old('phone', $user?->phone)" />
    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
</div>

<div>
    <x-input-label for="password" :value="$user ? __('Nova senha (deixe em branco para não alterar)') : __('Senha')" />
    <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" :required="! $user" />
    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>

<div>
    <x-input-label for="password_confirmation" :value="__('Confirmar senha')" />
    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" :required="! $user" />
</div>

<div>
    <x-input-label for="access_level" :value="__('Nível de acesso')" />
    <select id="access_level" name="access_level" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
        @foreach (['administrator' => 'Administrador', 'lawyer' => 'Advogado', 'staff' => 'Funcionário'] as $value => $label)
            <option value="{{ $value }}" @selected(old('access_level', $user?->access_level) === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('access_level')" class="mt-2" />
</div>

<div class="flex items-center">
    <input id="can_view_all_cases" name="can_view_all_cases" type="checkbox" value="1"
           @checked(old('can_view_all_cases', $user?->can_view_all_cases))
           class="rounded border-gray-300 text-indigo-600">
    <label for="can_view_all_cases" class="ms-2 text-sm text-gray-600">
        {{ __('Advogado: pode ver processos de outros advogados (não só os próprios)') }}
    </label>
</div>

<div class="flex items-center">
    <input id="can_access_financial" name="can_access_financial" type="checkbox" value="1"
           @checked(old('can_access_financial', $user?->can_access_financial))
           class="rounded border-gray-300 text-indigo-600">
    <label for="can_access_financial" class="ms-2 text-sm text-gray-600">
        {{ __('Funcionário: libera acesso ao módulo financeiro') }}
    </label>
</div>
