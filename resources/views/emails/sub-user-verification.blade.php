@component('mail::message')
# Olá, {{ $subUser->name }}!

Uma nova credencial de acesso foi gerada para você na plataforma da **Rastertech**. Para garantir a segurança dos seus dados, precisamos que você valide seu e-mail antes do primeiro acesso.

@component('mail::button', ['url' => route('customer-sub-users.verify', $subUser->validation_token)])
Validar meu E-mail e Ativar Conta
@endcomponent

**Detalhes do seu acesso:**
- **Usuário:** {{ $subUser->external_username }}
- **Plataforma:** {{ $subUser->platform->name }}

Se você não solicitou este acesso, por favor desconsidere este e-mail.

Atenciosamente,<br>
Equipe de Segurança Rastertech
@endcomponent
