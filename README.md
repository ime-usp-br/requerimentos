# Sistema de Equivalência de Estudos

# Configuração e Execução do Projeto

Este projeto utiliza **Docker** para ambientes de desenvolvimento e produção.

## 📋 Pré-requisitos

- Docker 20.10 ou superior
- Docker Compose 2.0 ou superior
- Git

## 🚀 Ambiente de Desenvolvimento

### Instalação e Configuração

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/ime-usp-br/requerimentos.git
   cd requerimentos
   ```

2. **Copie o arquivo de ambiente de desenvolvimento:**
   ```bash
   cp .env.development.example .env.development
   ```

3. **Configure o `.env.development`:**
   
   Edite o arquivo e ajuste as seguintes variáveis:
   ```env
   APP_KEY=                    # Será gerado no passo 5
   APP_URL=http://localhost:8000

   # Banco de dados (já configurado para Docker)
   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=requerimentos
   DB_USERNAME=requerimentos
   DB_PASSWORD=password
   DB_ROOT_PASSWORD=root_secret

   # Credenciais do Replicado (sistema USP)
   REPLICADO_HOST=
   REPLICADO_PORT=
   REPLICADO_DATABASE=
   REPLICADO_USERNAME=
   REPLICADO_PASSWORD=
   REPLICADO_TRUST_SERVER_CERTIFICATE=true

   # Senha Única USP
   SENHAUNICA_KEY=
   SENHAUNICA_SECRET=
   SENHAUNICA_CALLBACK_ID=
   ```

   **Nota:** Solicite as credenciais do Replicado e Senha Única à STI ou ao responsável pelo projeto.

4. **Inicie o ambiente de desenvolvimento:**
   ```bash
   docker compose -f docker-compose.dev.yml up
   ```

   Isso irá:
   - ✅ Construir a imagem de desenvolvimento
   - ✅ Instalar automaticamente dependências PHP (Composer) e JavaScript (npm)
   - ✅ Iniciar o servidor Laravel em `http://localhost:8000`
   - ✅ Iniciar o Vite dev server com Hot Module Replacement (HMR)
   - ✅ Montar seu código como volume (alterações refletem imediatamente)

5. **Gere a chave da aplicação (primeira vez):**
   ```bash
   docker compose -f docker-compose.dev.yml exec app php artisan key:generate
   ```
   
   **Importante:** Copie a chave gerada e adicione ao seu `.env.development` se necessário.

6. **Execute as migrações e seeders (primeira vez):**
   ```bash
   docker compose -f docker-compose.dev.yml exec app php artisan migrate
   docker compose -f docker-compose.dev.yml exec app php artisan db:seed
   ```

7. **Acesse a aplicação:**
   - **Frontend:** http://localhost:8000
   - **Vite HMR:** http://localhost:5173 (conectado automaticamente)

### Vantagens do Desenvolvimento com Docker

- ✅ **Hot Module Replacement (HMR)**: Alterações React refletem instantaneamente
- ✅ **Ambiente consistente**: Todos os desenvolvedores usam as mesmas versões de PHP, Node.js e extensões
- ✅ **Sem instalação local**: Não precisa instalar PHP 8.2, Composer, Node.js, MySQL ou extensões
- ✅ **Isolamento total**: Não afeta seu sistema operacional
- ✅ **Onboarding rápido**: Novos desenvolvedores iniciam em minutos
- ✅ **Code sync em tempo real**: Edite localmente, veja as mudanças imediatamente no container

### Comandos Úteis - Desenvolvimento

```bash
# Ver logs em tempo real
docker compose -f docker-compose.dev.yml logs -f

# Ver logs apenas do app
docker compose -f docker-compose.dev.yml logs -f app

# Acessar o container
docker compose -f docker-compose.dev.yml exec app sh

# Parar serviços
docker compose -f docker-compose.dev.yml down

# Reiniciar serviços
docker compose -f docker-compose.dev.yml restart

# Rebuild containers (após mudanças no Dockerfile)
docker compose -f docker-compose.dev.yml up -d --build

# Executar comandos Artisan
docker compose -f docker-compose.dev.yml exec app php artisan <comando>

# Executar comandos Composer
docker compose -f docker-compose.dev.yml exec app composer <comando>

# Executar comandos NPM
docker compose -f docker-compose.dev.yml exec app npm <comando>

# Limpar cache do Laravel
docker compose -f docker-compose.dev.yml exec app php artisan cache:clear
docker compose -f docker-compose.dev.yml exec app php artisan config:clear

# Acessar MySQL
docker compose -f docker-compose.dev.yml exec db mysql -u requerimentos -p

# Ver status dos containers
docker compose -f docker-compose.dev.yml ps
```

