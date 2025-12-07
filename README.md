# Sistema de Equivalência de Estudos

# Configuração e Execução do Projeto

## Pré-requisitos

- PHP 8.0 ou superior
- Composer
- Node.js (16.x ou superior) e npm
- MySQL
- Git

## Instalação

#### 1. Clone o repositório:
   ```bash
   git clone https://github.com/ime-usp-br/requerimentos.git
   cd requerimentos
   ```

#### 2. Ative as extensões do PHP (normalmente em etc/php/php.ini)
```
extension=curl
extension=gd
extension=iconv
extension=mysqlib
extension=pdo_mysql
extension=pdo_sqlite
extension=sqlite3
extension=zip
extension=pdo_sqlsrv.so
```
Obs.: Para algumas dessas extensões, será necessário baixar bibliotecas específicas no seu OS. 

#### 3. Rode o composer para instalar as dependências 
   ```bash
   composer install
   ```
  
#### 4. Instale as dependências do JavaScript:
   ```bash
   npm install
   ```

#### 5. Copie o arquivo de ambiente e configure as variáveis:
   ```bash
   cp .env.example .env
   ```
   
#### 6. Edite o arquivo `.env` com as configurações do seu banco de dados e outras variáveis de ambiente.

#### 7. Configure as credenciais do Replicado (sistema USP) no arquivo `.env`:
   ```
   REPLICADO_HOST=
   REPLICADO_PORT=
   REPLICADO_DATABASE=
   REPLICADO_USERNAME=
   REPLICADO_PASSWORD=
   REPLICADO_CODUNDCLG=8
   ```
   Solicite as credenciais de acesso ao banco Replicado à STI ou ao responsável pelo projeto.

#### 8. Gere uma chave para a aplicação:
   ```bash
   php artisan key:generate
   ```

#### 9. Execute as migrações para criar as tabelas:
   ```bash
   php artisan migrate
   ```

#### 10. Execute os seeders para popular o banco com dados iniciais:
   ```bash
   php artisan db:seed
   ```

### Para configurar o banco de dados
No terminal digite os seguinte comandos:

```bash
#Loga no mariadb como admin
sudo mariadb 
# No mariadb, cria um database chamado requerimentos 
create database requerimentos; 
# Cria um usuário chamado requerimentos com a senha que você escolheu
grant all privileges on requerimentos.* to 'requerimentos'@'localhost' identified by '<sua senha aqui>';
# Recarrega todos os privilégios do banco de dados
flush privileges  
```

## Execução

Para desenvolvimento local, você pode executar:

1. Servidor Laravel:
   ```bash
   php artisan serve
   ```

2. Compilação dos assets (em outro terminal):
   ```bash
   npm run dev
   ```

A aplicação estará disponível em `http://localhost:8000`.

## Compilação para Produção

Para compilar os assets para produção:

```bash
npm run build
```

---

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
