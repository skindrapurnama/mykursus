<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToMentorCourses;
use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CourseResource extends Resource
{
    use RestrictsToMentorCourses;

    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Kursus';

    protected static ?string $modelLabel = 'Kursus';

    protected static ?string $pluralModelLabel = 'Kursus';

    protected static ?string $navigationGroup = 'Katalog';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi kursus')
                    ->description('Detail dasar kursus yang akan tampil di katalog.')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul kursus')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Misal: Belajar Laravel dari Nol'),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(5)
                            ->maxLength(2000)
                            ->placeholder('Jelaskan materi, target peserta, dan manfaat kursus.')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                Forms\Components\Section::make('Gambar kursus')
                    ->description('Unggah satu atau lebih gambar/banner kursus. Gambar pertama akan dipakai sebagai thumbnail di katalog.')
                    ->schema([
                        Forms\Components\FileUpload::make('images')
                            ->label('Gambar / banner')
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('course-images')
                            ->visibility('public')
                            ->maxFiles(10)
                            ->maxSize(4096)
                            ->panelLayout('grid')
                            ->helperText('Format JPG/PNG/WEBP, maks 4 MB per gambar. Maksimal 10 gambar. Seret untuk mengatur urutan.')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                Forms\Components\Section::make('Harga & kuota')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Harga')
                            ->required()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),

                        Forms\Components\TextInput::make('quota')
                            ->label('Kuota peserta')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Kosongkan untuk tidak terbatas')
                            ->helperText('Jumlah maksimal peserta yang bisa mendaftar.'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Forms\Components\Section::make('Jadwal')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal mulai')
                            ->displayFormat('d M Y')
                            ->native(false),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal selesai')
                            ->displayFormat('d M Y')
                            ->native(false)
                            ->afterOrEqual('start_date'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Forms\Components\Section::make('Sertifikat & komentar')
                    ->schema([
                        Forms\Components\FileUpload::make('certificate_template')
                            ->label('Template sertifikat')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('certificate-templates')
                            ->visibility('public')
                            ->maxSize(4096)
                            ->helperText('Unggah gambar template (JPG/PNG, maks 4 MB). Akan digenerate per siswa saat sertifikat terbit.')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_comment_enabled')
                            ->label('Aktifkan komentar')
                            ->helperText('Izinkan siswa memberi komentar/testimoni di halaman kursus.')
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
                ImageColumn::make('images')
                    ->label('Gambar')
                    ->disk('public')
                    ->square()
                    ->size(48)
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(),

                ImageColumn::make('certificate_template')
                    ->label('Template')
                    ->disk('public')
                    ->square()
                    ->size(48)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('quota')
                    ->label('Kuota')
                    ->numeric()
                    ->sortable()
                    ->placeholder('Tidak terbatas'),

                TextColumn::make('registrations_count')
                    ->label('Peserta')
                    ->counts('registrations')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),

                IconColumn::make('is_comment_enabled')
                    ->label('Komentar')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_comment_enabled')
                    ->label('Komentar aktif')
                    ->boolean()
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->native(false),

                Filter::make('start_date')
                    ->label('Tanggal mulai')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari')->native(false),
                        Forms\Components\DatePicker::make('until')->label('Sampai')->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('start_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat'),
                Tables\Actions\EditAction::make()->label('Edit'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Hapus terpilih'),
                ]),
            ])
            ->emptyStateHeading('Belum ada kursus')
            ->emptyStateDescription('Tambahkan kursus pertama untuk mulai menerima pendaftaran.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Tambah kursus'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CourseResource\RelationManagers\MentorsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (static::isMentorUser()) {
            $query->whereHas('mentors', function (Builder $q): void {
                $q->where('mentors.user_id', auth()->id());
            });
        }

        return $query;
    }
}
