<?php

namespace App\Filament\User\Pages;

use App\Models\Post;
use App\Models\PostComment;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReviewQueue extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string $view = 'filament.user.pages.review-queue';

    protected static ?string $navigationLabel = 'Review Queue';

    protected static ?string $title = 'Review Queue';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        $tenantId = session('tenant_id');
        
        if (!$tenantId || !$user) {
            return false;
        }
        
        $role = $user->getTenantRole($tenantId);
        return in_array($role, ['tenant_admin', 'editor']);
    }

    public static function getNavigationBadge(): ?string
    {
        $tenantId = session('tenant_id');
        $count = Post::where('tenant_id', $tenantId)
            ->where('status', 'review')
            ->count();
        
        return $count > 0 ? (string) $count : null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Post::query()
                    ->where('tenant_id', session('tenant_id'))
                    ->where('status', 'review')
                    ->with(['author', 'allComments' => function ($query) {
                        $query->latest();
                    }])
            )
            ->columns([
                Tables\Columns\TextColumn::make('kind')
                    ->label('Type')
                    ->badge()
                    ->colors([
                        'primary' => 'page',
                        'success' => 'news',
                        'warning' => 'guide',
                        'info' => 'program',
                        'danger' => 'job',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('allComments_count')
                    ->counts('allComments')
                    ->label('Comments')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kind')
                    ->options([
                        'page' => 'Page',
                        'news' => 'News',
                        'guide' => 'Guide',
                        'program' => 'Program',
                        'job' => 'Job',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Post $record): string => match($record->kind) {
                        'program' => route('filament.app.resources.programs.edit', $record),
                        'job' => route('filament.app.resources.jobs.edit', $record),
                        default => route('filament.app.resources.posts.edit', $record),
                    })
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('comment')
                            ->label('Approval Comment (Optional)')
                            ->rows(3)
                            ->placeholder('Add any feedback or notes about this approval...'),
                    ])
                    ->action(function (Post $record, array $data) {
                        $record->update(['status' => 'approved']);
                        
                        // Create approval comment if provided
                        if (!empty($data['comment'])) {
                            PostComment::create([
                                'post_id' => $record->id,
                                'user_id' => Auth::id(),
                                'comment' => $data['comment'],
                                'type' => 'approval',
                            ]);
                        }
                        
                        Notification::make()
                            ->title('Content approved')
                            ->body("'{$record->title}' has been approved.")
                            ->success()
                            ->send();
                        
                        // TODO: Send notification to author
                    })
                    ->modalHeading('Approve Content')
                    ->modalDescription('This content will be marked as approved and ready to publish.')
                    ->modalSubmitActionLabel('Approve'),

                Tables\Actions\Action::make('reject')
                    ->label('Request Changes')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('comment')
                            ->label('Feedback for Author')
                            ->required()
                            ->rows(4)
                            ->placeholder('Explain what changes are needed...'),
                    ])
                    ->action(function (Post $record, array $data) {
                        $record->update(['status' => 'draft']);
                        
                        // Create review comment
                        PostComment::create([
                            'post_id' => $record->id,
                            'user_id' => Auth::id(),
                            'comment' => $data['comment'],
                            'type' => 'review',
                        ]);
                        
                        Notification::make()
                            ->title('Changes requested')
                            ->body("Feedback sent to author of '{$record->title}'.")
                            ->success()
                            ->send();
                        
                        // TODO: Send notification to author
                    })
                    ->modalHeading('Request Changes')
                    ->modalDescription('The content will be sent back to the author with your feedback.')
                    ->modalSubmitActionLabel('Send Feedback'),

                Tables\Actions\Action::make('addComment')
                    ->label('Add Comment')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('gray')
                    ->form([
                        Forms\Components\Textarea::make('comment')
                            ->label('Comment')
                            ->required()
                            ->rows(4)
                            ->placeholder('Add a comment or note...'),
                    ])
                    ->action(function (Post $record, array $data) {
                        PostComment::create([
                            'post_id' => $record->id,
                            'user_id' => Auth::id(),
                            'comment' => $data['comment'],
                            'type' => 'internal',
                        ]);
                        
                        Notification::make()
                            ->title('Comment added')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Add Comment')
                    ->modalSubmitActionLabel('Add Comment'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approveAll')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['status' => 'approved']);
                            }
                            
                            Notification::make()
                                ->title('Content approved')
                                ->body(count($records) . ' items have been approved.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading('No content awaiting review')
            ->emptyStateDescription('Content submitted for review will appear here.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check')
            ->defaultSort('updated_at', 'desc')
            ->poll('30s');
    }

    public function getHeading(): string
    {
        return 'Review Queue';
    }

    public function getSubheading(): ?string
    {
        $count = $this->table->getQuery()->count();
        return $count > 0 
            ? "{$count} " . str('item')->plural($count) . " awaiting review"
            : 'No items awaiting review';
    }
}
