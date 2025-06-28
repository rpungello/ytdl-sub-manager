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
                    <x-button class="btn btn-success" type="submit" icon="lucide.plus"/>
                </x-slot:actions>
            </x-card>
        </x-form>

        <x-card title="{{ __('Filter') }}">
            <x-select label="{{ __('Preset') }}" wire:model.live="filterPreset" :options="$presets" placeholder="- {{ __('All') }}- "/>
        </x-card>

        @foreach($subscriptions as $key => $subscription)
            @if($this->shouldShowSubscription($key))
                <x-form wire:submit.prevent="save">
                    <x-card :title="$subscription['overrides']['tv_show_name']">
                        <x-input label="{{ __('URL') }}" wire:model="subscriptions.{{ $key }}.overrides.url"/>
                        <x-select label="{{ __('Preset') }}" wire:model="subscriptions.{{ $key }}.preset" :options="$presets"/>


                        <x-slot:actions>
                            <x-button class="btn btn-primary" type="submit" icon="lucide.save"/>
                            <x-button class="btn btn-error" type="button" icon="lucide.trash" wire:click="remove('{{ $key }}')"/>
                        </x-slot:actions>
                    </x-card>
                </x-form>
            @endif
        @endforeach
    </div>
</div>
