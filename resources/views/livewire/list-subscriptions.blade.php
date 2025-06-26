<div>
    <!-- HEADER -->
    <x-header title="{{ __('Subscriptions') }}" separator>
    </x-header>

    <div class="space-y-4">
        <x-form wire:submit.prevent="create()">
            <x-card title="{{ __('New') }}">
                <x-input label="{{ __('Key') }}" wire:model="newKey"/>
                <x-input label="{{ __('Name') }}" wire:model="newName"/>
                <x-input label="{{ __('URL') }}" wire:model="newUrl"/>
                <x-select label="{{ __('Preset') }}" wire:model="newPreset" :options="$presets"/>


                <x-slot:actions>
                    <x-button class="btn btn-success" type="submit" label="{{ __('Create') }}"/>
                </x-slot:actions>
            </x-card>
        </x-form>

        @foreach($subscriptions as $key => $subscription)
            <x-form wire:submit.prevent="save">
                <x-card :title="$key">
                    <x-input label="{{ __('Name') }}" wire:model="subscriptions.{{ $key }}.overrides.tv_show_name"/>
                    <x-input label="{{ __('URL') }}" wire:model="subscriptions.{{ $key }}.overrides.url"/>
                    <x-select label="{{ __('Preset') }}" wire:model="subscriptions.{{ $key }}.preset" :options="$presets"/>


                    <x-slot:actions>
                        <x-button class="btn btn-primary" type="submit" label="{{ __('Save') }}"/>
                        <x-button class="btn btn-error" type="button" icon="o-trash" wire:click="remove('{{ $key }}')"/>
                    </x-slot:actions>
                </x-card>
            </x-form>
        @endforeach
    </div>
</div>
