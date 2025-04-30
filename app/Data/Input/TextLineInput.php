<?php

namespace App\Data\Input;

readonly class TextLineInput
{
    public function __construct(
        public string $productLineDefinitionId,
        public string $value
    )
    {
    }

    public static function parse(array $input): array
    {
        $data = [];
        foreach ($input as $piece) {
            $data[] = new self($piece['productLineDefinitionId'], $piece['value']);
        }
        return $data;
    }

    public function toArray(): array
    {
        return [
            'product_line_definition_id' => $this->productLineDefinitionId,
            'value' => $this->value,
        ];
    }
}
