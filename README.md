# Coffee API

API RESTful para controle de consumo de café, desenvolvida em PHP puro seguindo o padrão MVC, com autenticação JWT, versionamento de rotas e boas práticas de arquitetura.

## Tecnologias Utilizadas

- **PHP 8.0+** (sem frameworks)
- **MySQL 5.7** (banco relacional)
- **XAMPP** (Apache + MySQL local)
- **Composer 2.x** (gerenciador de dependências, apenas para JWT)
- **firebase/php-jwt 6.11.1** (autenticação JWT)
- **DBVisualizer (dbvear)** ou outro gerenciador de banco de dados

---

## Estrutura do Projeto

```
coffee-api/
├── app/
│   ├── Controllers/      # Lógica dos endpoints (User, Auth, Drink, Ranking, etc)
│   ├── Core/             # Classes base (Model, Controller, Router, Request, Response, Database, Auth)
│   ├── Models/           # Modelos de entidades (User, ...)
│   ├── Repositories/     # Acesso ao banco (UserRepository, DrinkLogRepository, RankingRepository)
│   ├── Services/         # Regras de negócio (UserService, DrinkService, RankingService)
│   ├── Validators/       # Validações de dados
│   ├── Utils/            # Utilitários (TokenUtil, etc)
├── config/
│   └── database.php      # Configuração de conexão com o banco
├── database/
│   └── schema.sql        # Script SQL para criação das tabelas
├── public/
│   └── index.php         # Front controller
├── routes/
│   └── api.php           # Definição das rotas
├── vendor/               # Dependências do Composer (JWT)
└── README.md             # Este arquivo
```

## Configuração do Ambiente

- **Servidor PHP/MySQL:** Recomenda-se o uso do XAMPP para rodar o Apache e o MySQL localmente.
- **Gerenciador de banco:** DBVisualizer (dbvear) ou outro de sua preferência.
- **Dependências:**
  - Instale o Composer (https://getcomposer.org/)
  - Execute `composer require firebase/php-jwt` na raiz do projeto para instalar a dependência JWT.

## Configuração do Banco de Dados

1. Crie o banco e as tabelas manualmente utilizando o script em `database/schema.sql`.
2. Configure o acesso ao banco em `config/database.php`:
   ```php
   return [
       'dsn' => 'mysql:host=localhost;dbname=coffee_api',
       'user' => 'root',
       'password' => ''
   ];
   ```

## Endpoints Principais

### Usuários
- `POST   /api/v1/users`           — Criação de usuário
- `POST   /api/v1/login`           — Login e geração de token JWT
- `GET    /api/v1/users`           — Listagem de usuários (com paginação opcional)
- `GET    /api/v1/users/{iduser}`  — Consulta de usuário
- `PUT    /api/v1/users/{iduser}`  — Atualização de usuário (apenas o próprio usuário)
- `DELETE /api/v1/users/{iduser}`  — Exclusão de usuário (apenas o próprio usuário)

### Consumo de Café
- `POST   /api/v1/users/{iduser}/drink` — Incrementa o contador de cafés do usuário autenticado
  - **Atenção:** Somente o usuário autenticado pode registrar seu próprio consumo.

### Histórico e Ranking (Opcionais)
- `GET /api/v1/users/{iduser}/drink/history` — Histórico diário de consumo do usuário
- `GET /api/v1/ranking/day?date=YYYY-MM-DD` — Ranking de consumo em um dia específico
- `GET /api/v1/ranking/last-days?days=7`   — Ranking de consumo nos últimos X dias

## Autenticação
- Todos os endpoints protegidos exigem o envio do token JWT no header:
  ```
  Authorization: Bearer <token>
  ```

## Observações Importantes
- O projeto não utiliza frameworks, apenas a biblioteca JWT via Composer.
- Todas as respostas e entradas são em JSON.
- O padrão de arquitetura é MVC, com separação clara entre controllers, services, repositories e models.
- O registro de consumo de café (`/drink`) só pode ser feito pelo próprio usuário autenticado (ownership garantida).
- O banco deve ser criado manualmente usando o script SQL fornecido.
- Recomenda-se o uso do XAMPP para ambiente local e DBVisualizer (dbvear) para gerenciar o banco.

---

