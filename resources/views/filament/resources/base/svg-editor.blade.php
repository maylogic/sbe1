@if ($getRecord()?->id)
    @livewire('base-text-areas-editor', ['baseId' => $getRecord()->id])
@endif
