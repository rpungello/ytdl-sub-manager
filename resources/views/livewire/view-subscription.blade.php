<div>
    <flux:heading size="xl" level="1">
        {{ $name }}
    </flux:heading>
    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Upload Date') }}</flux:table.column>
            <flux:table.column>{{ __('Title') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach($this->videos as $video)
                <flux:table.row>
                    <flux:table.cell>{{ $video['upload_date'] }}</flux:table.cell>
                    <flux:table.cell>{{ $video['file_names'][0] }}</flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
