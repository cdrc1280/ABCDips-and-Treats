<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatEscalationResource\Pages\EditChatEscalation;
use App\Filament\Resources\ChatEscalationResource\Pages\ListChatEscalations;
use App\Models\ChatEscalation;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ChatEscalationResource extends Resource
{
    protected static ?string $model = ChatEscalation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Engagement & Content';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Client Support Escalations';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'open')->count() ?: null;
    }

    public static function renderConversationHtml(?ChatEscalation $record): HtmlString
    {
        if (!$record || empty($record->conversation)) {
            return new HtmlString('<div class="text-sm text-gray-500 italic p-4 text-center">No messages in conversation.</div>');
        }

        $html = '<div class="space-y-4 max-h-[500px] overflow-y-auto p-4 bg-gray-50 dark:bg-[#140D09] rounded-2xl border border-gray-200 dark:border-[#C08E5D]/30">';
        $clientName = e($record->guest_name ?? $record->user?->name ?? 'Client');

        foreach ($record->conversation as $msg) {
            $role = $msg['role'] ?? 'user';
            $content = e($msg['content'] ?? '');
            $time = e($msg['time'] ?? '');

            if ($role === 'admin') {
                $html .= "
                <div class='flex justify-end mb-3'>
                    <div class='max-w-[80%] bg-[#5C3A22] text-[#FBF3E7] p-3.5 rounded-2xl rounded-tr-sm shadow-sm border border-[#C08E5D]/30'>
                        <div class='flex items-center justify-between gap-3 text-[11px] font-bold text-[#E2C08A] mb-1 pb-1 border-b border-[#E2C08A]/20'>
                            <span>🛡️ Support Agent (Admin)</span>
                            <span>{$time}</span>
                        </div>
                        <p class='text-xs leading-relaxed whitespace-pre-line'>{$content}</p>
                    </div>
                </div>";
            } elseif ($role === 'assistant') {
                $html .= "
                <div class='flex justify-start mb-3'>
                    <div class='max-w-[80%] bg-[#F5E8D0] dark:bg-[#2A1C13] text-[#1C1410] dark:text-[#FBF3E7] p-3.5 rounded-2xl rounded-tl-sm shadow-sm border border-[#C08E5D]/20'>
                        <div class='flex items-center justify-between gap-3 text-[11px] font-bold text-[#5C3A22] dark:text-[#E2C08A] mb-1 pb-1 border-b border-[#C08E5D]/20'>
                            <span>🧁 Dips AI Helper</span>
                            <span>{$time}</span>
                        </div>
                        <p class='text-xs leading-relaxed whitespace-pre-line'>{$content}</p>
                    </div>
                </div>";
            } else {
                $html .= "
                <div class='flex justify-start mb-3'>
                    <div class='max-w-[80%] bg-white dark:bg-[#1E1510] text-[#1C1410] dark:text-[#FBF3E7] p-3.5 rounded-2xl rounded-tl-sm shadow-sm border border-gray-300 dark:border-[#C08E5D]/30'>
                        <div class='flex items-center justify-between gap-3 text-[11px] font-bold text-gray-700 dark:text-[#E2C08A] mb-1 pb-1 border-b border-gray-200 dark:border-[#C08E5D]/20'>
                            <span>👤 {$clientName}</span>
                            <span>{$time}</span>
                        </div>
                        <p class='text-xs leading-relaxed whitespace-pre-line'>{$content}</p>
                    </div>
                </div>";
            }
        }

        $html .= '</div>';
        return new HtmlString($html);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client Information & Status')
                    ->columns(3)
                    ->components([
                        TextInput::make('client_name')
                            ->label('Client Name')
                            ->statePath('guest_name')
                            ->readOnly(),

                        TextInput::make('client_email')
                            ->label('Client Email')
                            ->statePath('guest_email')
                            ->readOnly(),

                        Select::make('status')
                            ->label('Escalation Status')
                            ->options([
                                'open' => 'Open 🔴',
                                'in_progress' => 'In Progress 🟡',
                                'resolved' => 'Resolved 🟢',
                            ])
                            ->required(),

                        Textarea::make('admin_notes')
                            ->label('Internal Admin Notes')
                            ->placeholder('Add private notes for support agents...')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Conversational Thread Stream')
                    ->description('Complete live chat history between client, AI assistant, and admin support.')
                    ->components([
                        Placeholder::make('conversation_stream')
                            ->label('')
                            ->content(fn($record) => static::renderConversationHtml($record))
                            ->columnSpanFull(),

                        Textarea::make('new_reply')
                            ->label('Send Reply to Client')
                            ->placeholder('Type your message to reply to the client in real-time...')
                            ->helperText('Saving will automatically send this reply to the customer\'s chat widget and mark status as In Progress.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('client')
                    ->label('Client')
                    ->getStateUsing(fn(ChatEscalation $record) => $record->guest_name ?? $record->user?->name ?? 'Guest')
                    ->description(fn(ChatEscalation $record) => $record->guest_email ?? $record->user?->email ?? 'No email')
                    ->searchable(['guest_name', 'guest_email']),

                TextColumn::make('conversation_count')
                    ->label('Messages')
                    ->getStateUsing(fn(ChatEscalation $record) => count($record->conversation ?? []) . ' messages')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'open' => 'danger',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => ucfirst(str_replace('_', ' ', $state))),

                TextColumn::make('updated_at')
                    ->label('Last Activity')
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->actions([
                Action::make('view_messages')
                    ->label('View Messages 👁️')
                    ->color('info')
                    ->modalHeading(fn(ChatEscalation $record) => 'Conversation History — ' . ($record->guest_name ?? $record->user?->name ?? 'Client'))
                    ->modalContent(fn(ChatEscalation $record) => static::renderConversationHtml($record))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                Action::make('reply')
                    ->label('Reply 💬')
                    ->color('primary')
                    ->form([
                        Textarea::make('reply_content')
                            ->label('Reply Message')
                            ->placeholder('Type your message here...')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (ChatEscalation $record, array $data) {
                        $conv = $record->conversation ?? [];
                        $conv[] = [
                            'role' => 'admin',
                            'content' => trim($data['reply_content']),
                            'time' => now()->format('M j, g:i A'),
                        ];

                        $record->update([
                            'conversation' => $conv,
                            'status' => 'in_progress',
                        ]);

                        Notification::make()
                            ->title('Reply Sent to Client')
                            ->success()
                            ->send();
                    }),

                Action::make('resolve')
                    ->label('Resolve ✅')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (ChatEscalation $record) {
                        $conv = $record->conversation ?? [];
                        $conv[] = [
                            'role' => 'admin',
                            'content' => '✅ Escalation marked as resolved by support team.',
                            'time' => now()->format('M j, g:i A'),
                        ];

                        $record->update([
                            'conversation' => $conv,
                            'status' => 'resolved',
                        ]);

                        Notification::make()
                            ->title('Chat Escalation Resolved')
                            ->success()
                            ->send();
                    })
                    ->visible(fn(ChatEscalation $record) => $record->status !== 'resolved'),

                Action::make('reopen')
                    ->label('Re-open 🔄')
                    ->color('warning')
                    ->action(function (ChatEscalation $record) {
                        $record->update(['status' => 'open']);
                        Notification::make()->title('Chat Re-opened')->info()->send();
                    })
                    ->visible(fn(ChatEscalation $record) => $record->status === 'resolved'),

                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChatEscalations::route('/'),
            'edit' => EditChatEscalation::route('/{record}/edit'),
        ];
    }
}
