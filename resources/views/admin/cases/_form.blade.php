@php
    $case = $case ?? null;
@endphp

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="client_id" :value="__('Cliente')" />
        <select id="client_id" name="client_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @foreach ($clients as $client)
                <option value="{{ $client->id }}" @selected((int) old('client_id', $case?->client_id) === $client->id)>{{ $client->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="legal_area_id" :value="__('Área jurídica')" />
        <select id="legal_area_id" name="legal_area_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @foreach ($legalAreas as $area)
                <option value="{{ $area->id }}" @selected((int) old('legal_area_id', $case?->legal_area_id) === $area->id)>{{ $area->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('legal_area_id')" class="mt-2" />
    </div>
</div>

<div class="mt-4 grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="case_number" :value="__('Número do processo')" />
        <x-text-input id="case_number" name="case_number" type="text" class="block mt-1 w-full" :value="old('case_number', $case?->case_number)" />
        <x-input-error :messages="$errors->get('case_number')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="responsible_lawyer_id" :value="__('Advogado responsável')" />
        <select id="responsible_lawyer_id" name="responsible_lawyer_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
            <option value="">{{ __('— Nenhum —') }}</option>
            @foreach ($lawyers as $lawyer)
                <option value="{{ $lawyer->id }}" @selected((int) old('responsible_lawyer_id', $case?->responsible_lawyer_id) === $lawyer->id)>{{ $lawyer->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('responsible_lawyer_id')" class="mt-2" />
    </div>
</div>

<div class="mt-4 grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="country" :value="__('País')" />
        <select id="country" name="country" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
            <option value="Brazil" @selected(old('country', $case?->country) === 'Brazil')>{{ __('Brasil') }}</option>
            <option value="Portugal" @selected(old('country', $case?->country) === 'Portugal')>{{ __('Portugal') }}</option>
        </select>
        <x-input-error :messages="$errors->get('country')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @foreach (['in_progress' => 'Em andamento', 'completed' => 'Concluído', 'suspended' => 'Suspenso', 'archived' => 'Arquivado'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $case?->status) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>
</div>

<div class="mt-4 grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="opened_at" :value="__('Data de abertura')" />
        <x-text-input id="opened_at" name="opened_at" type="date" class="block mt-1 w-full" :value="old('opened_at', $case?->opened_at?->format('Y-m-d'))" />
        <x-input-error :messages="$errors->get('opened_at')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="current_deadline" :value="__('Prazo atual')" />
        <x-text-input id="current_deadline" name="current_deadline" type="date" class="block mt-1 w-full" :value="old('current_deadline', $case?->current_deadline?->format('Y-m-d'))" />
        <x-input-error :messages="$errors->get('current_deadline')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="description" :value="__('Descrição')" />
    <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $case?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>
