# Comércios Locais (PHP + SQLite + Bootstrap)

## Requisitos
- PHP 8.1+ com extensões `pdo_sqlite` e `fileinfo`

## Executar
```bash
php -S localhost:8000 -t public
```
Acesse http://localhost:8000 — login padrão: `admin` / `admin123` (arquivo `storage/users.txt`).

O banco (`storage/database.sqlite`) é criado automaticamente no primeiro acesso.

## Usuários (`storage/users.txt`)
Uma linha por usuário no formato `usuario:senha`. A senha pode estar em texto puro
ou como hash:
```bash
php -r "echo password_hash('minhasenha', PASSWORD_DEFAULT);"
```

## Estrutura
```
bootstrap.php            autoload, config e sessão
routes/web.php           definição das rotas
public/                  front controller (index.php), assets e uploads
app/Core/                Router, Database, Auth, View, Response, Config
app/Controllers/         recebem a requisição e devolvem a view
app/Services/            regras de negócio (validação, upload, login)
app/Repositories/        acesso a dados (SQLite e users.txt)
app/Entities/            Comercio, Usuario
app/Views/               templates PHP + Bootstrap
storage/                 database.sqlite e users.txt
```
