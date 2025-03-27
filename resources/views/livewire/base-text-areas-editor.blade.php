<div>
    <h1>Позиции текстовых зон</h1>

    @if ($this->svgContent)
        @if($this->textAreas->count())
            <div id="svg-editor-content">
                {!! $this->svgContent !!}
                <div class="editor">
                    @foreach($this->textAreas as $textArea)
                        <div class="text-area draggable"
                             data-id="{{$textArea->id}}"
                             style="
                           width: {{$textArea->width}}px;
                           height: {{$textArea->height}}px;
                           left: {{$textArea->left}}px;
                           top: {{$textArea->top}}px;
                         ">###{{$textArea->id}}</div>
                    @endforeach
                </div>
            </div>
        @else
            Нет текстовых зон
        @endif
    @else
        Нет svg контента
    @endif
</div>

@assets
@vite(['resources/js/app.js'])
@vite(['resources/css/app.css'])
@endassets
@script
<script>
    let sendingCoordinates, sendingSize
    interact('.draggable').draggable({
        listeners: {
            move(event) {
                const target = event.target;
                const left = (parseFloat(target.style.left) || 0) + event.dx;
                const top = (parseFloat(target.style.top) || 0) + event.dy;

                target.style.left = `${left}px`
                target.style.top = `${top}px`
            },
            end(event) {
                const target = event.target;
                const left = (parseFloat(target.style.left) || 0) + event.dx;
                const top = (parseFloat(target.style.top) || 0) + event.dy;
                target.style.left = `${left}px`
                target.style.top = `${top}px`
                const id = target.dataset.id
                clearTimeout(sendingCoordinates)
                sendingCoordinates = setTimeout(() => {
                    Livewire.dispatch('updatePosition', {data: {id, left, top}});
                }, 100)
            }
        }
    });

    interact('.draggable').resizable({
        edges: {left: false, right: true, bottom: true, top: false},
        listeners: {
            move(event) {
                const target = event.target;
                let width = parseFloat(target.style.width) || 100;
                let height = parseFloat(target.style.height) || 50;

                width += event.deltaRect.width;
                height += event.deltaRect.height;

                target.style.width = `${width}px`;
                target.style.height = `${height}px`;
            },
            end(event) {
                const target = event.target;
                let width = parseFloat(target.style.width) || 100;
                let height = parseFloat(target.style.height) || 50;

                width += event.deltaRect.width;
                height += event.deltaRect.height;

                target.style.width = `${width}px`;
                target.style.height = `${height}px`;
                const id = target.dataset.id;
                clearTimeout(sendingSize)
                sendingSize = setTimeout(() => {
                    Livewire.dispatch('updatePosition', {
                        data: {
                            id,
                            width,
                            height,
                        }
                    });
                }, 100)
            }
        }
    });
</script>
@endscript
