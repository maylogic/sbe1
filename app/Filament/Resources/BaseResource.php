<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BaseResource\Pages;
use App\Livewire\BaseTextAreasEditor;
use App\Models\Base;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class BaseResource extends Resource
{
    protected static ?string $model = Base::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $schema = [
            Forms\Components\TextInput::make('label')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('base_preview_id')
                ->preload()
                ->relationship('base_preview', titleAttribute: "label"),
            Forms\Components\FileUpload::make('filename')
                ->preserveFilenames()
                ->required(),

            Forms\Components\Repeater::make('text_areas')->live()
                ->relationship('text_areas')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('id')->disabled(),
                    Forms\Components\TextInput::make('width')
                        ->default('10')
                        ->numeric()->required(),
                    Forms\Components\TextInput::make('height')
                        ->default('10')
                        ->numeric()->required(),
                    Forms\Components\TextInput::make('left')
                        ->default('10')
                        ->numeric()->required(),
                    Forms\Components\TextInput::make('top')
                        ->default('10')
                        ->numeric()->required(),
                    Forms\Components\TextInput::make('lines')
                        ->default('1')
                        ->numeric()->required(),
                    Forms\Components\TextInput::make('first_line')
                        ->default('1')
                        ->numeric()->required(),
                    Forms\Components\ColorPicker::make('text_color')->hex(),
                    Forms\Components\Checkbox::make('inherit_color')
                ])
                ->defaultItems(0)
                ->addActionLabel('Добавить text area'),
        ];
        if ($form->getRecord()) {
            $schema[] =
                Forms\Components\Livewire::make(BaseTextAreasEditor::class, ['baseId' => $form->getRecord()->id]);
        }
        return $form->schema($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->searchable(),
                Tables\Columns\TextColumn::make('filename')
                    ->searchable(),
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBases::route('/'),
        ];
    }
}
