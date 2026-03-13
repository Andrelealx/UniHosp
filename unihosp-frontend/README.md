# UniHosp Frontend (Next.js)

Aplicação web do MVP UniHosp com interface premium, responsiva e integrada à API Laravel.

## Executar localmente

```bash
cp .env.example .env.local
npm install
npm run dev
```

## Rotas principais

- `/login`
- `/dashboard`
- `/pacientes`
- `/recepcao`
- `/filas`
- `/senhas`
- `/chamadas`
- `/triagem`
- `/atendimentos`
- `/prontuarios`
- `/relatorios`
- `/paineis`
- `/usuarios`
- `/painel/[slug]` (modo TV)

## Build

```bash
npm run lint
npm run build
```

## Deploy Railway

O projeto inclui `Procfile` para start em produção:

```text
web: npm run start -- --hostname 0.0.0.0 --port ${PORT}
```
