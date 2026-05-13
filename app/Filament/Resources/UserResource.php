<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';

    protected static ?string $navigationGroup = 'Manajemen';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi pengguna')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('role')
                            ->label('Peran')
                            ->options(User::ROLES)
                            ->native(false)
                            ->required()
                            ->default(User::ROLE_STUDENT)
                            ->helperText('Hanya peran "Admin" yang dapat mengakses dashboard.'),

                        Forms\Components\TextInput::make('password')
                            ->label('Kata sandi')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->helperText(fn (string $operation): string => $operation === 'edit'
                                ? 'Kosongkan jika tidak ingin mengubah.'
                                : 'Minimal 8 karakter.'),
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

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        User::ROLE_ADMIN => 'danger',
                        User::ROLE_INSTRUCTOR => 'warning',
                        User::ROLE_STUDENT => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => User::ROLES[$state] ?? ucfirst($state))
                    ->sortable(),

                TextColumn::make('registrations_count')
                    ->label('Pendaftaran')
                    ->counts('registrations')
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('certificates_count')
                    ->label('Sertifikat')
                    ->counts('certificates')
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                TextColumn::make('email_verified_at')
                    ->label('Email terverifikasi')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Belum')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Peran')
                    ->multiple()
                    ->options(User::ROLES),
            ])
            ->actions([
                Tables\Actions\Action::make('makeAdmin')
                    ->label('Jadikan admin')
                    ->icon('heroicon-o-shield-check')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Jadikan pengguna ini admin?')
                    ->modalDescription('Pengguna akan dapat mengakses seluruh dashboard admin.')
                    ->visible(fn (User $record): bool => $record->role !== User::ROLE_ADMIN)
                    ->action(fn (User $record) => $record->update(['role' => User::ROLE_ADMIN])),

                Tables\Actions\EditAction::make()->label('Edit'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus terpilih')
                        ->before(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each(function (User $user) {
                                if ($user->id === auth()->id()) {
                                    throw new \Illuminate\Validation\ValidationException(
                                        validator: validator([], []),
                                        response: response()->json(['message' => 'Tidak dapat menghapus akun sendiri.'], 422)
                                    );
                                }
                            });
                        }),
                ]),
            ])
            ->emptyStateHeading('Belum ada pengguna')
            ->emptyStateDescription('Tambahkan pengguna pertama untuk memulai.');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
