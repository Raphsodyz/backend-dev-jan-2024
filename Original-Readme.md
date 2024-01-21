<p align="center">
  <br>
  <img alt="TerraQ logo" width="150" src="https://sigma01.nyc3.digitaloceanspaces.com/terraq/assets/images/logo/logo.png"/>
  <br>
</p>
 
Repositório contendo as instruções do processo seletivo para Desenvolvedor Backend na TerraQ em Jan/24.

# Descrição e objetivos

Este teste foi criado para avaliar suas habilidades no desenvolvimento de uma aplicação backend que integre funcionalidades geoespaciais. O objetivo é criar uma API que interaja com um banco de dados PostgreSQL com a extensão PostGIS, fornecendo endpoints para manipulação e consulta de dados geoespaciais.

Seus objetivos são:

1. Instalar e Configurar PostGIS no PostgreSQL:

    - Instale a extensão PostGIS no PostgreSQL.
    - Configure o ambiente PostgreSQL com PostGIS nas migrations do Laravel. Isto deve ser feito para preparar o banco de dados para operações geoespaciais.


2. Criar Tabelas de Feições Geoespaciais:

    - Tabela `municipios_geometria`: Para armazenar as geometrias dos municípios de SP e MG. Os municípios devem ser criados a partir dos GeoJSONs fornecidos. Esta tabela deve possuir os campos "id", "nome_municipio" e "geom".
    - Tabela `estados_geometria`: Para armazenar as geometrias dos estados de São Paulo (SP) e Minas Gerais (MG). Esta tabela deve ser criada processando os dados dos municípios de cada estado e realizando um join/dissolve para formar a geometria do estado. Esta tabela deve possuir os campos "id", "nome_estado" e "geom".
    - Crie as tabelas estados_geometria e municipios_geometria utilizando os geojson dos [municípios de São Paulo](https://raw.githubusercontent.com/tbrugz/geodata-br/master/geojson/geojs-35-mun.json) e o geojson dos [municípios de Minas Gerais](https://raw.githubusercontent.com/tbrugz/geodata-br/master/geojson/geojs-31-mun.json) como insumos.
    - Tabela `pontos_usuario`: Para armazenar os pontos postados pelos usuários, com campos para "id", "latitude", "longitude", "municipio_id" e "geom".
    - PS: As colunas de geometrias das tabelas criadas devem ser do tipo Geometry e devem ter o nome "geom" e devem estar no SRID 4326.


3. Desenvolver Endpoints Específicos:

    - Consulta por Latitude e Longitude (`/api/localizar-municipio`): Endpoint para receber latitude e longitude e retornar o município correspondente ou erro se não encontrado.
    - CRUD de Pontos (`/api/pontos`): Implemente um CRUD completo para manipular a tabela pontos_usuario, com os métodos POST, GET, PUT, DELETE.

# Avaliação
## Lista de tarefas a serem cumpridas
- [ ] Instalar e Configurar PostGIS no PostgreSQL via migrations do Laravel

- [ ] Criar Tabela `estados_geometria`

- [ ] Criar Tabela `municipios_geometria`

- [ ] Criar Tabela `pontos_usuario`

- [ ] Consulta por Latitude e Longitude (`/api/localizar-municipio`)

- [ ] CRUD de Pontos (`/api/pontos`)

- [ ] Criar um README.md com instruções adicionais de instalação e configuração do projeto

- [ ] Enviar o link do repositório para o e-mail


## Critérios avaliados
* Cumprimento da lista de tarefas;
* Qualidade do Código: Limpeza, eficiência e organização do código.
* Git: Boas práticas e escrita nas mensagens de commits realizados;
* Facilidade de manutenção e extensão das novas funções implementadas;
* Processamento de Dados: Eficácia no manuseio e análise de dados geoespaciais.
* Visualização: Clareza, interatividade e estética da visualização de dados.
* Laravel: Utilização de recursos do framework Laravel para facilitar o desenvolvimento como migrations, models, controllers, rotas, etc.

# Dicas
* Use o estilo de código mais confortável à você;
* Os nomes de variáveis e comentários no código podem ser feitos em português ou inglês;
* Se o código submetido demandar instalações de quaisquer natureza, espera-se que hajam instruções de instalação no README;
* Feito é melhor que perfeito, submeta seus resultados ainda que estejam parcialmente incompletos.
* Neste teste não há pegadinhas ou detalhes escondidos, então não se preocupe em tentar procurá-los.
* O uso de ferramentas de IA como GitHub Copilot e ChatGPT é permitido (*inclusive, incentivado*) mas espera-se que o candidato tenha entendimento do código gerado e saiba explicá-lo, se perguntado.

# Submissão
* Faça uma cópia do repositório, torne-o privado e adicione o usuário **@abreufilho** como colaborador usando o link abaixo:
  * >https://github.com/SEU_USUARIO/backend-dev-jan-2024/settings/access
* Faça as alterações de modo a cumprir as tarefas deste teste, dê os commits necessários e o push final para o seu repositório remoto no GitHub, lembrando de fazê-lo antes do prazo final (22/01/2024 11:59h - Horário de Brasília).
* Lembre-se de utilizar sua conta real do GitHub;
* O tempo de entrega *NÃO* está sendo avaliado, mas envios após a data e hora limite serão desconsiderados.
* Nenhum código desenvolvido neste teste será utilizado em produção/comercialmente pela TerraQ.
* Não adicione requerimentos de configuração de ambiente ou instalações via Docker, parte do teste é que ele funcione na versão 9 do Laravel e com PHP <= 8.1.
* Ao concluir seu último push, envie um e-mail para antonio.abreu@terraq.com.br com cópia (CC) para talita.mendonca@terraq.com.br contendo somente o link para seu repositório no corpo do texto e o título do e-mail com as seguintes especificações ex.:
  >De: **seu_nome@email.com**

  >Para: **antonio.abreu@terraq.com.br**

  >Título: **Teste Backend - [Seu Nome]**

  >Corpo do e-mail: **https://github.com/{SEU_USUARIO}/backend-dev-jan-2024**
   
# Prazo

## Serão considerados pushes realizados até <span style="color:red">**11:59h de 22/01/2024**</span> (segunda-feira)

Todos os candidatos receberão o aviso e acesso à este teste simultaneamente, portanto, todos terão exatamente o mesmo tempo e oportunidade.

# Instruções de instalação/configuração

Para começar, faça um repositório a partir da cópia do original acessando o link (lembre-se de logar com seu usuário GitHub).

> https://github.com/terra-q/backend-dev-jan-2024/generate

Lembre-se de não deixar o repositório público caso não queira que outros candidatos tenham acesso ao seu código.

Após a cópia, clone o repositório localmente:

```
git clone https://github.com/<SEU_USUARIO>/backend-dev-jan-2024.git
```

Instale as dependências do projeto:

```
composer install
npm install
```

Depois copie o arquivo `.env.example` para `.env`:

```
cp .env.example .env
```

Crie um banco de dados temporário para a aplicação com o comando abaixo (ou crie-o manualmente, se preferir):
    
```
sudo -u postgres psql -c "CREATE DATABASE terraq_teste"
```

Depois, gere a chave da aplicação:

```
php artisan key:generate
```

Rode a migração para criar as tabelas no banco de dados:

```
php artisan migrate
```

E por fim, inicie o servidor local da Aplicação:

```
php artisan serve
```

Acesse a aplicação em http://localhost:8000.

Este repositório utiliza Laravel 9, portanto, você pode consultar a documentação oficial em https://laravel.com/docs/9.x para mais informações.
