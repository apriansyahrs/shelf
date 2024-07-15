<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetTransferResource\Pages;
use App\Models\Asset;
use App\Models\AssetTransfer;
use App\Models\BusinessEntity;
use App\Models\JobTitle;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                            ->translateLabel()
                            ->options(BusinessEntity::all()->pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set(
                                'letter_number',
                                self::generateLetterNumber(BusinessEntity::find($state), null)
                            ))
                            ->columns(6),
                        TextInput::make('letter_number')
                            ->translateLabel()
                            ->columns(6)
                            ->extraInputAttributes(['readonly' => true]),
                        Select::make('from_user_id')
                            ->relationship('fromUser', 'name')
                            ->required()
                            ->translateLabel()
                            ->reactive()
                            ->searchable()
                            ->options(function () {
                                return User::whereDoesntHave('roles', function ($query) {
                                    $query->where('name', 'super_admin');
                                })->pluck('name', 'id');
                            })
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $set('to_user_id', null);
                                $set('details', null);

                                // Update the asset_id options based on the new from_user_id
                                $fromUserId = $get('from_user_id');
                                $assets = Asset::query();
                                $details = [];

                                if ($fromUserId) {
                                    $user = User::find($fromUserId);
                                    if ($user && $user->hasRole('general_affair')) {
                                        // If the user has 'general_affair' role, only select available assets
                                        $assets->where('is_available', 1);
                                        $details = [['asset_id' => '', 'equipment' => '']]; // reset the details repeater with one empty entry
                                    } else {
                                        // If the user does not have 'general_affair' role, restrict to assets with recipient_id = from_user_id
                                        $assets->where('recipient_id', $fromUserId);
                                        $details = $assets->get()->map(function ($asset) {
                                            return ['asset_id' => $asset->id, 'equipment' => ''];
                                        })->toArray();
                                    }
                                }

                                $set('details', $details);
                            }),
                        Select::make('to_user_id')
                            ->translateLabel()
                            ->options(function (callable $get) {
                                $fromUserId = $get('from_user_id');
                                return User::where('id', '!=', $fromUserId)
                                    ->whereDoesntHave('roles', function ($query) {
                                        $query->where('name', 'super_admin');
                                    })
                                    ->pluck('name', 'id');
                            })
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->translateLabel()
                                    ->required()
                                    ->maxLength(255),
                                Select::make('business_entity_id')
                                    ->options(BusinessEntity::all()->pluck('name', 'id'))
                                    ->translateLabel()
                                    ->searchable(),
                                Select::make('job_title_id')
                                    ->options(JobTitle::all()->pluck('title', 'id'))
                                    ->translateLabel()
                                    ->searchable(),
                            ])->columns(2)
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2),
                Repeater::make('details')
                    ->relationship('details')
                    ->schema([
                        Select::make('asset_id')
                            ->reactive()
                            ->required()
                            ->translateLabel()
                            ->searchable()
                            ->options(function (callable $get) {
                                $fromUserId = $get('../../from_user_id');
                                $selectedAssets = collect($get('../../details'))->pluck('asset_id')->filter()->all();
                                $query = Asset::query();

                                if ($fromUserId) {
                                    $user = User::find($fromUserId);
                                    if ($user && $user->hasRole('general_affair')) {
                                        $query->where('is_available', 1);
                                    } else {
                                        $query->where('recipient_id', $fromUserId);
                                    }
                                }

                                // Exclude already selected assets
                                if (!empty($selectedAssets)) {
                                    $query->whereNotIn('id', $selectedAssets);
                                }

                                return $query->pluck('name', 'id')->toArray();
                            })
                            ->getOptionLabelUsing(function ($value) {
                                return Asset::find($value)?->name;
                            }),
                        TextInput::make('equipment')
                            ->translateLabel(),
                    ])
                    ->columns(2)
                    ->translateLabel()
                    ->required()
                    ->hidden(fn (callable $get) => !$get('from_user_id')) // Hide the repeater when from_user_id is not selected
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('businessEntity.name') // Mengambil nama dari relasi businessEntity
                    ->translateLabel()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'CV.CS' => 'gray',
                        'MKLI' => 'warning',
                        'MAJU' => 'success',
                        'RISM' => 'danger',
                        'TOP' => 'danger',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'primary' => 'BERITA ACARA SERAH TERIMA',
                        'success' => 'BERITA ACARA PENGALIHAN BARANG',
                        'danger' => 'BERITA ACARA PENGEMBALIAN BARANG',
                        'secondary' => 'Unknown Status',
                    ])
                    ->getStateUsing(function ($record) {
                        return $record->status;
                    }),
                TextColumn::make('letter_number')
                    ->translateLabel()
                    ->badge(),
                TextColumn::make('fromUser.name')
                    ->translateLabel()
                    ->badge()
                    ->color('danger')
                    ->searchable(),
                TextColumn::make('toUser.name')
                    ->translateLabel()
                    ->badge()
                    ->color('success')
                    ->searchable(),

                TextColumn::make('created_at')->translateLabel()->dateTime(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('businessEntity')->relationship('businessEntity', 'name')->translateLabel(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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

            // Extract the numeric part from the last letter number
            $lastNumber = $lastTransfer ? (int) preg_replace('/\D/', '', substr($lastTransfer->letter_number, -6)) : 0;
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        }

        // Generate the new letter number
        return "{$format}{$newNumber}";
    }
}