---

## 🏭 Ambiente de Produção

### Instalação e Configuração

## 🏭 Ambiente de Produção

### Instalação e Configuração

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/ime-usp-br/requerimentos.git
   cd requerimentos
   ```

2. **Copie o arquivo de ambiente de produção:**
   ```bash
   cp .env.production.example .env.production
   ```

3. **Configure o `.env.production` com valores seguros:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=                    # Será gerado no passo 5
   APP_URL=https://seu-dominio.com

   # Banco de dados
   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=requerimentos
   DB_USERNAME=requerimentos
   DB_PASSWORD=SENHA_SEGURA_AQUI        # ⚠️ ALTERE PARA UMA SENHA FORTE
   DB_ROOT_PASSWORD=ROOT_SENHA_AQUI     # ⚠️ ALTERE PARA UMA SENHA FORTE

   # Credenciais do Replicado (produção)
   REPLICADO_HOST=
   REPLICADO_PORT=
   REPLICADO_DATABASE=
   REPLICADO_USERNAME=
   REPLICADO_PASSWORD=

   # Senha Única USP (produção)
   SENHAUNICA_KEY=
   SENHAUNICA_SECRET=
   SENHAUNICA_CALLBACK_ID=

   # Email (configure seu servidor SMTP)
   MAIL_MAILER=smtp
   MAIL_HOST=
   MAIL_PORT=
   MAIL_USERNAME=
   MAIL_PASSWORD=
   ```

4. **Ajuste as credenciais do banco no arquivo de produção para corresponder ao `.env.production`.**

5. **Construa e inicie os containers:**
   ```bash
   docker compose up -d --build
   ```

   Isso irá:
   - ✅ Construir a imagem otimizada para produção
   - ✅ Compilar assets React/Inertia durante o build
   - ✅ Instalar dependências de produção (sem dev-dependencies)
   - ✅ Configurar Nginx + PHP-FPM + Supervisor
   - ✅ Aplicar otimizações de cache do PHP (OPcache)

6. **Gere a chave da aplicação (primeira vez):**
   ```bash
   docker compose exec app php artisan key:generate
   ```

7. **Execute as migrações e seeders:**
   ```bash
   docker compose exec app php artisan migrate
   docker compose exec app php artisan db:seed
   ```

8. **Acesse a aplicação:**
   - A aplicação estará disponível em `http://localhost:8000` ou no domínio configurado.

### Comandos Úteis - Produção

```bash
# Ver logs
docker compose logs -f app

# Acessar o container
docker compose exec app sh

# Reiniciar serviços
docker compose restart

# Parar serviços
docker compose down

# Limpar cache
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# Ver status dos containers
docker compose ps
```

### Estrutura Docker - Produção

O ambiente de produção utiliza uma arquitetura multi-stage otimizada com:
- **PHP 8.2-FPM** com extensões necessárias (pdo_mysql, sqlsrv, pdo_sqlsrv, gd, intl, mbstring, zip, bcmath, opcache)
- **Nginx** como servidor web
- **MySQL 8.0** como banco de dados
- **Node 20** para build dos assets React/Inertia (apenas durante build)
- **Supervisor** para gerenciar PHP-FPM e Nginx
- **OPcache** ativado para máximo desempenho

---

## 📁 Estrutura de Ambientes

Este projeto utiliza arquivos `.env` separados para cada ambiente:

| Arquivo | Uso | Docker Compose | Dockerfile |
|---------|-----|----------------|------------|
| `.env.development.example` | Template para desenvolvimento | `docker-compose.dev.yml` | `Dockerfile.dev` |
| `.env.development` | Desenvolvimento (gitignored) | `docker-compose.dev.yml` | `Dockerfile.dev` |
| `.env.production.example` | Template para produção | `docker-compose.yml` | `Dockerfile` |
| `.env.production` | Produção (gitignored) | `docker-compose.yml` | `Dockerfile` |

### Diferenças entre Ambientes

| Característica | Desenvolvimento | Produção |
|----------------|-----------------|----------|
| **APP_ENV** | `local` | `production` |
| **APP_DEBUG** | `true` | `false` |
| **Assets** | Vite dev server com HMR | Pre-compilados no build |
| **Código** | Montado como volume | Copiado para a imagem |
| **PHP** | CLI com display_errors | FPM otimizado |
| **Servidor Web** | `php artisan serve` | Nginx |
| **Dependências** | Inclui dev-dependencies | Apenas produção |
| **Cache** | Desabilitado | OPcache ativado |
| **Portas** | 8000 (Laravel) + 5173 (Vite) | 8000 (Nginx) |

