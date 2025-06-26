<div>
    <!-- HEADER -->
    <x-header title="{{ __('Subscriptions') }}" separator>
    </x-header>

    <div class="space-y-4">
        @foreach($subscriptions as $key => $subscription)
            <x-card :title="$key">
                <x-form wire:submit.prevent="save('{{ $key }}')">
                    <x-input label="{{ __('Name') }}" wire:model="subscriptions.{{ $key }}.overrides.tv_show_name"/>
                    <x-input label="{{ __('URL') }}" wire:model="subscriptions.{{ $key }}.overrides.url"/>

                    <x-button class="btn btn-primary" type="submit" label="{{ __('Save') }}"/>
                </x-form>
            </x-card>
        @endforeach
    </div>
</div>
