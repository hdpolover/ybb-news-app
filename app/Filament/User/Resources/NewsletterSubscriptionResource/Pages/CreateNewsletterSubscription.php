<?php

namespace App\Filament\User\Resources\NewsletterSubscriptionResource\Pages;

use App\Filament\User\Resources\NewsletterSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsletterSubscription extends CreateRecord
{
    protected static string $resource = NewsletterSubscriptionResource::class;
}
