# UniHosp MVP

MVP full stack do sistema hospitalar **UniHosp**, construído para validação rápida com arquitetura separada, fluxo operacional real e interface premium responsiva.

## Visão Geral

Fluxo principal implementado:

1. Login por perfil (admin, recepção, enfermagem, médico)
2. Busca/cadastro de paciente
3. Emissão de senha
4. Entrada em fila
5. Chamadas e painel TV em tempo real (polling + eventos broadcast)
6. Triagem com sinais vitais e classificação de risco
7. Encaminhamento para atendimento médico
8. Registro de atendimento, CID, conduta e prescrição simples
9. Prontuário básico com linha do tempo
10. Dashboard e relatórios iniciais por período

---

## Stack

### Frontend (`unihosp-frontend`)
- Next.js (App Router)
- TypeScript
- Tailwind CSS
- Componentes no padrão shadcn/ui (Button, Card, Input, Badge, Table etc.)
- React Hook Form + Zod
- Zustand (sessão/autenticação)
- Axios padronizado
- Sonner (toasts)
- Laravel Echo + Reverb (quando configurado)

### Backend (`unihosp-backend`)
- Laravel 12
- API REST modular
- PostgreSQL (produção)
- Redis (cache/queue)
- Sanctum (tokens)
- Spatie Laravel Permission (roles/permissions)
- Eventos de domínio com broadcasting
- Policies + middleware de auditoria

---

## Estrutura de Pastas

```text
UniHosp/
├── unihosp-backend/
│   ├── app/
│   │   ├── Events/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/
│   │   │   ├── Middleware/
│   │   │   ├── Requests/
│   │   │   └── Resources/
│   │   ├── Models/
│   │   ├── Policies/
│   │   └── Services/
│   ├── config/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/
│   ├── .env.example
│   └── Procfile
├── unihosp-frontend/
│   ├── src/
│   │   ├── app/
│   │   ├── components/
│   │   ├── config/
│   │   ├── hooks/
│   │   ├── lib/
│   │   ├── store/
│   │   └── types/
│   ├── .env.example
│   └── Procfile
└── README.md
```

---

## Módulos do MVP

- Autenticação e sessão segura
- Usuários, papéis e permissões
- Dashboard operacional
- Pacientes (CRUD + busca)
- Recepção (cadastro rápido + emissão de senha)
- Filas e senhas com ações operacionais
- Chamadas
- Painéis TV (público e administração)
- Triagem
- Atendimento médico básico
- Prontuário básico
- Relatórios iniciais
- Auditoria básica de ações

---

## Banco de Dados

Tabelas principais implementadas:

- `users`
- `roles`, `permissions` (Spatie)
- `pacientes`
- `convenios`
- `setores`
- `salas`
- `filas`
- `senhas`
- `chamadas`
- `paineis`
- `triagens`
- `atendimentos`
- `prontuarios`
- `evolucoes_medicas`
- `prescricoes`
- `auditoria_logs`

Com índices e soft deletes nos módulos críticos.

---

## Seeders

Seeders implementados:

- `RolesAndPermissionsSeeder`
- `InitialUsersSeeder`
- `SetoresSeeder`
- `SalasSeeder`
- `FilasSeeder`
- `PaineisSeeder`
- `ConveniosSeeder`
- `StatusSeeder`

Usuários iniciais:

- `admin@unihosp.local` / `UniHosp@123`
- `recepcao@unihosp.local` / `UniHosp@123`
- `enfermagem@unihosp.local` / `UniHosp@123`
- `medico@unihosp.local` / `UniHosp@123`

---

## Instalação Local

### 1) Backend

```bash
cd unihosp-backend
cp .env.example .env
composer install
php artisan key:generate
```

Configure PostgreSQL e Redis no `.env`.

```bash
php artisan migrate --seed
php artisan serve
```

Opcional (tempo real/filas):

```bash
php artisan queue:work
php artisan reverb:start
```

### 2) Frontend

```bash
cd unihosp-frontend
cp .env.example .env.local
npm install
npm run dev
```

---

## API (Resumo)

Prefixo: `/api`

- `auth`: login/logout/me
- `dashboard`
- `pacientes`
- `recepcao`
- `filas`
- `senhas`
- `chamadas`
- `paineis` + público `/api/paineis/publico/{slug}`
- `triagem`
- `prontuarios`
- `atendimentos`
- `relatorios`
- `usuarios`
- `lookups`

---

## Tempo Real

Eventos implementados:

- `SenhaCriada`
- `SenhaChamada`
- `SenhaEncaminhada`
- `SenhaFinalizada`
- `PacienteAusente`
- `PainelAtualizado`

No frontend há atualização periódica + assinatura via Echo/Reverb quando variáveis WebSocket estão configuradas.

---

## Segurança e LGPD

- Autenticação por token (Sanctum)
- Autorização por permission middleware + policies
- Validação no frontend (Zod) e backend (Form Requests)
- Middleware de auditoria para ações críticas
- Painel TV sem dados sensíveis completos (modo configurável)

---

## Deploy na Railway

### Backend Service
1. Criar serviço apontando para pasta `unihosp-backend`
2. Configurar variáveis de ambiente do `.env.example`
3. Provisionar PostgreSQL e Redis no Railway
4. Start command (web): usar `Procfile`
5. Rodar migração no deploy:
   - `php artisan migrate --force`
   - `php artisan db:seed --force` (opcional em ambiente inicial)

### Frontend Service
1. Criar serviço apontando para pasta `unihosp-frontend`
2. Configurar variáveis do `.env.example`
3. Build: `npm run build`
4. Start: `npm run start -- --hostname 0.0.0.0 --port $PORT`

---

## Responsividade

Implementação mobile-first com:

- Sidebar desktop + drawer mobile
- Tabelas com `overflow-x-auto`
- Cards alternativos em módulos críticos
- Formulários em uma coluna no mobile e grid no desktop
- Botões e ações com tamanho adequado para toque

---

## Próximos Passos

1. Evoluir prescrição para itens estruturados
2. Incluir assinatura digital e impressão avançada
3. Adicionar testes E2E por fluxo clínico
4. Criar monitor de SLA (tempo de espera por setor/fila)
5. Integrar SSO e trilha de auditoria avançada
