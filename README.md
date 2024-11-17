# Muller's Cupcake Shop 🧁

Uma aplicação web Symfony para uma loja de cupcakes com autenticação de usuários, personalização de produtos e funcionalidade de carrinho de compras.

## Pré-requisitos

- PHP >= 8.0.2
- Composer
- MySQL/MariaDB
- Symfony CLI (opcional, mas recomendado)

## Tecnologias Principais

- Symfony 6.0.*
- Doctrine ORM 2.20
- Twig 2.12|3.0
- Bootstrap 5.3.0
- Font Awesome 6.0.0

## Instalação

1. Clone o repositório
```bash
git clone [seu-repositório-url]
cd cupcake-shop
```

2. Instale as dependências
```bash
composer install
```

3. Configure seu banco de dados no arquivo `.env.local`
```env
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
```

4. Crie o banco de dados e execute as migrações
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. Crie um usuário super admin
```bash
php bin/console app:create-admin
```

6. Inicie o servidor de desenvolvimento Symfony
```bash
symfony server:start
# ou
php -S localhost:8000 -t public/
```

## Funcionalidades

- Autenticação de Usuário (Login/Registro)
- Níveis de Acesso (ROLE_USER, ROLE_ADMIN, ROLE_SUPER_ADMIN)
- Sistema de Carrinho de Compras
- Personalização de Produtos
- Gerenciamento de Produtos (Admin)
- Design Responsivo

## Uso

### Funcionalidades do Cliente
- Registrar/Login para acessar a loja
- Navegar pelos cupcakes disponíveis
- Personalizar cupcakes (sabores, coberturas, decorações)
- Adicionar produtos ao carrinho
- Gerenciar carrinho de compras
- Realizar pedidos

### Funcionalidades do Admin
- Gerenciar produtos (adicionar, editar, excluir)
- Visualizar pedidos
- Gerenciar usuários

## Estrutura do Projeto

```
cupcake-shop/
├── config/                 # Arquivos de configuração
├── public/                # Diretório web root
│   ├── css/              # Arquivos CSS
│   └── uploads/          # Imagens dos produtos
├── src/
│   ├── Controller/       # Controladores
│   ├── Entity/           # Entidades Doctrine
│   ├── Form/             # Tipos de formulários
│   ├── Repository/       # Repositórios Doctrine
│   └── Service/          # Classes de serviço
├── templates/            # Templates Twig
└── .env                  # Configuração de ambiente
```

## Dependências Principais

### Produção
- doctrine/annotations: ^2.0
- doctrine/doctrine-bundle: ^2.13
- doctrine/doctrine-migrations-bundle: ^3.3
- doctrine/orm: ^2.20
- symfony/framework-bundle: 6.0.*
- symfony/security-bundle: 6.0.*
- symfony/form: 6.0.*
- twig/twig: ^2.12|^3.0

### Desenvolvimento
- symfony/maker-bundle: ^1.0
- symfony/web-profiler-bundle: 6.0.*
- symfony/debug-bundle: 6.0.*
- phpunit/phpunit: ^9.5

## Esquema do Banco de Dados

A aplicação utiliza as seguintes entidades principais:
- User (Usuário)
- Product (Produto)
- Cart (Carrinho)
- CartItem (Item do Carrinho)
- Customization (Personalização)
- Order (Pedido)
- OrderItem (Item do Pedido)

## Segurança

- Senhas dos usuários são criptografadas usando o componente de segurança do Symfony
- Proteção CSRF ativada
- Controle de acesso baseado em níveis
- Validação de formulários

## Como Contribuir

1. Faça um fork do repositório
2. Crie sua branch de feature (`git checkout -b feature/NovaFuncionalidade`)
3. Faça commit das suas alterações (`git commit -m 'Adiciona nova funcionalidade'`)
4. Faça push para a branch (`git push origin feature/NovaFuncionalidade`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob licença proprietária.

## Testes

### Configuração do Ambiente de Teste

1. Criar banco de dados de teste:
```bash
php bin/console doctrine:database:create --env=test
```

2. Criar schema do banco de teste:
```bash
php bin/console doctrine:schema:create --env=test
```

3. Carregar fixtures de teste:
```bash
php bin/console doctrine:fixtures:load --env=test --no-interaction
```

### Estrutura dos Testes

```
tests/
├── Controller/
│   ├── CustomizationControllerTest.php  # Testes de customização de produtos
│   └── ProductControllerTest.php        # Testes de gerenciamento de produtos
└── Entity/
    ├── CartTest.php                     # Testes da entidade Cart
    └── ProductTest.php                  # Testes da entidade Product
```

### Dados de Teste

Os testes utilizam dados fornecidos através de fixtures:

- Usuário Regular:
  - Email: test@example.com
  - Senha: test123
  - Role: ROLE_USER

- Usuário Admin:
  - Email: admin@example.com
  - Senha: admin123
  - Role: ROLE_ADMIN

- Produtos de teste:
  - Test Cupcake (R$ 10,00)
  - Another Cupcake (R$ 12,00)

### Executando os Testes

```bash
# Executar todos os testes
php bin/phpunit

# Executar testes de um diretório específico
php bin/phpunit tests/Controller
php bin/phpunit tests/Entity

# Executar um arquivo de teste específico
php bin/phpunit tests/Controller/CustomizationControllerTest.php

# Executar um método de teste específico
php bin/phpunit --filter testCustomizeProductAsAnonymous tests/Controller/CustomizationControllerTest.php
```

### Casos de Teste

1. **Testes de Controller**
   - ProductController:
     - Acesso como usuário anônimo
     - Acesso como usuário autenticado
     - Acesso à página de novo produto
   
   - CustomizationController:
     - Tentativa de customização sem autenticação
     - Customização como usuário autenticado

2. **Testes de Entity**
   - Testes de getters e setters
   - Validação de dados
   - Relacionamentos entre entidades

### Cobertura de Testes

Os testes cobrem:
- Autenticação e autorização
- Acesso a rotas protegidas
- Operações de produto (visualização e customização)
- Comportamento de redirecionamento
- Validações de entidades

### Manutenção dos Testes

Ao fazer alterações no código:
1. Atualizar os testes correspondentes
2. Garantir que todos os testes passem antes do commit
3. Adicionar novos testes para novas funcionalidades
4. Manter os dados de teste atualizados nas fixtures

### Resolução de Problemas

Se os testes falharem:
1. Verificar se o banco de dados de teste existe
2. Recriar o schema do banco de teste
3. Recarregar as fixtures
4. Limpar o cache:
```bash
php bin/console cache:clear --env=test
```