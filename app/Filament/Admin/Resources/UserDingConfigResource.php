<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserDingConfigResource\Pages;
use App\Models\UserDingConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserDingConfigResource extends Resource
{
    protected static ?string $model = UserDingConfig::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = '钉钉配置';
    protected static ?string $pluralLabel = '钉钉配置';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('所属用户')
                ->relationship('user', 'name')
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('name')
                ->label('配置名称')
                ->required()
                ->maxLength(50),

            Forms\Components\TextInput::make('webhook')
                ->label('Webhook 地址')
                ->url()
                ->required()
                ->columnSpanFull(),

            Forms\Components\TextInput::make('secret')
                ->label('加签密钥')
                ->required()
                ->columnSpanFull(),

            Forms\Components\Select::make('status')
                ->label('状态')
                ->options([
                    UserDingConfig::STATUS_ENABLE  => '启用',
                    UserDingConfig::STATUS_DISABLE => '禁用',
                ])
                ->default(UserDingConfig::STATUS_ENABLE)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user'))
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('用户')->searchable(),
                Tables\Columns\TextColumn::make('name')->label('配置名'),
                Tables\Columns\TextColumn::make('webhook')->label('Webhook')->limit(40),
                Tables\Columns\IconColumn::make('status')
                    ->label('状态')
                    ->icon(fn ($state) => $state === UserDingConfig::STATUS_ENABLE
                        ? 'heroicon-o-check-circle'
                        : 'heroicon-o-x-circle'
                    )
                    ->color(fn ($state) => $state === UserDingConfig::STATUS_ENABLE
                        ? 'success'
                        : 'danger'
                    )
                    ->tooltip(fn ($state) => $state === UserDingConfig::STATUS_ENABLE ? '启用' : '禁用'),
                Tables\Columns\TextColumn::make('created_at')->label('创建时间')->dateTime(),
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
            ->defaultSort('created_at', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUserDingConfigs::route('/'),
            'create' => Pages\CreateUserDingConfig::route('/create'),
            'edit'   => Pages\EditUserDingConfig::route('/{record}/edit'),
        ];
    }
}