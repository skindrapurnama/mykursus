<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Support\RawJs;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('registration_id')
                    ->readOnly()
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('amount')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->prefix('Rp')
                    ->placeholder('0')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('payment_method')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('payment_proof')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Select::make('status')
                    ->options([
                        'paid' => 'Terbayar',
                        'pending' => 'Tertunda',
                        'rejected' => 'Gagal',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('paid_at'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration.user.name')->label('User'),
                TextColumn::make('registration.course.title')->label('Course'),
                TextColumn::make('amount')->money('IDR'),

                ImageColumn::make('payment_proof')->label('Bukti'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        'paid' => 'success'
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update(['status' => 'paid']);
                        // \App\Models\Registration::where('id', $record->registration_id)->update(['status' => 'approved']);
                        // sudah di handle paymentobserver
                        // \App\Models\Certificate::create([
                        //     'user_id' => $record->registration->user_id,
                        //     'course_id' => $record->registration->course_id,
                        //     'certificate_number' => 'CERT-' . uniqid(),
                        //     'issued_at' => now(),
                        // ]);
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'paid')
                    ->action(function ($record) {
                        $record->update(['status' => 'rejected']);
                        \App\Models\Registration::where('id', $record->registration_id)->update(['status' => 'rejected']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
