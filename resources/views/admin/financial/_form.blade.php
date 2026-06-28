@php
    $entry = $entry ?? null;
@endphp

<div>
    <x-input-label for="client_id" :value="__('Cliente')" />
    <select id="client_id" name="client_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
        @foreach ($clients as $client)
            <option value="{{ $client->id }}" @selected(old('client_id', $entry?->client_id) == $client->id)>
                {{ $client->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="case_id" :value="__('Processo (opcional)')" />
    <select id="case_id" name="case_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
        <option value="">{{ __('— Nenhum —') }}</option>
        @foreach ($cases as $case)
            <option value="{{ $case->id }}" @selected(old('case_id', $entry?->case_id) == $case->id)>
                {{ $case->case_number ?? ('#'.$case->id) }} — {{ $case->client->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('case_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="type" :value="__('Tipo')" />
    <select id="type" name="type" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
        <option value="income" @selected(old('type', $entry?->type) === 'income')>{{ __('Receita') }}</option>
        <option value="expense" @selected(old('type', $entry?->type) === 'expense')>{{ __('Despesa') }}</option>
    </select>
    <x-input-error :messages="$errors->get('type')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="description" :value="__('Descrição')" />
    <textarea id="description" name="description" rows="2" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $entry?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="mt-4 grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="amount" :value="__('Valor')" />
        <x-text-input id="amount" name="amount" type="number" step="0.01" min="0" class="block mt-1 w-full" :value="old('amount', $entry?->amount)" />
        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="currency" :value="__('Moeda')" />
        <select id="currency" name="currency" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
            <option value="BRL" @selected(old('currency', $entry?->currency) === 'BRL')>BRL</option>
            <option value="EUR" @selected(old('currency', $entry?->currency) === 'EUR')>EUR</option>
        </select>
        <x-input-error :messages="$errors->get('currency')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="due_date" :value="__('Data de vencimento')" />
    <x-text-input id="due_date" name="due_date" type="date" class="block mt-1 w-full" :value="old('due_date', $entry?->due_date?->format('Y-m-d'))" />
    <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="status" :value="__('Status')" />
    <select id="status" name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
        @foreach (['pending' => 'Pendente', 'paid' => 'Pago', 'overdue' => 'Atrasado', 'cancelled' => 'Cancelado'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $entry?->status ?? 'pending') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('status')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="payment_date" :value="__('Data de pagamento (preenchida automaticamente ao marcar como Pago, se deixada em branco)')" />
    <x-text-input id="payment_date" name="payment_date" type="date" class="block mt-1 w-full" :value="old('payment_date', $entry?->payment_date?->format('Y-m-d'))" />
    <x-input-error :messages="$errors->get('payment_date')" class="mt-2" />
</div>
