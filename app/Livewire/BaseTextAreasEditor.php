<?php

namespace App\Livewire;

use App\Models\Base;
use App\Models\BaseTextArea;
use DOMDocument;
use Illuminate\Support\Collection;
use Livewire\Component;

class BaseTextAreasEditor extends Component
{
    public $baseId;
    public $svgContent;
    /** @var Collection<BaseTextArea> $textAreas */
    public Collection $textAreas;
    protected $listeners = ['updatePosition'];

    public function mount($baseId)
    {
        $this->baseId = $baseId;
        $this->loadSvg();
    }

    public function loadSvg()
    {
        $base = Base::with('text_areas')->find($this->baseId);
        $this->textAreas = $base->text_areas;
        if (!$base || !$base->filename) {
            $this->svgContent = 'Svg file is not set yet';
            return;
        }

        $filePath = storage_path('app/public/' . $base->filename);
        $dom = $this->loadSvgFile($filePath);
        $this->resizeSvg($dom);
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
        $svg->removeAttribute('width');
        $svg->removeAttribute('height');
    }

    private function getSvgAsString(DOMDocument $dom): string
    {
        return $dom->saveXML();
    }

    public function updatePosition($data)
    {
        $textArea = BaseTextArea::find($data['id']);
        if ($textArea) {
            $textArea->update([
                'left' => $data['left'] ?? $textArea->left,
                'top' => $data['top'] ?? $textArea->top,
                'width' => $data['width'] ?? $textArea->width,
                'height' => $data['height'] ?? $textArea->height,
            ]);

            $this->loadSvg(); // Перезагружаем SVG
        }
    }

    public function render()
    {
        return view('livewire.base-text-areas-editor');
    }
}