---

## 🔧 Gerenciamento de Dependências

### Adicionar Pacotes PHP

**Desenvolvimento:**
```bash
docker compose -f docker-compose.dev.yml exec app composer require vendor/package
```

**Produção:** Após adicionar no development, reconstrua a imagem de produção.

### Adicionar Pacotes JavaScript

**Desenvolvimento:**
```bash
docker compose -f docker-compose.dev.yml exec app npm install nome-do-pacote
```

**Produção:** Após adicionar no development, reconstrua a imagem de produção.

---

## 📝 Arquivos de Configuração

- `.env.development` - Configuração de desenvolvimento (gitignored)
- `.env.production` - Configuração de produção (gitignored)
- `.env.development.example` - Template para desenvolvimento
- `.env.production.example` - Template para produção
- `docker-compose.dev.yml` - Configuração Docker para desenvolvimento
- `docker-compose.yml` - Configuração Docker para produção
- `Dockerfile.dev` - Imagem Docker de desenvolvimento
- `Dockerfile` - Imagem Docker de produção
- `vite.config.js` - Configuração do Vite (compatível com Docker)

---

## 📚 Documentação do Projeto

Esta documentação concentra-se exclusivamente nos aspectos de Modelos e Controladores do projeto.

## Models do Projeto

- **Department**  
  Representa os departamentos da instituição, armazenando informações como nome, sigla e outros detalhes relevantes.

- **DepartmentUserRole**  
  Relaciona usuários aos departamentos e define os papéis que eles desempenham dentro de cada departamento.

- **Document**  
  Gerencia documentos recebidos pelo aluno.

- **Event**  
  Registra eventos que ocorrem no sistema, que podem ser usados para notificar mudanças ou registrar atividades.

- **Requisition**  
  Representa os requerimentos de equivalência de estudos feitos pelos alunos, incluindo informações como solicitante, descrição, status e dados relacionados ao processamento.

- **RequisitionsPeriod**  
  Define se o sistema aceita novos requerimentos ou a edição de existentes, apenas para alunos.

- **RequisitionsVersion**  
  Mantém o histórico de versões das requisições, permitindo a rastreabilidade de alterações e revisões.

- **Review**  
  Armazena pareceres dados sobre as requisições, podendo incluir comentários e feedback detalhado.

- **ReviewsVersion**  
  Similar ao RequisitionsVersion, esta model registra as versões ou alterações feitas nos reviews, assegurando a rastreabilidade do processo de avaliação.

- **Role**  
  Define os papéis ou funções dos usuários na aplicação, determinando os níveis de acesso e as permissões concedidas.

- **TakenDisciplines**  
  Registra as disciplinas que os alunos já cursaram.

- **User**  
  Representa os usuários do sistema, armazenando informações pessoais, credenciais de acesso, e outras características necessárias para o funcionamento da aplicação.

## Métodos Públicos dos Controllers

### AdminController
- `admin(Request $request)`: Exibe o painel administrativo e lista usuários com seus papéis, filtrando com base no usuário corrente.
- `getRequisitionPeriodStatus()`: Retorna os status da permissão de criar novos requerimentos ou editar abertos pelos alunos.
- `setRequisitionPeriodStatus(Request $request)`: Atualiza os status.

### DocumentsController
- `view($id)`: Exibe o documento PDF identificado por `$id`, verificando permissões do usuário.

### ListController
- `list()`: Renderiza a página de listagem de requerimentos de acordo com o papel do usuário, aplicando filtros específicos.

### RequisitionController
- `showRequisition($requisitionId)`: Exibe os detalhes de um requerimento, validando o acesso e selecionando ações específicas.
- `newRequisitionGet()`: Renderiza o formulário para criação de um novo requerimento.
- `newRequisitionPost(RequisitionCreationRequest $request)`: Processa a criação de um novo requerimento, realizando transação e salvando registros, documentos e disciplinas.
- `updateRequisitionGet($requisitionId)`: Prepara e renderiza a página para atualização de um requerimento com dados atuais e últimas versões.
- `updateRequisitionPost(RequisitionUpdateRequest $request)`: Processa a atualização de um requerimento existente.
- `sendToDepartment(Request $request)`: Envia o requerimento para análise do departamento responsável.
- `automaticDeferral(Request $request)`: Realiza o deferimento automático de um requerimento.
- `registered(Request $request)`: Marca um requerimento como registrado no sistema Júpiter.
- `exportRequisitionsGet()`: Exibe a página de exportação de requerimentos com opções de filtros.
- `exportRequisitionsPost(Request $request)`: Processa a exportação de requerimentos com base nos filtros selecionados.
- `setRequisitionResult(Request $request)`: Define o resultado de um requerimento (deferido, indeferido, etc.).

