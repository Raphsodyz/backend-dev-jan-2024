# Execução do código

Requisitos:
- Php 8.3.1
- Postgres (utilizada versão psql 16.1)
- PostGIS instalado junto ao postrgres (utilizado versão 3.4.1-2 no archlinux)

Faça o download do código acima.

Na raiz do diretorio do projeto extraído, execute:
``` shell
composer install
```
``` shell
npm install
```
``` shell
cp .env.example .env
```
Dentro do projeto, acesse o arquivo database.php com seu editor de código em config/database.php. Na opção default do arquivo, altere para usar o conector de dados do postgres ```pgsql``` ficando como: ```'default' => env('DB_CONNECTION', 'pgsql'),```. Desça para ```connections``` e na opção ```pgsql``` altere as configurações para as do seu postgres local. Com o banco de dados configurado execute:

``` shell
php artisan migrate
```
``` shell
php artisan db:seed
```

E agora execute o servidor:
``` shell
php artisan serve
```

O projeto por default fica disponível na porta http://127.0.0.1:8000;

O projeto inclui uma documentacao do swagger para teste das funcionalidades que pode ser acessado em: http://127.0.0.1:8000/api/documentation;
