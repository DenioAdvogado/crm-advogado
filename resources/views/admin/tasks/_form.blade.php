@php
    $task = $task ?? null;
@endphp

<div>
    <x-input-label for="case_id" :value="__('Processo (opcional se vincular a um Serviço)')" />
    <select id="case_id" name="case_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
        <option value="">{{ __('— Nenhum —') }}</option>
        @foreach ($cases as $case)
            <option value="{{ $case->id }}" @selected(old('case_id', $task?->case_id) == $case->id)>
                {{ $case->case_number ?? ('#'.$case->id) }} — {{ $case->client->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('case_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="service_id" :value="__('Serviço (opcional se vincular a um Processo)')" />
    <select id="service_id" name="service_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
        <option value="">{{ __('— Nenhum —') }}</option>
        @foreach ($services as $service)
            <option value="{{ $service->id }}" @selected(old('service_id', $task?->service_id) == $service->id)>
                {{ $service->description }} — {{ $service->client->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('service_id')" class="mt-2" />
    <p class="text-xs text-gray-500 mt-1">
        {{ __('Pelo menos um vínculo — Processo OU Serviço — é obrigatório.') }}
    </p>
</div>

<div class="mt-4">
    <x-input-label for="responsible_id" :value="__('Responsável')" />
    <select id="responsible_id" name="responsible_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
        @foreach ($users as $candidate)
            <option value="{{ $candidate->id }}" @selected(old('responsible_id', $task?->responsible_id) == $candidate->id)>
                {{ $candidate->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('responsible_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="title" :value="__('Título')" />
    <x-text-input id="title" name="title" type="text" class="block mt-1 w-full" :value="old('title', $task?->title)" />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="description" :value="__('Descrição')" />
    <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $task?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="due_date" :value="__('Prazo (data e hora)')" />
    <x-text-input id="due_date" name="due_date" type="datetime-local" class="block mt-1 w-full"
        :value="old('due_date', $task?->due_date?->format('Y-m-d\TH:i'))" />
    <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
</div>

@if ($task)
    <div class="mt-4">
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @foreach (['pending' => 'Pendente', 'in_progress' => 'Em andamento', 'completed' => 'Concluída', 'overdue' => 'Atrasada'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $task->status) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>
@endif
