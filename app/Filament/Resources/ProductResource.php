<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('₽'),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->columnSpanFull(),

                Repeater::make('gallery')
                    ->label('Галерея')
                    ->relationship()
                    ->addActionLabel('Добавить изображение или видео')
                    ->schema([
                        TextInput::make('label')
                            ->label('Название')
                            ->required(),
                        Forms\Components\Checkbox::make('is_video')
                            ->label('Видео'),
                        FileUpload::make('filename')->required(),
                    ])->defaultItems(0),

                Forms\Components\Select::make('designs')
                    ->multiple()
                    ->preload()
                    ->relationship(titleAttribute: "label"),

                Repeater::make('sizes')
                    ->relationship()
                    ->label('Размеры')
                    ->addActionLabel('Добавить размер')
                    ->schema([
                        TextInput::make('label')
                            ->label('Наименование')
                            ->required(),
                        TextInput::make('width')
                            ->numeric()
                            ->label('Ширина (мм)')
                            ->required(),
                        TextInput::make('height')
                            ->numeric()
                            ->label('Высота (мм)')
                            ->required(),
                        TextInput::make('thickness')
                            ->numeric()
                            ->label('Толщина (мм)')
                            ->required(),
                        TextInput::make('price')
                            ->label('Добавочная стоимость')
                            ->required()
                            ->numeric()
                            ->prefix('₽'),
                    ])->defaultItems(0),
                Repeater::make('options')
                    ->label('Опции')
                    ->relationship()
                    ->addActionLabel('Добавить опцию')
                    ->schema([
                        TextInput::make('label')
                            ->label('Имя опции')
                            ->required(),
                        Forms\Components\Checkbox::make('required')
                            ->label('Обязательно'),
                        Repeater::make('items')
                            ->label('Варианты опции')
                            ->addActionLabel('Добавить вариант опции')
                            ->relationship()
                            ->schema([
                                TextInput::make('label')
                                    ->label('Имя варианта')
                                    ->required(),
                                TextInput::make('price')
                                    ->label('Добавочная стоимость')
                                    ->required()
                                    ->numeric()
                                    ->prefix('₽'),
                            ])
                    ])->defaultItems(0),
                Repeater::make('line_definitions')
                    ->relationship()
                    ->label('Текстовые линии')
                    ->addActionLabel('Добавить линию')
                    ->schema([
                        TextInput::make('label')
                            ->label('Имя линии')
                            ->required(),
                        TextInput::make('default_value')
                            ->label('Начальное значение')
                            ->required(),
                        Forms\Components\Checkbox::make('required')
                            ->label('Обязательно'),
                        Forms\Components\Checkbox::make('hidden')
                            ->label('Скрыто изначально'),
                        TextInput::make('max_length')
                            ->label('Максимальная длина')
                            ->required()
                            ->numeric(),
                    ])->defaultItems(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('RUB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
