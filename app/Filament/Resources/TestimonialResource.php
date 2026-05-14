<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToMentorCourses;
use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TestimonialResource extends Resource
{
    use RestrictsToMentorCourses;

    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Testimoni';

    protected static ?string $modelLabel = 'Testimoni';

    protected static ?string $pluralModelLabel = 'Testimoni';

    protected static ?string $navigationGroup = 'Katalog';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Penulis')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Siswa')
                            ->relationship('user', 'name')
                            ->searchable(['name', 'email'])
                            ->preload()
                            ->required()
                            ->disabledOn('edit'),

                        Forms\Components\Select::make('course_id')
                            ->label('Kursus')
                            ->relationship('course', 'title')
                            ->searchable(['title'])
                            ->preload()
                            ->required()
                            ->disabledOn('edit'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Forms\Components\Section::make('Ulasan')
                    ->schema([
                        Forms\Components\Select::make('rating')
                            ->label('Rating')
                            ->options([
                                5 => '★★★★★ (5)',
                                4 => '★★★★☆ (4)',
                                3 => '★★★☆☆ (3)',
                                2 => '★★☆☆☆ (2)',
                                1 => '★☆☆☆☆ (1)',
                            ])
                            ->native(false)
                            ->required()
                            ->default(5),

                        Forms\Components\Textarea::make('comment')
                            ->label('Komentar')
                            ->rows(4)
                            ->maxLength(1000)
                            ->placeholder('Apa pendapat siswa tentang kursus ini?')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                Forms\Components\Section::make('Moderasi')
                    ->schema([
                        Forms\Components\Textarea::make('admin_reply')
                            ->label('Balasan admin')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Balasan resmi yang akan tampil di samping komentar (opsional).')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_visible')
                            ->label('Tampilkan di halaman publik')
                            ->helperText('Matikan untuk menyembunyikan testimoni dari katalog tanpa menghapusnya.')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Testimonial $record): ?string => $record->user?->email),

                TextColumn::make('course.title')
                    ->label('Kursus')
                    ->searchable()
                    ->sortable()
                    ->limit(35)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state === 3 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state).str_repeat('☆', max(0, 5 - $state))),

                TextColumn::make('comment')
                    ->label('Komentar')
                    ->limit(60)
                    ->tooltip(fn ($state): ?string => $state)
                    ->wrap()
                    ->placeholder('—'),

                IconColumn::make('admin_reply')
                    ->label('Dibalas')
                    ->boolean()
                    ->getStateUsing(fn (Testimonial $record): bool => filled($record->admin_reply))
                    ->toggleable(),

                IconColumn::make('is_visible')
                    ->label('Tampil')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('rating')
                    ->label('Rating')
                    ->multiple()
                    ->options([
                        5 => '5 bintang',
                        4 => '4 bintang',
                        3 => '3 bintang',
                        2 => '2 bintang',
                        1 => '1 bintang',
                    ]),

                SelectFilter::make('course_id')
                    ->label('Kursus')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                TernaryFilter::make('is_visible')
                    ->label('Status tampil')
                    ->boolean()
                    ->trueLabel('Hanya yang tampil')
                    ->falseLabel('Hanya yang disembunyikan')
                    ->native(false),

                TernaryFilter::make('admin_reply')
                    ->label('Balasan admin')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah dibalas')
                    ->falseLabel('Belum dibalas')
                    ->native(false)
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('admin_reply')->where('admin_reply', '!=', ''),
                        false: fn ($query) => $query->where(fn ($q) => $q->whereNull('admin_reply')->orWhere('admin_reply', '')),
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('toggleVisibility')
                    ->label(fn (Testimonial $record): string => $record->is_visible ? 'Sembunyikan' : 'Tampilkan')
                    ->icon(fn (Testimonial $record): string => $record->is_visible ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn (Testimonial $record): string => $record->is_visible ? 'gray' : 'success')
                    ->requiresConfirmation()
                    ->visible(fn (): bool => static::isAdminUser())
                    ->action(fn (Testimonial $record) => $record->update(['is_visible' => ! $record->is_visible])),

                Tables\Actions\EditAction::make()->label('Edit')->visible(fn (): bool => static::isAdminUser()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('show')
                        ->label('Tampilkan')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (): bool => static::isAdminUser())
                        ->action(fn ($records) => $records->each->update(['is_visible' => true])),

                    Tables\Actions\BulkAction::make('hide')
                        ->label('Sembunyikan')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(fn (): bool => static::isAdminUser())
                        ->action(fn ($records) => $records->each->update(['is_visible' => false])),

                    Tables\Actions\DeleteBulkAction::make()->label('Hapus terpilih')->visible(fn (): bool => static::isAdminUser()),
                ]),
            ])
            ->emptyStateHeading('Belum ada testimoni')
            ->emptyStateDescription('Testimoni dari siswa akan muncul di sini setelah mereka mengirim ulasan.');
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
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return static::scopeToMentorCourses(parent::getEloquentQuery());
    }
}
