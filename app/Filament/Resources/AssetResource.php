<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Filament\Resources\AssetResource\RelationManagers;
use App\Models\Asset;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getCategoryOptions()
    {
        $categories = Category::with('children')->get();

        $options = [];
        foreach ($categories as $category) {
            if ($category->children->isNotEmpty()) {
                $subcategories = $category->children->pluck('name', 'id')->toArray();
                $options[$category->name] = $subcategories;
            }
        }

        return $options;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('letter_number')
                    ->translateLabel('Letter Number')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('purchase_date')
                    ->translateLabel('Purchase Date')
                    ->required(),
                Select::make('business_entity_id')
                    ->label(__('Business Entity'))
                    ->relationship('businessEntity', 'name')
                    ->required(),
                TextInput::make('item_name')
                    ->translateLabel('Item Name')
                    ->required()
                    ->maxLength(255),
                Select::make('category_id')
                    ->translateLabel('Category')
                    ->options(self::getCategoryOptions())
                    ->searchable()
                    ->required(),
                TextInput::make('brand')
                    ->translateLabel('Brand')
                    ->required()
                    ->maxLength(255),
                TextInput::make('type')
                    ->translateLabel('Type')
                    ->required()
                    ->maxLength(255),
                TextInput::make('serial_number')
                    ->translateLabel('Serial Number')
                    ->required()
                    ->maxLength(255),
                TextInput::make('imei1')
                    ->translateLabel('IMEI 1')
                    ->maxLength(255),
                TextInput::make('imei2')
                    ->translateLabel('IMEI 2')
                    ->maxLength(255),
                TextInput::make('item_price')
                    ->translateLabel('Item Price')
                    ->required()
                    ->numeric(),
                TextInput::make('inventory_holder_name')
                    ->translateLabel('Inventory Holder Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('inventory_holder_position')
                    ->translateLabel('Inventory Holder Position')
                    ->required()
                    ->maxLength(255),
                TextInput::make('item_location')
                    ->translateLabel('Item Location')
                    ->required()
                    ->maxLength(255),
                TextInput::make('status')
                    ->translateLabel('Status')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('upload_bast')
                    ->translateLabel('Upload BAST')
                    ->disk('public')
                    ->directory('uploads/bast'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('letter_number')->translateLabel('Letter Number')->sortable()->searchable(),
                TextColumn::make('purchase_date')->translateLabel('Purchase Date')->date()->sortable(),
                TextColumn::make('businessEntity.name')->translateLabel('Business Entity')->sortable()->searchable(),
                TextColumn::make('item_name')->translateLabel('Item Name')->sortable()->searchable(),
                TextColumn::make('category.name')->translateLabel('Category')->sortable(),
                TextColumn::make('brand')->translateLabel('Brand')->sortable()->searchable(),
                TextColumn::make('type')->translateLabel('Type')->sortable()->searchable(),
                TextColumn::make('serial_number')->translateLabel('Serial Number')->sortable()->searchable(),
                TextColumn::make('imei1')->translateLabel('IMEI 1')->sortable()->searchable(),
                TextColumn::make('imei2')->translateLabel('IMEI 2')->sortable()->searchable(),
                TextColumn::make('item_price')->translateLabel('Item Price')->sortable()->money('IDR', true),
                TextColumn::make('inventory_holder_name')->translateLabel('Inventory Holder Name')->sortable()->searchable(),
                TextColumn::make('inventory_holder_position')->translateLabel('Inventory Holder Position')->sortable()->searchable(),
                TextColumn::make('item_location')->translateLabel('Item Location')->sortable()->searchable(),
                TextColumn::make('item_age')->translateLabel('Item Age')->sortable()->numeric(),
                TextColumn::make('status')->translateLabel('Status')->sortable()->searchable(),
                TextColumn::make('upload_bast')->translateLabel('Upload BAST'),
                TextColumn::make('created_at')->translateLabel('Created at')->dateTime()->sortable(),
                TextColumn::make('updated_at')->translateLabel('Updated at')->dateTime()->sortable(),
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
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Asset');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Assets');
    }
}
