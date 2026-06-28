<x-portal-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meus Dados') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">
                <div>
                    <h3 class="font-medium text-gray-900 mb-2">{{ __('Dados não editáveis') }}</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        {{ __('Para corrigir nome, e-mail ou documento, contate o escritório.') }}
                    </p>

                    <div class="space-y-2 text-sm">
                        <div>
                            <span class="text-gray-500">{{ __('Nome') }}:</span>
                            <span class="font-medium">{{ $client->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('E-mail') }}:</span>
                            <span class="font-medium">{{ $client->email }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">
                                {{ $client->country === 'Brazil' ? ($client->person_type === 'individual' ? 'CPF' : 'CNPJ') : 'NIF' }}:
                            </span>
                            <span class="font-medium">{{ $client->formatted_document_number }}</span>
                        </div>
                        @if ($client->secondary_document_number)
                            <div>
                                <span class="text-gray-500">{{ __('Cartão de Cidadão') }}:</span>
                                <span class="font-medium">{{ $client->secondary_document_number }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route('portal.meus-dados.update') }}" class="space-y-4 border-t pt-6">
                    @csrf
                    @method('patch')

                    <div>
                        <x-input-label for="phone" :value="__('Telefone')" />
                        <x-text-input id="phone" name="phone" type="text" class="block mt-1 w-full" :value="old('phone', $client->phone)" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="address_street" :value="__('Endereço')" />
                        <x-text-input id="address_street" name="address_street" type="text" class="block mt-1 w-full" :value="old('address_street', $client->address_street)" />
                        <x-input-error :messages="$errors->get('address_street')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="address_city" :value="__('Cidade')" />
                        <x-text-input id="address_city" name="address_city" type="text" class="block mt-1 w-full" :value="old('address_city', $client->address_city)" />
                        <x-input-error :messages="$errors->get('address_city')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="address_state" :value="__('Estado/Distrito')" />
                        <x-text-input id="address_state" name="address_state" type="text" class="block mt-1 w-full" :value="old('address_state', $client->address_state)" />
                        <x-input-error :messages="$errors->get('address_state')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="address_zipcode" :value="__('CEP/Código Postal')" />
                        <x-text-input id="address_zipcode" name="address_zipcode" type="text" class="block mt-1 w-full" :value="old('address_zipcode', $client->address_zipcode)" />
                        <x-input-error :messages="$errors->get('address_zipcode')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="address_country" :value="__('País')" />
                        <x-text-input id="address_country" name="address_country" type="text" class="block mt-1 w-full" :value="old('address_country', $client->address_country)" />
                        <x-input-error :messages="$errors->get('address_country')" class="mt-2" />
                    </div>

                    <x-primary-button>{{ __('Salvar alterações') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-portal-layout>
