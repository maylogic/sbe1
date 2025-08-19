<?php

namespace App\Livewire;

use App\Models\Base;
use App\Models\BasePreview;
use App\Models\BasePreviewTextArea;
use App\Models\BaseTextArea;
use DOMDocument;
use Illuminate\Support\Collection;
use Livewire\Component;

class BaseTextAreasEditor extends Component
{
    public $baseId;
    public $isPreview = false;
    public $svgContent;
    /** @var Collection<BaseTextArea> $textAreas */
    public Collection $textAreas;
    protected $listeners = ['updatePosition'];

    public function mount($baseId, $isPreview = false)
    {
        $this->baseId = $baseId;
        $this->isPreview = $isPreview;
        $this->loadSvg();
    }

    public function loadSvg()
    {
        $base = $this->getBaseClass()::with('text_areas')->find($this->baseId);
        $this->textAreas = $base->text_areas;
        if (!$base || !$base->filename) {
            $this->svgContent = 'Svg file is not set yet';
            return;
        }

        $filePath = storage_path('app/public/' . $base->filename);
        $dom = $this->loadSvgFile($filePath);
        $this->resizeSvg($dom);
        foreach ($this->textAreas as $textArea) {
            $rect = $dom->createElementNS('http://www.w3.org/2000/svg', 'rect');
            $rect->setAttribute('data-id', $textArea->id);
            $rect->setAttribute('width', $textArea->width);
            $rect->setAttribute('height', $textArea->height);
            $rect->setAttribute('x', $textArea->left);
            $rect->setAttribute('y', $textArea->top);
            $rect->className = 'draggable';
            $svg = $dom->getElementsByTagName('svg')[0];
            $svg->appendChild($rect);
        }
        $this->svgContent = $this->getSvgAsString($dom);
    }

    private function loadSvgFile(string $filePath): DOMDocument
    {
        if (!file_exists($filePath)) {
            throw new \Exception("SVG file not found: {$filePath}");
        }
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->load($filePath);
        libxml_clear_errors();
        return $dom;
    }

    /**
     * @throws \Exception
     */
    private function resizeSvg(DOMDocument $dom): void
    {
        $svg = $dom->getElementsByTagName('svg')->item(0);
        if (!$svg) {
            throw new \Exception("SVG element not found in the document.");
        }
        $svg->setAttribute('width', '320px');
        $svg->setAttribute('height', '320px');
    }

    private function getSvgAsString(DOMDocument $dom): string
    {
        return $dom->saveXML();
    }

    public function updatePosition($data): void
    {
        $textArea = $this->getBaseTextAreaClass()::find($data['id']);
        if ($textArea) {

            $upd = [];
            foreach (['left', 'top', 'width', 'height'] as $dim) {
                if (!empty($data[$dim])) {
                    $upd[$dim] = $data[$dim];
                }
            }
            $textArea->update($upd);

            $this->loadSvg(); // Перезагружаем SVG
        }
    }

    public function render()
    {
        return view('livewire.base-text-areas-editor');
    }

    public function getBaseClass(): string
    {
        return $this->isPreview ? BasePreview::class : Base::class;
    }

    public function getBaseTextAreaClass(): string
    {
        return $this->isPreview ? BasePreviewTextArea::class : BaseTextArea::class;
    }
}
