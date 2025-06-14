<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesRuleResource\Pages;
use App\Filament\Resources\SalesRuleResource\RelationManagers;
use App\Models\SalesRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesRuleResource extends Resource
{
    protected static ?string $model = SalesRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('expression')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('ends_at'),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->default(0),
                Forms\Components\TextInput::make('sort')
                    ->numeric()
                    ->required()
                    ->default(0),
                Forms\Components\Checkbox::make('is_percentage'),
                Forms\Components\Checkbox::make('active'),
                Forms\Components\Checkbox::make('product'),
                Forms\Components\Checkbox::make('cart'),
                Forms\Components\Checkbox::make('shipping')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expression')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->searchable(),
                Tables\Columns\TextColumn::make('sort')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\CheckboxColumn::make('is_percentage'),
                Tables\Columns\CheckboxColumn::make('active'),
                Tables\Columns\CheckboxColumn::make('product'),
                Tables\Columns\CheckboxColumn::make('cart'),
                Tables\Columns\CheckboxColumn::make('shipping'),
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
            'index' => Pages\ManageSalesRules::route('/'),
        ];
    }
}
