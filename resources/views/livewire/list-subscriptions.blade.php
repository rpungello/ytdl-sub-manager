<div>
    <!-- HEADER -->
    <flux:heading size="xl" level="1" class="mb-4">
        {{ __('Subscriptions') }}
    </flux:heading>

    <div class="space-y-4">
        <form wire:submit.prevent="create()">
            <flux:card class="space-y-4">
                <flux:heading size="lg" level="2">
                    {{ __('New') }}
                </flux:heading>
                <flux:input label="{{ __('Key') }}" wire:model="newKey"/>
                <flux:input label="{{ __('Name') }}" wire:model="newName"/>
                <flux:input label="{{ __('URL') }}" wire:model="newUrl"/>
                <flux:select label="{{ __('Preset') }}" wire:model="newPreset">
                    @foreach($presets as $preset)
                        <flux:select.option label="{{ $preset }}" value="{{ $preset }}"/>
                    @endforeach
                </flux:select>

                <flux:button variant="primary" type="submit" icon="save"/>
            </flux:card>
        </form>

        <flux:card>
            <flux:heading size="lg" level="2" class="mb-4">
                {{ __('Filter') }}
            </flux:heading>
            <flux:select label="{{ __('Preset') }}" wire:model.live="filterPreset">
                <flux:select.option label="{{ __('All') }}" value="all"/>
                @foreach($presets as $preset)
                    <flux:select.option label="{{ $preset }}" value="{{ $preset }}"/>
                @endforeach
            </flux:select>
        </flux:card>

        @foreach($subscriptions as $key => $subscription)
            @if($this->shouldShowSubscription($key))
                <form wire:submit.prevent="save">
                    <flux:card class="space-y-4">
                        <flux:heading size="lg" level="2">
                            <flux:link :href="route('subscriptions.view', $key)">
                                {{ $subscription['overrides']['tv_show_name'] }}
                            </flux:link>
                            <flux:badge size="sm">{{ number_format($this->getNumberOfEpisodes($key)) }}</flux:badge>
                        </flux:heading>
                        <flux:input label="{{ __('URL') }}" wire:model="subscriptions.{{ $key }}.overrides.url"/>
                        <flux:select label="{{ __('Preset') }}" wire:model="subscriptions.{{ $key }}.preset">
                            @foreach($presets as $preset)
                                <flux:select.option label="{{ $preset }}" value="{{ $preset }}"/>
                            @endforeach
                        </flux:select>

                        <flux:button variant="primary" type="submit" icon="save"/>
                        <flux:modal.trigger name="delete-subscription">
                            <flux:button variant="danger" type="button" icon="trash" wire:click="prepareRemove('{{ $key }}')"/>
                        </flux:modal.trigger>
                    </flux:card>
                </form>
            @endif
        @endforeach
    </div>

    <flux:modal name="delete-subscription" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete subscription?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this subscription.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer/>
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="removeSubscription" variant="danger">Delete subscription</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
