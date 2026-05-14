<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToMentorCourses;
use App\Filament\Resources\CertificateResource\Pages;
use App\Models\Certificate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class CertificateResource extends Resource
{
    use RestrictsToMentorCourses;

    protected static ?string $model = Certificate::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Sertifikat';

    protected static ?string $modelLabel = 'Sertifikat';

    protected static ?string $pluralModelLabel = 'Sertifikat';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Penerima')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Siswa')
                            ->relationship('user', 'name')
                            ->searchable(['name', 'email'])
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('course_id')
                            ->label('Kursus')
                            ->relationship('course', 'title')
                            ->searchable(['title'])
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Forms\Components\Section::make('Detail sertifikat')
                    ->schema([
                        Forms\Components\TextInput::make('certificate_number')
                            ->label('Nomor sertifikat')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Misal: CERT-2026-0001')
                            ->helperText('Harus unik. Bisa diisi manual atau dibiarkan default.'),

                        Forms\Components\DateTimePicker::make('issued_at')
                            ->label('Tanggal terbit')
                            ->displayFormat('d M Y H:i')
                            ->native(false)
                            ->seconds(false)
                            ->default(now()),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Forms\Components\Section::make('Berkas')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('File sertifikat')
                            ->disk('public')
                            ->directory('certificates')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf', 'image/png', 'image/jpeg'])
                            ->maxSize(8192)
                            ->downloadable()
                            ->openable()
                            ->helperText('PDF / PNG / JPG, maks 8 MB. Bisa kosong jika menggunakan template otomatis dari kursus.'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('issued_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('certificate_number')
                    ->label('Nomor')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor sertifikat disalin')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Certificate $record): ?string => $record->user?->email),

                TextColumn::make('course.title')
                    ->label('Kursus')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('file_path')
                    ->label('Berkas')
                    ->formatStateUsing(fn (?string $state): string => $state ? 'Lihat berkas' : '—')
                    ->url(fn (Certificate $record): ?string => $record->file_path
                        ? Storage::disk('public')->url($record->file_path)
                        : null)
                    ->openUrlInNewTab()
                    ->color(fn (?string $state): string => $state ? 'primary' : 'gray')
                    ->icon(fn (?string $state): ?string => $state ? 'heroicon-o-arrow-top-right-on-square' : null),

                TextColumn::make('issued_at')
                    ->label('Terbit')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('course_id')
                    ->label('Kursus')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('user_id')
                    ->label('Siswa')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('issued_at')
                    ->label('Tanggal terbit')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari')->native(false),
                        Forms\Components\DatePicker::make('until')->label('Sampai')->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('issued_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('issued_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = 'Dari '.Carbon::parse($data['from'])->translatedFormat('d M Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators[] = 'Sampai '.Carbon::parse($data['until'])->translatedFormat('d M Y');
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn (Certificate $record): bool => filled($record->file_path))
                    ->url(fn (Certificate $record): ?string => $record->file_path
                        ? Storage::disk('public')->url($record->file_path)
                        : null)
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()->label('Edit')->visible(fn (): bool => static::isAdminUser()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Hapus terpilih')->visible(fn (): bool => static::isAdminUser()),
                ]),
            ])
            ->emptyStateHeading('Belum ada sertifikat')
            ->emptyStateDescription('Sertifikat akan diterbitkan setelah pembayaran disetujui dan kursus selesai.');
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
            'index' => Pages\ListCertificates::route('/'),
            'create' => Pages\CreateCertificate::route('/create'),
            'edit' => Pages\EditCertificate::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return static::scopeToMentorCourses(parent::getEloquentQuery());
    }
}
