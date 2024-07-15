<?php

namespace App\Filament\Resources\AssetTransferResource\Pages;

use App\Filament\Resources\AssetTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetTransfer extends CreateRecord
{
    protected static string $resource = AssetTransferResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;
        $toUser = $record->toUser;
        $hasGeneralAffairRole = $toUser->hasRole('general_affair');

        foreach ($record->details as $detail) {
            $asset = $detail->asset;
            $asset->recipient_id = $record->to_user_id;
            $asset->recipient_business_entity_id = $record->business_entity_id;
            $asset->is_available = $hasGeneralAffairRole ? true : false;
            $asset->save();
        }
    }
}
