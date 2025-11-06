<?php

namespace App\Filament\User\Resources\NewsletterSubscriptionResource\Pages;

use App\Filament\User\Resources\NewsletterSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNewsletterSubscription extends EditRecord
{
    protected static string $resource = NewsletterSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
