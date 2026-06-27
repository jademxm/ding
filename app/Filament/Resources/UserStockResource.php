<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserStockResource\Pages;
use App\Models\UserStock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserStockResource extends Resource
{
    protected static ?string $model = UserStock::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = '用户股票';
    protected static ?string $pluralLabel = '股票';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('所属用户')
                ->relationship('user', 'name')
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('name')
                ->label('股票名称')
                ->required()
                ->maxLength(50),

            Forms\Components\TextInput::make('code')
                ->label('股票代码')
                ->required(),

            Forms\Components\TextInput::make('min')
                ->label('监控最小值')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('max')
                ->label('监控最大值')
                ->numeric()
                ->required(),

            Forms\Components\Select::make('status')
                ->label('状态')
                ->options([
                    UserStock::STATUS_ENABLE  => '启用',
                    UserStock::STATUS_DISABLE => '禁用',
                ])
                ->default(UserStock::STATUS_ENABLE)
                ->required(),

            Forms\Components\TextInput::make('order')
                ->label('排序')
                ->numeric()
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // 预加载关联，防 N+1
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user'))
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('用户')->searchable(),
                Tables\Columns\TextColumn::make('name')->label('股票名称')->searchable(),
                Tables\Columns\TextColumn::make('code')->label('代码'),
                Tables\Columns\TextColumn::make('min')->label('监控最小值'),
                Tables\Columns\TextColumn::make('max')->label('监控最大值'),
                Tables\Columns\IconColumn::make('status')
                    ->label('状态')
                    ->icon(fn ($state) => $state === UserStock::STATUS_ENABLE
                        ? 'heroicon-o-check-circle'
                        : 'heroicon-o-x-circle'
                    )
                    ->color(fn ($state) => $state === UserStock::STATUS_ENABLE
                        ? 'success'
                        : 'danger'
                    )
                    ->tooltip(fn ($state) => $state === UserStock::STATUS_ENABLE ? '启用' : '禁用'),
                Tables\Columns\TextColumn::make('order')->label('排序')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('添加时间')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options([1 => '启用', 2 => '禁用']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('order', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUserStocks::route('/'),
            'create' => Pages\CreateUserStock::route('/create'),
            'edit'   => Pages\EditUserStock::route('/{record}/edit'),
        ];
    }
}