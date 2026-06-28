@php
    $client = $client ?? null;
@endphp

<div>
    <x-input-label for="name" :value="__('Nome')" />
    <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $client?->name)" />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4 grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="person_type" :value="__('Tipo de pessoa')" />
        <select id="person_type" name="person_type" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
            <option value="individual" @selected(old('person_type', $client?->person_type) === 'individual')>{{ __('Física') }}</option>
            <option value="company" @selected(old('person_type', $client?->person_type) === 'company')>{{ __('Jurídica') }}</option>
        </select>
        <x-input-error :messages="$errors->get('person_type')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="country" :value="__('País')" />
        <select id="country" name="country" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
            <option value="Brazil" @selected(old('country', $client?->country) === 'Brazil')>{{ __('Brasil') }}</option>
            <option value="Portugal" @selected(old('country', $client?->country) === 'Portugal')>{{ __('Portugal') }}</option>
        </select>
        <x-input-error :messages="$errors->get('country')" class="mt-2" />
    </div>
</div>

<div class="mt-4 grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="document_number" :value="__('CPF/CNPJ/NIF')" />
        <x-text-input id="document_number" name="document_number" type="text" class="block mt-1 w-full" :value="old('document_number', $client?->document_number)" />
        <x-input-error :messages="$errors->get('document_number')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="secondary_document_number" :value="__('Cartão de Cidadão (Portugal, opcional)')" />
        <x-text-input id="secondary_document_number" name="secondary_document_number" type="text" class="block mt-1 w-full" :value="old('secondary_document_number', $client?->secondary_document_number)" />
        <x-input-error :messages="$errors->get('secondary_document_number')" class="mt-2" />
    </div>
</div>

<div class="mt-4 grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="email" :value="__('E-mail (login do portal)')" />
        <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email', $client?->email)" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="phone" :value="__('Telefone')" />
        <x-text-input id="phone" name="phone" type="text" class="block mt-1 w-full" :value="old('phone', $client?->phone)" />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="portal_password" :value="$client ? __('Nova senha do portal (deixe em branco para manter)') : __('Senha do portal')" />
    <x-text-input id="portal_password" name="portal_password" type="password" class="block mt-1 w-full" />
    <x-input-error :messages="$errors->get('portal_password')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="address_street" :value="__('Endereço')" />
    <x-text-input id="address_street" name="address_street" type="text" class="block mt-1 w-full" :value="old('address_street', $client?->address_street)" />
    <x-input-error :messages="$errors->get('address_street')" class="mt-2" />
</div>

<div class="mt-4 grid grid-cols-3 gap-4">
    <div>
        <x-input-label for="address_city" :value="__('Cidade')" />
        <x-text-input id="address_city" name="address_city" type="text" class="block mt-1 w-full" :value="old('address_city', $client?->address_city)" />
    </div>
    <div>
        <x-input-label for="address_state" :value="__('Estado/Distrito')" />
        <x-text-input id="address_state" name="address_state" type="text" class="block mt-1 w-full" :value="old('address_state', $client?->address_state)" />
    </div>
    <div>
        <x-input-label for="address_zipcode" :value="__('CEP/Código Postal')" />
        <x-text-input id="address_zipcode" name="address_zipcode" type="text" class="block mt-1 w-full" :value="old('address_zipcode', $client?->address_zipcode)" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="address_country" :value="__('País do endereço')" />
    <x-text-input id="address_country" name="address_country" type="text" class="block mt-1 w-full" :value="old('address_country', $client?->address_country)" />
</div>

<div class="mt-4">
    <x-input-label :value="__('Áreas jurídicas')" />
    @php
        $selectedAreaIds = old('legal_area_ids', $client?->legalAreas->pluck('id')->all() ?? []);
    @endphp
    <div class="mt-1 space-y-1">
        @foreach ($legalAreas as $area)
            <label class="flex items-center text-sm text-gray-700">
                <input type="checkbox" name="legal_area_ids[]" value="{{ $area->id }}" class="rounded border-gray-300"
                    @checked(in_array($area->id, $selectedAreaIds))>
                <span class="ml-2">{{ $area->name }}</span>
            </label>
        @endforeach
    </div>
</div>

@if ($client)
    <div class="mt-4 flex items-center">
        <input type="checkbox" id="active" name="active" value="1" class="rounded border-gray-300" @checked(old('active', $client->active))>
        <label for="active" class="ml-2 text-sm text-gray-700">{{ __('Cliente ativo') }}</label>
    </div>
@endif
