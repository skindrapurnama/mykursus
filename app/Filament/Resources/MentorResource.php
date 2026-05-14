<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MentorResource\Pages;
use App\Models\Mentor;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MentorResource extends Resource
{
    protected static ?string $model = Mentor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Mentor';

    protected static ?string $modelLabel = 'Mentor';

    protected static ?string $pluralModelLabel = 'Mentor';

    protected static ?string $navigationGroup = 'Manajemen';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Akun pengguna')
                    ->description('Pilih pengguna terdaftar yang akan menjadi mentor. Pengguna otomatis diubah perannya menjadi "Instruktur".')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Pengguna')
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query, ?Mentor $record) => $query->where(function ($q) use ($record) {
                                    $q->whereDoesntHave('mentor');
                                    if ($record) {
                                        $q->orWhere('id', $record->user_id);
                                    }
                                }),
                            )
                            ->searchable(['name', 'email'])
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn (User $user): string => "{$user->name} ({$user->email})")
                            ->helperText('Hanya pengguna yang belum jadi mentor yang muncul.'),
                    ])
                    ->columnSpanFull(),

                Forms\Components\Section::make('Profil mentor')
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->label('Foto')
                            ->image()
                            ->imageEditor()
                            ->avatar()
                            ->disk('public')
                            ->directory('mentors')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('JPG/PNG, maks 2 MB.'),

                        Forms\Components\Textarea::make('bio')
                            ->label('Bio')
                            ->required()
                            ->rows(5)
                            ->maxLength(2000)
                            ->placeholder('Ceritakan latar belakang, pengalaman, dan pendekatan mengajar mentor.')
                            ->columnSpanFull(),

                        Forms\Components\TagsInput::make('expertise')
                            ->label('Keahlian')
                            ->placeholder('Tambahkan keahlian, tekan Enter')
                            ->suggestions(['Laravel', 'PHP', 'React', 'Vue', 'Node.js', 'Python', 'Data Science', 'UI/UX', 'Mobile Development', 'DevOps'])
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('linkedin_url')
                            ->label('URL LinkedIn')
                            ->url()
                            ->prefix('https://')
                            ->placeholder('linkedin.com/in/username')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('years_experience')
                            ->label('Tahun pengalaman')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(60)
                            ->suffix('tahun'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->helperText('Nonaktifkan agar mentor tidak ditampilkan di halaman kursus.')
                            ->default(true)
                            ->required()
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
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->size(48),

                TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Mentor $record): ?string => $record->user?->email),

                TextColumn::make('expertise')
                    ->label('Keahlian')
                    ->badge()
                    ->separator(',')
                    ->color('primary')
                    ->limitList(3)
                    ->expandableLimitedList(),

                TextColumn::make('years_experience')
                    ->label('Pengalaman')
                    ->suffix(' tahun')
                    ->alignCenter()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('courses_count')
                    ->label('Kursus')
                    ->counts('courses')
                    ->alignCenter()
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status aktif')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Edit'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Hapus terpilih'),
                ]),
            ])
            ->emptyStateHeading('Belum ada mentor')
            ->emptyStateDescription('Tambahkan mentor pertama untuk dihubungkan ke kursus.');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMentors::route('/'),
            'create' => Pages\CreateMentor::route('/create'),
            'edit' => Pages\EditMentor::route('/{record}/edit'),
        ];
    }
}
