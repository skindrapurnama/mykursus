<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToMentorCourses;
use App\Filament\Resources\RegistrationResource\Pages;
use App\Models\Registration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrationResource extends Resource
{
    use RestrictsToMentorCourses;

    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Pendaftaran';

    protected static ?string $modelLabel = 'Pendaftaran';

    protected static ?string $pluralModelLabel = 'Pendaftaran';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pendaftaran')
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

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Tertunda',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->native(false)
                            ->required()
                            ->default('pending'),
                    ])
                    ->columns(2)
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
                    ->description(fn (Registration $record): ?string => $record->user?->email),

                TextColumn::make('course.title')
                    ->label('Kursus')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Tertunda',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => ucfirst($state),
                    }),

                TextColumn::make('payment.status')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid', 'approved' => 'success',
                        'rejected', 'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'Tertunda',
                        'paid' => 'Terbayar',
                        'approved' => 'Terbayar (Midtrans)',
                        'rejected' => 'Ditolak',
                        'failed' => 'Gagal',
                        null, '' => 'Belum bayar',
                        default => ucfirst((string) $state),
                    }),

                TextColumn::make('created_at')
                    ->label('Didaftarkan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status pendaftaran')
                    ->multiple()
                    ->options([
                        'pending' => 'Tertunda',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),

                SelectFilter::make('course_id')
                    ->label('Kursus')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('user_id')
                    ->label('Siswa')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui pendaftaran?')
                    ->visible(fn (Registration $record): bool => static::isAdminUser() && $record->status === 'pending')
                    ->action(fn (Registration $record) => $record->update(['status' => 'approved'])),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak pendaftaran?')
                    ->visible(fn (Registration $record): bool => static::isAdminUser() && $record->status !== 'rejected')
                    ->action(fn (Registration $record) => $record->update(['status' => 'rejected'])),

                Tables\Actions\EditAction::make()->label('Edit')->visible(fn (): bool => static::isAdminUser()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('exportCsv')
                        ->label('Ekspor CSV terpilih')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records): StreamedResponse => static::streamCsv($records)),

                    Tables\Actions\DeleteBulkAction::make()->label('Hapus terpilih')->visible(fn (): bool => static::isAdminUser()),
                ]),
            ])
            ->emptyStateHeading('Belum ada pendaftaran')
            ->emptyStateDescription('Pendaftaran akan muncul di sini setelah siswa mendaftar ke kursus.');
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
            'index' => Pages\ListRegistrations::route('/'),
            'create' => Pages\CreateRegistration::route('/create'),
            'edit' => Pages\EditRegistration::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return static::scopeToMentorCourses(parent::getEloquentQuery());
    }

    public static function exportQuery(Builder $query): StreamedResponse
    {
        $records = $query->with(['user', 'course', 'payment'])->get();

        return static::streamCsv($records);
    }

    public static function streamCsv(Collection $records): StreamedResponse
    {
        $records->loadMissing(['user', 'course', 'payment']);

        $filename = 'pendaftaran-'.now()->format('Ymd-His').'.csv';

        $headers = [
            'ID',
            'Tanggal Daftar',
            'Nama Siswa',
            'Email Siswa',
            'Kursus',
            'Harga Kursus (Rp)',
            'Status Pendaftaran',
            'Status Pembayaran',
            'Metode Pembayaran',
            'Nominal Bayar (Rp)',
            'Tanggal Bayar',
            'Order ID',
        ];

        $statusLabels = [
            'pending' => 'Tertunda',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        $paymentLabels = [
            'pending' => 'Tertunda',
            'paid' => 'Terbayar',
            'approved' => 'Terbayar (Midtrans)',
            'rejected' => 'Ditolak',
            'failed' => 'Gagal',
        ];

        return new StreamedResponse(function () use ($records, $headers, $statusLabels, $paymentLabels) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, $headers);

            foreach ($records as $record) {
                fputcsv($handle, [
                    $record->id,
                    $record->created_at?->translatedFormat('d M Y H:i'),
                    $record->user?->name,
                    $record->user?->email,
                    $record->course?->title,
                    $record->course?->price !== null ? number_format((float) $record->course->price, 0, ',', '.') : '',
                    $statusLabels[$record->status] ?? $record->status,
                    $paymentLabels[$record->payment?->status] ?? ($record->payment?->status ?? 'Belum bayar'),
                    $record->payment?->payment_method ?? '',
                    $record->payment?->amount !== null ? number_format((float) $record->payment->amount, 0, ',', '.') : '',
                    $record->payment?->paid_at?->translatedFormat('d M Y H:i') ?? '',
                    $record->payment?->order_id ?? '',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
