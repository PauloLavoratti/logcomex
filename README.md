## Primeiros Passos

```bash
./vendor/bin/sail artisan migrate
```

> Antes de executar, configure o `.env` com os dados corretos de banco e demais serviços.

## Usuários do Sistema

| Ambiente | Email            | Senha     |
|----------|------------------|-----------|
| Aplicação | `admin@example.com` | `password` |

> Ajuste conforme seus seeds/factories. Caso tenha criado outros usuários via `php artisan tinker` ou teste, inclua-os aqui.

## Acesso ao MySQL

| Configuração | Valor         |
|--------------|---------------|
| Host         | `mysql`       |
| Porta        | `3306`        |
| Database     | `laravel`     |
| Usuário      | `sail`        |
| Senha        | `password`    |

> Utilize os mesmos dados definidos no `.env` (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`). Se estiver usando Docker/Compose, adapte para o container (`mysql` ou o nome do serviço correspondente).
