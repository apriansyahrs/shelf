<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\BusinessEntity;
use App\Models\JobTitle;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        $isSuperAdmin = Auth::user()->hasRole('super_admin');

        return $form
            ->schema([
                Card::make([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Select::make('business_entity_id')
                        ->options(BusinessEntity::all()->pluck('name', 'id'))
                        ->label('Business Entity')
                        ->searchable(),
                    Select::make('job_title_id')
                        ->options(JobTitle::all()->pluck('title', 'id'))
                        ->label('Job Title')
                        ->searchable(),
                ]),
                Card::make([
                    TextInput::make('username')
                        ->maxLength(255)
                        ->visible($isSuperAdmin),
                    TextInput::make('email')
                        ->maxLength(255)
                        ->visible($isSuperAdmin),
                    TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->maxLength(255)
                        ->visible($isSuperAdmin),
                    DateTimePicker::make('email_verified_at')
                        ->label('Email Verified At')
                        ->visible($isSuperAdmin),
                    Select::make('roles')
                        ->label('Roles')
                        ->relationship('roles', 'name')
                        ->searchable()
                        ->visible($isSuperAdmin),
                ])->visible($isSuperAdmin)
            ]);
    }

    public static function table(Table $table): Table
    {
        $isSuperAdmin = Auth::user()->hasRole('super_admin');

        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('businessEntity.name')
                    ->translateLabel('Business Entity')
                    ->badge()
                    ->color(fn ($record) => $record->businessEntity->color)
                    ->getStateUsing(fn ($record) => $record->businessEntity->name ?? null),
                TextColumn::make('jobTitle.title')->translateLabel()->sortable()->searchable(),
                TextColumn::make('roles.name')
                    ->badge()
                    ->visible($isSuperAdmin),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()->hasRole('super_admin')) {
            return $query;
        }

        return $query->whereDoesntHave('roles', function (Builder $query) {
            $query->where('name', 'super_admin');
        });
    }

    public static function getModelLabel(): string
    {
        return __('User');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Users');
    }
}
