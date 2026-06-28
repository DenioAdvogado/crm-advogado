@php
    $legalArea = $legalArea ?? null;
@endphp

<div>
    <x-input-label for="name" :value="__('Nome')" />
    <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $legalArea?->name)" />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="applicable_country" :value="__('País aplicável')" />
    <select id="applicable_country" name="applicable_country" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
        <option value="Both" @selected(old('applicable_country', $legalArea?->applicable_country) === 'Both')>{{ __('Ambos') }}</option>
        <option value="Brazil" @selected(old('applicable_country', $legalArea?->applicable_country) === 'Brazil')>{{ __('Brasil') }}</option>
        <option value="Portugal" @selected(old('applicable_country', $legalArea?->applicable_country) === 'Portugal')>{{ __('Portugal') }}</option>
    </select>
    <x-input-error :messages="$errors->get('applicable_country')" class="mt-2" />
</div>