### ReviewController
- `reviewerPick($requisitionId)`: Retorna os pareceristas para o requerimento com base no departamento.
- `createReview(Request $request)`: Cria ou atualiza uma review para o requerimento.
- `reviews($requisitionId)`: Renderiza a página com as reviews atribuídas ao requerimento.
- `submit(Request $request)`: Submete a decisão do parecerista, atualizando a review e registrando o evento.

### LoginController
- `redirectToProvider()`: Redireciona para o provedor de autenticação usando Socialite.
- `callbackHandler()`: Trata o retorno do provedor, criando ou atualizando o usuário e efetuando o login.
- `logout()`: Desloga o usuário e redireciona para a página inicial.

### RecordController
- `requisitionRecord($requisitionId)`: Exibe o histórico de eventos para um requerimento específico.
- `requisitionVersion($eventId)`: Exibe detalhes da versão histórica de um requerimento baseado em um evento.

### RoleController
- `addRole(Request $request)`: Adiciona um papel a um usuário, validando os dados e realizando a transação necessária.
- `removeRole(Request $request)`: Remove um papel de um usuário, validando os dados e removendo a associação.
- `switchRole(Request $request)`: Alterna o papel atual do usuário, atualizando suas informações de acordo com o novo papel.
- `listRolesAndDepartments()`: Retorna uma lista de papéis disponíveis (exceto estudante) e departamentos em formato JSON, filtrando conforme o papel do usuário atual.

---

## Estrutura de Recursos (Resources)

O diretório `resources` contém todos os recursos front-end do projeto, organizados em uma hierarquia que facilita a manutenção e escalabilidade. Esta estrutura segue os padrões do Laravel para recursos web.

### Visão Geral

```
resources/
├── css/         # Estilos CSS
├── img/         # Imagens e elementos gráficos
├── js/          # Código JavaScript/React
│   ├── Context/     # Contextos React (state management)
│   ├── Dialogs/     # Componentes de diálogos modais
│   ├── Features/    # Módulos de funcionalidades
│   │   ├── Admin/
│   │   ├── AssignedReviews/
│   │   ├── ExportRequisitions/
│   │   ├── Header/
│   │   ├── RequisitionDetail/
│   │   ├── RequisitionForm/
│   │   └── RequisitionList/
│   ├── Pages/       # Componentes de páginas completas
│   ├── ui/          # Componentes de interface reutilizáveis
│   └── app.jsx      # Ponto de entrada da aplicação React
├── lang/        # Arquivos de tradução
│   └── pt_BR/       # Traduções em português do Brasil
└── views/       # Templates Blade (Laravel)
```

### Responsabilidades dos Componentes em Features

O diretório `Features` contém os principais módulos funcionais da aplicação, cada um responsável por uma área específica do sistema:

- **Admin/**: Componentes para gerenciamento administrativo do sistema
  - Gerenciamento de usuários e seus papéis
  - Configuração de períodos de requerimentos
  - Interface para administradores do sistema

- **AssignedReviews/**: Componentes para a funcionalidade de pareceres
  - Visualização de reviews atribuídos a pareceristas
  - Formulários para submissão de decisões e justificativas
  - Gerenciamento do fluxo de análise de requerimentos

- **ExportRequisitions/**: Componentes para exportação de dados
  - Formulários de filtros para exportação
  - Tabelas de visualização de dados a serem exportados
  - Funcionalidades de exportação para Excel

- **Header/**: Componentes do cabeçalho da aplicação
  - Barra de navegação principal
  - Menu de usuário e troca de papéis
  - Exibição de informações contextuais

- **RequisitionDetail/**: Componentes para visualização detalhada de requerimentos
  - Exibição de informações completas do requerimento
  - Documentos associados e histórico de versões
  - Ações disponíveis conforme o papel do usuário

- **RequisitionForm/**: Componentes para criação e edição de requerimentos
  - Formulários para submissão de informações
  - Upload de documentos
  - Validação de dados e feedback

- **RequisitionList/**: Componentes para listagem de requerimentos
  - Tabelas de visualização com filtros
  - Ações em lote ou individuais para requerimentos
  - Navegação para detalhes ou edição
