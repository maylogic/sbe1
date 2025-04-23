<div>
    <h1>Позиции текстовых зон</h1>

    @if ($this->svgContent)
        @if($this->textAreas->count())
            <div id="svg-editor-content">
                {!! $this->svgContent !!}
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
                const left = (parseFloat(target.getAttribute('x')) || 0) + event.dx / 10;
                const top = (parseFloat(target.getAttribute('y')) || 0) + event.dy / 10;

                target.setAttribute('x', left)
                target.setAttribute('y', top)
            },
            end(event) {
                const target = event.target;
                const left = (parseFloat(target.getAttribute('x')) || 0) + event.dx / 10;
                const top = (parseFloat(target.getAttribute('y')) || 0) + event.dy / 10;
                target.setAttribute('x', left)
                target.setAttribute('y', top)
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
                let width = parseFloat(target.getAttribute('width')) || 10;
                let height = parseFloat(target.getAttribute('height')) || 10;

                width += event.deltaRect.width / 10;
                height += event.deltaRect.height / 10;

                target.setAttribute('width', width)
                target.setAttribute('height', height)
            },
            end(event) {
                const target = event.target;
                let width = parseFloat(target.getAttribute('width')) || 10;
                let height = parseFloat(target.getAttribute('height')) || 10;

                width += event.deltaRect.width / 10;
                height += event.deltaRect.height / 10;

                target.setAttribute('width', width)
                target.setAttribute('height', height)

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
