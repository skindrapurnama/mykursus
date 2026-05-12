<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Registration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Pembayaran';

    protected static ?string $modelLabel = 'Pembayaran';

    protected static ?string $pluralModelLabel = 'Pembayaran';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pendaftaran')
                    ->schema([
                        Forms\Components\Select::make('registration_id')
                            ->label('Pendaftaran')
                            ->relationship('registration', 'id')
                            ->getOptionLabelFromRecordUsing(
                                fn (Registration $record): string => "#{$record->id} · ".
                                    ($record->user?->name ?? 'Tanpa nama').' · '.
                                    ($record->course?->title ?? 'Kursus dihapus'),
                            )
                            ->searchable(['id'])
                            ->preload()
                            ->required()
                            ->disabledOn('edit'),
                    ])
                    ->columnSpanFull(),

                Forms\Components\Section::make('Detail pembayaran')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        Forms\Components\Select::make('payment_method')
                            ->label('Metode pembayaran')
                            ->options([
                                'transfer' => 'Transfer bank',
                                'midtrans' => 'Midtrans (online)',
                                'cash' => 'Tunai',
                                'other' => 'Lainnya',
                            ])
                            ->native(false)
                            ->placeholder('Pilih metode'),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Tertunda',
                                'paid' => 'Terbayar (manual)',
                                'approved' => 'Terbayar (Midtrans)',
                                'rejected' => 'Ditolak',
                                'failed' => 'Gagal',
                            ])
                            ->native(false)
                            ->required(),

                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Tanggal pembayaran')
                            ->displayFormat('d M Y H:i')
                            ->native(false)
                            ->seconds(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Forms\Components\Section::make('Bukti & referensi')
                    ->schema([
                        Forms\Components\FileUpload::make('payment_proof')
                            ->label('Bukti pembayaran')
                            ->image()
                            ->imagePreviewHeight('200')
                            ->disk('public')
                            ->directory('payments')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('Unggah screenshot transfer (JPG/PNG, maks 2 MB).'),

                        Forms\Components\TextInput::make('order_id')
                            ->label('Order ID')
                            ->readOnly()
                            ->maxLength(255)
                            ->helperText('Otomatis terisi oleh Midtrans.'),

                        Forms\Components\Textarea::make('snap_token')
                            ->label('Snap Token')
                            ->readOnly()
                            ->rows(2)
                            ->helperText('Diset otomatis oleh integrasi Midtrans.')
                            ->columnSpanFull(),
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

                TextColumn::make('registration.user.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('registration.course.title')
                    ->label('Kursus')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Order ID disalin')
                    ->toggleable(),

                ImageColumn::make('payment_proof')
                    ->label('Bukti')
                    ->disk('public')
                    ->square()
                    ->size(40)
                    ->url(fn ($record): ?string => $record->payment_proof
                        ? Storage::disk('public')->url($record->payment_proof)
                        : null)
                    ->openUrlInNewTab(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid', 'approved' => 'success',
                        'rejected', 'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Tertunda',
                        'paid' => 'Terbayar',
                        'approved' => 'Terbayar (Midtrans)',
                        'rejected' => 'Ditolak',
                        'failed' => 'Gagal',
                        default => ucfirst($state),
                    }),

                TextColumn::make('paid_at')
                    ->label('Dibayar')
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
                SelectFilter::make('status')
                    ->label('Status')
                    ->multiple()
                    ->options([
                        'pending' => 'Tertunda',
                        'paid' => 'Terbayar (manual)',
                        'approved' => 'Terbayar (Midtrans)',
                        'rejected' => 'Ditolak',
                        'failed' => 'Gagal',
                    ]),

                Filter::make('paid_at')
                    ->label('Tanggal pembayaran')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari'),
                        Forms\Components\DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('paid_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('paid_at', '<=', $date),
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
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui pembayaran?')
                    ->modalDescription('Pendaftaran terkait akan otomatis disetujui melalui PaymentObserver.')
                    ->visible(fn ($record): bool => $record->status === 'pending')
                    ->action(function ($record): void {
                        $record->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak pembayaran?')
                    ->modalDescription('Pendaftaran terkait akan ditandai rejected.')
                    ->visible(fn ($record): bool => in_array($record->status, ['paid', 'approved', 'pending'], true))
                    ->action(function ($record): void {
                        $record->update(['status' => 'rejected']);
                        Registration::where('id', $record->registration_id)
                            ->update(['status' => 'rejected']);
                    }),

                Tables\Actions\EditAction::make()->label('Edit'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Hapus terpilih'),
                ]),
            ])
            ->emptyStateHeading('Belum ada pembayaran')
            ->emptyStateDescription('Pembayaran akan muncul di sini setelah siswa mendaftar ke kursus.');
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
