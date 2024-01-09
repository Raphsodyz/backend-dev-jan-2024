# Execução do código

Requisitos:
- Php 8.3.1
- Postgres (utilizada versão psql 16.1)
- PostGIS instalado junto ao postrgres (utilizado versão 3.4.1-2 archlinux)

Faça o download do código acima.

Na raiz do diretorio do projeto extraído, execute:
``` php artisan migrate ```

Em seguida execute:
``` php artisan db:seed ```

E agora execute o servidor:
``` php artisan serve ```

O projeto por default fica disponível na porta http://127.0.0.1:8000;

O projeto inclui uma documentacao do swagger para teste das funcionalidades que pode ser acessado em: http://127.0.0.1:8000/api/documentation;
