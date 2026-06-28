@php
    $service = $service ?? null;
@endphp

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="client_id" :value="__('Cliente')" />
        <select id="client_id" name="client_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @foreach ($clients as $client)
                <option value="{{ $client->id }}" @selected((int) old('client_id', $service?->client_id) === $client->id)>{{ $client->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="case_id" :value="__('Processo (opcional)')" />
        <select id="case_id" name="case_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
            <option value="">{{ __('— Nenhum —') }}</option>
            @foreach ($cases as $case)
                <option value="{{ $case->id }}" @selected((int) old('case_id', $service?->case_id) === $case->id)>{{ $case->case_number ?? ('#'.$case->id) }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('case_id')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="description" :value="__('Descrição')" />
    <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $service?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="mt-4 grid grid-cols-3 gap-4">
    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @foreach (['pending' => 'Pendente', 'in_progress' => 'Em andamento', 'completed' => 'Concluído'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $service?->status) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="execution_deadline" :value="__('Prazo de execução')" />
        <x-text-input id="execution_deadline" name="execution_deadline" type="date" class="block mt-1 w-full" :value="old('execution_deadline', $service?->execution_deadline?->format('Y-m-d'))" />
        <x-input-error :messages="$errors->get('execution_deadline')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="responsible_id" :value="__('Responsável')" />
        <select id="responsible_id" name="responsible_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
            <option value="">{{ __('— Nenhum —') }}</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected((int) old('responsible_id', $service?->responsible_id) === $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('responsible_id')" class="mt-2" />
    </div>
</div>
