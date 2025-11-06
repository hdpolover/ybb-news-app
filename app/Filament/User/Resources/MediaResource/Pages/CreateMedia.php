<?php

namespace App\Filament\User\Resources\MediaResource\Pages;

use App\Filament\User\Resources\MediaResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Filament::auth()->user();
        $data['tenant_id'] = session('current_tenant_id');
        $data['uploaded_by'] = $user->id;
        $data['disk'] = 'public';
        
        // Ensure mime_type is set - extract from file_name if not already set
        if (empty($data['mime_type']) && !empty($data['file_name'])) {
            $filePath = storage_path('app/public/' . $data['file_name']);
            if (file_exists($filePath)) {
                $data['mime_type'] = mime_content_type($filePath);
            } else {
                // Fallback: guess from extension
                $extension = pathinfo($data['file_name'], PATHINFO_EXTENSION);
                $data['mime_type'] = match(strtolower($extension)) {
                    'jpg', 'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'svg' => 'image/svg+xml',
                    'webp' => 'image/webp',
                    'pdf' => 'application/pdf',
                    'doc' => 'application/msword',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'xls' => 'application/vnd.ms-excel',
                    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'zip' => 'application/zip',
                    default => 'application/octet-stream',
                };
            }
        }
        
        // Ensure size is set
        if (empty($data['size']) && !empty($data['file_name'])) {
            $filePath = storage_path('app/public/' . $data['file_name']);
            if (file_exists($filePath)) {
                $data['size'] = filesize($filePath);
            }
        }
        
        return $data;
    }
}
