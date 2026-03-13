# UniHosp Backend (Laravel)

API REST do MVP hospitalar UniHosp.

## Executar localmente

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Opcional:

```bash
php artisan queue:work
php artisan reverb:start
```

## Principais módulos da API

- `auth`
- `dashboard`
- `pacientes`
- `recepcao`
- `filas`
- `senhas`
- `chamadas`
- `paineis`
- `triagem`
- `prontuarios`
- `atendimentos`
- `relatorios`
- `usuarios`
- `lookups`

## Deploy Railway

O projeto inclui `Procfile` com processos:

- `web` (HTTP)
- `worker` (fila)
- `reverb` (tempo real)
