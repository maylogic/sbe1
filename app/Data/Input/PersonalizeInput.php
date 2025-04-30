<?php

namespace App\Data\Input;

readonly class PersonalizeInput
{
    /**
     * @param string $colorId
     * @param string $fontId
     * @param string $designId
     * @param string $sizeId
     * @param OptionInput[] $options
     * @param TextLineInput[] $textLines
     */
    public function __construct(
        public string $colorId,
        public string $fontId,
        public string $designId,
        public string $sizeId,
        public array  $options,
        public array  $textLines
    )
    {
    }

    public static function parse(array $input): PersonalizeInput
    {
        return new self(
            $input['colorId'],
            $input['fontId'],
            $input['designId'],
            $input['sizeId'],
            OptionInput::parse($input['options']),
            TextLineInput::parse($input['textLines'])
        );
    }

    public function getOptionValueIds(): array
    {
        return array_map(
            fn(OptionInput $optionInput) => (int)$optionInput->optionValueId,
            $this->options
        );
    }

    public function getTextLinesArray(): array
    {
        return array_map(
            fn(TextLineInput $textLineInput) => $textLineInput->toArray(),
            $this->textLines
        );
    }
}
