<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Utils\UrlUtils;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items'; // должно соответствовать методу в модели Order

    protected static ?string $recordTitleAttribute = 'id'; // можно изменить, например на 'product_id'

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_regular')
                    ->label('Unit Price')
                    ->money('RUB')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->getStateUsing(function ($record) {
                        return $record->quantity * $record->price_regular;
                    })
                    ->money('RUB')
                    ->sortable(),

                Tables\Columns\TextColumn::make('link')
                    ->label('Download PDF')
                    ->html()
                    ->getStateUsing(function ($record) {
                        $url = UrlUtils::getFrontendUrl();
                        return "<a target='_blank' href='{$url}api/order-item-pdf/$record->id.svg'>Download</a>";
                    })
            ])
            ->filters([
                // при необходимости
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
