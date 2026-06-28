<?php

namespace App\Filament\App\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('phone')
                    ->label('手机号')
                    ->placeholder('请输入注册手机号')
                    ->required()
                    ->autofocus()
                    ->autocomplete('tel')
                    ->extraInputAttributes(['tabindex' => 1]),

                $this->getPasswordFormComponent()
                    ->label('密码')
                    ->extraInputAttributes(['tabindex' => 2]),

                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    // ★ 关键：告诉 Laravel Auth 用 phone 字段 attempt
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'phone'    => $data['phone'],
            'password' => $data['password'],
            'status'   => 1,   // 只许启用用户登录（1=启用 2=禁用）
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.phone' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}