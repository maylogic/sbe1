<?php

namespace App\Data\Input;

readonly class OptionInput
{
    public function __construct(
        public string $optionId,
        public string $optionValueId
    )
    {
    }

    public static function parse(array $input): array
    {
        $data = [];
        foreach ($input as $piece) {
            $data[] = new self($piece['optionId'], $piece['optionValueId']);
        }
        return $data;
    }
}
