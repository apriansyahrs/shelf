<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetTransferResource\Pages;
use App\Filament\Resources\AssetTransferResource\RelationManagers;
use App\Models\AssetTransfer;
use App\Models\BusinessEntity;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetTransferResource extends Resource
{
    protected static ?string $model = AssetTransfer::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Select::make('business_entity_id')
                            ->label('Business Entity')
                            ->options(BusinessEntity::all()->pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set(
                                'letter_number',
                                self::generateLetterNumber(BusinessEntity::find($state), null)
                            ))
                            ->columns(6),
                        TextInput::make('letter_number')
                            ->label('Letter Number')
                            ->columns(6)
                            ->extraInputAttributes(['readonly' => true]),
                            Select::make('from_user_id')
                            ->relationship('fromUser', 'name')
                            ->required()
                            ->label('From User')
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('to_user_id', null)),
                        Select::make('to_user_id')
                            ->label('To User')
                            ->options(function (callable $get) {
                                $fromUserId = $get('from_user_id');
                                return User::where('id', '!=', $fromUserId)->pluck('name', 'id');
                            })
                            ->required(),
                    ])
                    ->columns(2), // Set columns to 2 to make the layout more compact and user-friendly
                Card::make()
                    ->schema([
                        Repeater::make('details')
                            ->relationship('details')
                            ->schema([
                                Select::make('asset_id')
                                    ->relationship('asset', 'name')
                                    ->required()
                                    ->label('Asset'),
                                TextInput::make('equipment')
                                    ->required()
                                    ->label('Equipment'),
                            ])
                            ->columns(2) // Set columns to 2 to make the repeater more compact
                            ->label('Asset Transfer Details')
                            ->required(),
                    ])
                    ->columns(1), // Set columns to 1 to keep the repeater in a single card
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('letter_number')->label('Letter Number'),
                TextColumn::make('fromUser.name')->label('From User'),
                TextColumn::make('toUser.name')->label('To User'),
                TextColumn::make('created_at')->label('Created At')->dateTime(),
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
            'index' => Pages\ListAssetTransfers::route('/'),
            'create' => Pages\CreateAssetTransfer::route('/create'),
            'edit' => Pages\EditAssetTransfer::route('/{record}/edit'),
        ];
    }


    private static function generateLetterNumber(?BusinessEntity $businessEntity, $newNumber = null): string
    {
        if (!$businessEntity) {
            return '';
        }

        $format = $businessEntity->format;

        if ($newNumber === null) {
            // Ambil nomor terakhir dari AssetTransfer dengan business_entity_id yang sesuai
            $lastTransfer = AssetTransfer::where('business_entity_id', $businessEntity->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $lastNumber = $lastTransfer ? (int) filter_var($lastTransfer->letter_number, FILTER_SANITIZE_NUMBER_INT) : 0;
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        }

        return "{$format}{$newNumber}";
    }
}
