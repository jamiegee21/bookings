<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Enums\DayOfWeek;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $title = 'Weekly schedule';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('day_of_week')
                    ->options(collect(DayOfWeek::cases())->mapWithKeys(fn (DayOfWeek $day) => [$day->value => $day->name])->all())
                    ->required()
                    ->native(false),
                TimePicker::make('start_time')
                    ->required()
                    ->seconds(false),
                TimePicker::make('end_time')
                    ->required()
                    ->seconds(false),
                TimePicker::make('lunch_start')
                    ->seconds(false),
                TimePicker::make('lunch_end')
                    ->seconds(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('day_of_week')
            ->columns([
                TextColumn::make('day_of_week')
                    ->label('Day')
                    ->formatStateUsing(fn (DayOfWeek $state): string => $state->name),
                TextColumn::make('start_time')
                    ->label('Start'),
                TextColumn::make('end_time')
                    ->label('End'),
                TextColumn::make('lunch_start')
                    ->label('Lunch start')
                    ->placeholder('—'),
                TextColumn::make('lunch_end')
                    ->label('Lunch end')
                    ->placeholder('—'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
