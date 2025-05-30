<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FontResource\Pages;
use App\Filament\Resources\FontResource\RelationManagers;
use App\Models\Font;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class FontResource extends Resource
{
    protected static ?string $model = Font::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('system_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('filename_woff2')
                    ->label('Woff2')
                    ->acceptedFileTypes(['font/woff2'])
                    ->preserveFilenames()
                    ->required(),
                Forms\Components\FileUpload::make('filename_ttf')
                    ->preserveFilenames()
                    ->label('TTF'),
                Forms\Components\FileUpload::make('filename_otf')
                    ->preserveFilenames()
                    ->acceptedFileTypes(['font/otf', 'application/otf','font/opentype', 'application/vnd.ms-opentype'])
                    ->label('OTF'),
                Forms\Components\FileUpload::make('preview')
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('filename_woff2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('filename_ttf')
                    ->searchable(),
                Tables\Columns\TextColumn::make('filename_otf')
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
            'index' => Pages\ManageFonts::route('/'),
        ];
    }
}
