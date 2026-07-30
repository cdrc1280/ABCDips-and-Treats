<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPostResource\Pages\EditBlogPost;
use App\Filament\Resources\BlogPostResource\Pages\ListBlogPosts;
use App\Models\BlogPost;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|\UnitEnum|null $navigationGroup = 'Content';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Post Details')->components([
                TextInput::make('title')->required()->live(onBlur: true)
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')->required()->readOnly()->unique(BlogPost::class, 'slug', ignoreRecord: true),
                Textarea::make('excerpt')->rows(3)->columnSpanFull(),
                RichEditor::make('body')->required()->columnSpanFull(),
                FileUpload::make('cover_image')->image()->disk('public')->directory('blog')->columnSpanFull(),
            ]),
            Section::make('Publishing')->components([
                Select::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])
                    ->default('draft')->required(),
                TextInput::make('category')->default('General'),
                DateTimePicker::make('published_at'),
            ]),
            Section::make('SEO')->components([
                TextInput::make('seo_title'),
                Textarea::make('seo_description')->rows(2)->columnSpanFull(),
            ])->columns(1)->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->disk('public')
                    ->label('Cover')
                    ->circular()
                    ->defaultImageUrl(asset('images/logo.png'))
                    ->getStateUsing(function (BlogPost $record) {
                        if ($record->cover_image) {
                            return str_starts_with($record->cover_image, 'http')
                                ? $record->cover_image
                                : asset('storage/' . ltrim($record->cover_image, '/'));
                        }
                        return asset('images/logo.png');
                    }),
                TextColumn::make('title')->searchable()->limit(50)->weight('bold'),
                TextColumn::make('status')->badge()
                    ->colors(['gray' => 'draft', 'success' => 'published', 'warning' => 'archived']),
                TextColumn::make('category'),
                TextColumn::make('published_at')->date()->sortable(),
                TextColumn::make('created_at')->date()->sortable()->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogPosts::route('/'),
            'create' => CreateBlogPost::route('/create'),
            'edit' => EditBlogPost::route('/{record}/edit'),
        ];
    }
}
