<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = '用户管理';// 菜单
    protected static ?string $pluralLabel = '用户'; // 页面标题
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('用户名')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('phone')
                ->label('手机号')
                ->tel()
                ->required()
                ->maxLength(20),

            Forms\Components\TextInput::make('password')
                ->label('登录密码')
                ->password()
                ->revealable()
                ->required(fn (string $operation): bool => $operation === 'create') // 新建必填
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state)) // 编辑时空不提交
                ->maxLength(255),

            Forms\Components\Select::make('status')
                ->label('状态')
                ->options([
                    User::STATUS_ENABLE  => '启用',
                    User::STATUS_DISABLE => '禁用',
                ])
                ->default(1)
                ->required(),

            Forms\Components\TextInput::make('balance')
                ->label('余额')
                ->numeric()
                ->default(0.00)
                ->prefix('¥'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('用户名')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('手机号')->searchable(),
                Tables\Columns\TextColumn::make('balance')->label('余额')->money('CNY', true),
                Tables\Columns\IconColumn::make('status')
                    ->label('状态')
                    ->icon(fn ($state) => $state === User::STATUS_ENABLE
                        ? 'heroicon-o-check-circle'
                        : 'heroicon-o-x-circle'
                    )
                    ->color(fn ($state) => $state === User::STATUS_ENABLE
                        ? 'success'
                        : 'danger'
                    )
                    ->tooltip(fn ($state) => $state === User::STATUS_ENABLE ? '启用' : '禁用'),
                Tables\Columns\TextColumn::make('created_at')->label('创建时间')->dateTime()->sortable(),
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}