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
use Filament\Actions\ViewAction;
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
            return new HtmlString('<div style="padding: 24px; text-align: center; color: #a1a1aa; font-size: 13px; font-style: italic;">No messages in conversation history.</div>');
        }

        $html = '<style>
            .chat-thread-container {
                display: flex;
                flex-direction: column;
                gap: 14px;
                padding: 16px;
                max-height: 520px;
                overflow-y: auto;
                background-color: #18181b;
                border-radius: 16px;
                border: 1px solid rgba(255, 255, 255, 0.1);
                font-family: inherit;
            }
            .chat-msg-group {
                display: flex;
                flex-direction: column;
                max-width: 85%;
            }
            .chat-msg-user {
                align-self: flex-start;
            }
            .chat-msg-ai {
                align-self: flex-start;
            }
            .chat-msg-admin {
                align-self: flex-end;
            }
            .chat-msg-header {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 11px;
                font-weight: 700;
                margin-bottom: 4px;
            }
            .chat-msg-header-user { color: #60a5fa; }
            .chat-msg-header-ai { color: #fbbf24; }
            .chat-msg-header-admin { color: #34d399; justify-content: flex-end; }
            .chat-msg-bubble {
                padding: 12px 16px;
                border-radius: 16px;
                font-size: 13px;
                line-height: 1.5;
                word-break: break-word;
            }
            .chat-msg-bubble-user {
                background-color: #1e293b;
                color: #f8fafc;
                border-top-left-radius: 4px;
                border: 1px solid #334155;
            }
            .chat-msg-bubble-ai {
                background-color: #27272a;
                color: #f4f4f5;
                border-top-left-radius: 4px;
                border: 1px solid #3f3f46;
            }
            .chat-msg-bubble-admin {
                background-color: #064e3b;
                color: #ecfdf5;
                border-top-right-radius: 4px;
                border: 1px solid #047857;
            }
            .chat-msg-bubble p { margin: 0 0 8px 0; }
            .chat-msg-bubble p:last-child { margin: 0; }
            .chat-msg-bubble ul, .chat-msg-bubble ol { margin: 4px 0 8px 18px; padding: 0; }
            .chat-msg-bubble li { margin-bottom: 2px; }
            .chat-msg-bubble strong { color: #ffffff; font-weight: 700; }
            .chat-msg-time {
                font-size: 10px;
                opacity: 0.65;
                font-weight: 400;
            }
        </style>';

        $html .= '<div class="chat-thread-container">';
        $clientName = e($record->guest_name ?? $record->user?->name ?? 'Client');

        foreach ($record->conversation as $msg) {
            $role = $msg['role'] ?? 'user';
            $rawContent = $msg['content'] ?? '';
            $time = e($msg['time'] ?? ($msg['created_at'] ?? ''));

            // Format markdown text to clean HTML
            $parsedHtml = \Illuminate\Support\Str::markdown($rawContent);

            if ($role === 'admin') {
                $html .= "
                <div class='chat-msg-group chat-msg-admin'>
                    <div class='chat-msg-header chat-msg-header-admin'>
                        <span class='chat-msg-time'>{$time}</span>
                        <span>🛡️ Support Agent (Admin)</span>
                    </div>
                    <div class='chat-msg-bubble chat-msg-bubble-admin'>
                        {$parsedHtml}
                    </div>
                </div>";
            } elseif ($role === 'assistant') {
                $html .= "
                <div class='chat-msg-group chat-msg-ai'>
                    <div class='chat-msg-header chat-msg-header-ai'>
                        <span>🧁 Dips AI Helper</span>
                        <span class='chat-msg-time'>{$time}</span>
                    </div>
                    <div class='chat-msg-bubble chat-msg-bubble-ai'>
                        {$parsedHtml}
                    </div>
                </div>";
            } else {
                $html .= "
                <div class='chat-msg-group chat-msg-user'>
                    <div class='chat-msg-header chat-msg-header-user'>
                        <span>👤 {$clientName}</span>
                        <span class='chat-msg-time'>{$time}</span>
                    </div>
                    <div class='chat-msg-bubble chat-msg-bubble-user'>
                        {$parsedHtml}
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
