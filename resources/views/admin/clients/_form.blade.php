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
            <option value="individual" @selected(old('person_type', $client?->person_type) === 'individual')>{{ __('FÃ­sica') }}</option>
            <option value="company" @selected(old('person_type', $client?->person_type) === 'company')>{{ __('JurÃ­dica') }}</option>
        </select>
        <x-input-error :messages="$errors->get('person_type')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="country" :value="__('PaÃ­s')" />
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
        <x-input-label for="secondary_document_number" :value="__('CartÃ£o de CidadÃ£o (Portugal, opcional)')" />
        <x-text-input id="secondary_document_number" name="secondary_document_number" type="text" class="block mt-1 w-full" :value="old('secondary_document_number', $client?->secondary_document_number)" />
        <x-input-error :messages="$errors->get('secondary_document_number')" class="mt-2" />
    </div>
</div>


        {{-- Qualificação (para petição inicial) --}}
        <div class="mt-6 border-t pt-4">
            <h3 class="text-base font-semibold text-gray-800">{{ __('Qualificação') }}</h3>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="nationality" :value="__('Nacionalidade')" />
                <x-text-input id="nationality" name="nationality" type="text" class="block mt-1 w-full" :value="old('nationality', $client?->nationality)" />
                <x-input-error :messages="$errors->get('nationality')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="profession" :value="__('Profissão')" />
                <x-text-input id="profession" name="profession" type="text" class="block mt-1 w-full" :value="old('profession', $client?->profession)" />
                <x-input-error :messages="$errors->get('profession')" class="mt-2" />
            </div>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="marital_status" :value="__('Estado civil')" />
                <select id="marital_status" name="marital_status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">—</option>
                    @foreach (['solteiro'=>'Solteiro(a)','casado'=>'Casado(a)','divorciado'=>'Divorciado(a)','viuvo'=>'Viúvo(a)','separado'=>'Separado(a)','uniao_estavel'=>'União estável'] as $val => $lbl)
                        <option value="{{ $val }}" @selected(old('marital_status', $client?->marital_status) === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('marital_status')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="birth_date" :value="__('Data de nascimento')" />
                <x-text-input id="birth_date" name="birth_date" type="date" class="block mt-1 w-full" :value="old('birth_date', $client?->birth_date?->format('Y-m-d'))" />
                <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
            </div>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="document_issuer" :value="__('RG — Órgão emissor')" />
                <x-text-input id="document_issuer" name="document_issuer" type="text" class="block mt-1 w-full" :value="old('document_issuer', $client?->document_issuer)" />
                <x-input-error :messages="$errors->get('document_issuer')" class="mt-2" />
            </div>
            <div class="flex items-center mt-6">
                <input type="checkbox" id="stable_union" name="stable_union" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('stable_union', $client?->stable_union)) />
                <label for="stable_union" class="ms-2 text-sm text-gray-700">{{ __('Convive em união estável') }}</label>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="mother_name" :value="__('Nome da mãe (filiação)')" />
                <x-text-input id="mother_name" name="mother_name" type="text" class="block mt-1 w-full" :value="old('mother_name', $client?->mother_name)" />
                <x-input-error :messages="$errors->get('mother_name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="father_name" :value="__('Nome do pai (filiação)')" />
                <x-text-input id="father_name" name="father_name" type="text" class="block mt-1 w-full" :value="old('father_name', $client?->father_name)" />
                <x-input-error :messages="$errors->get('father_name')" class="mt-2" />
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
    <x-input-label for="address_street" :value="__('EndereÃ§o')" />
    <x-text-input id="address_street" name="address_street" type="text" class="block mt-1 w-full" :value="old('address_street', $client?->address_street)" />
    <x-input-error :messages="$errors->get('address_street')" class="mt-2" />
</div>

        <div class="mt-4 grid grid-cols-3 gap-4">
            <div>
                <x-input-label for="address_number" :value="__('Número')" />
                <x-text-input id="address_number" name="address_number" type="text" class="block mt-1 w-full" :value="old('address_number', $client?->address_number)" />
                <x-input-error :messages="$errors->get('address_number')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="address_complement" :value="__('Complemento')" />
                <x-text-input id="address_complement" name="address_complement" type="text" class="block mt-1 w-full" :value="old('address_complement', $client?->address_complement)" />
                <x-input-error :messages="$errors->get('address_complement')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="address_neighborhood" :value="__('Bairro')" />
                <x-text-input id="address_neighborhood" name="address_neighborhood" type="text" class="block mt-1 w-full" :value="old('address_neighborhood', $client?->address_neighborhood)" />
                <x-input-error :messages="$errors->get('address_neighborhood')" class="mt-2" />
            </div>
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
        <x-input-label for="address_zipcode" :value="__('CEP/CÃ³digo Postal')" />
        <x-text-input id="address_zipcode" name="address_zipcode" type="text" class="block mt-1 w-full" :value="old('address_zipcode', $client?->address_zipcode)" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="address_country" :value="__('PaÃ­s do endereÃ§o')" />
    <x-text-input id="address_country" name="address_country" type="text" class="block mt-1 w-full" :value="old('address_country', $client?->address_country)" />
</div>

        {{-- Dados de Pessoa Jurídica --}}
        <div class="mt-6 border-t pt-4">
            <h3 class="text-base font-semibold text-gray-800">{{ __('Dados de Pessoa Jurídica') }} <span class="text-xs font-normal text-gray-500">({{ __('preencher se cliente empresa') }})</span></h3>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="company_legal_name" :value="__('Razão social')" />
                <x-text-input id="company_legal_name" name="company_legal_name" type="text" class="block mt-1 w-full" :value="old('company_legal_name', $client?->company_legal_name)" />
                <x-input-error :messages="$errors->get('company_legal_name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="company_trade_name" :value="__('Nome fantasia')" />
                <x-text-input id="company_trade_name" name="company_trade_name" type="text" class="block mt-1 w-full" :value="old('company_trade_name', $client?->company_trade_name)" />
                <x-input-error :messages="$errors->get('company_trade_name')" class="mt-2" />
            </div>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-4">
            <div>
                <x-input-label for="legal_representative" :value="__('Representante legal')" />
                <x-text-input id="legal_representative" name="legal_representative" type="text" class="block mt-1 w-full" :value="old('legal_representative', $client?->legal_representative)" />
                <x-input-error :messages="$errors->get('legal_representative')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="legal_representative_document" :value="__('CPF do representante')" />
                <x-text-input id="legal_representative_document" name="legal_representative_document" type="text" class="block mt-1 w-full" :value="old('legal_representative_document', $client?->legal_representative_document)" />
                <x-input-error :messages="$errors->get('legal_representative_document')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="legal_representative_role" :value="__('Cargo do representante')" />
                <x-text-input id="legal_representative_role" name="legal_representative_role" type="text" class="block mt-1 w-full" :value="old('legal_representative_role', $client?->legal_representative_role)" />
                <x-input-error :messages="$errors->get('legal_representative_role')" class="mt-2" />
            </div>
        </div>

<div class="mt-4">
    <x-input-label :value="__('Ãreas jurÃ­dicas')" />
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
