## Instalação

1. Baixe o projeto do Github
2. Na raiz do projeto, verifique no arquivo **.env** os valores **APP_PORT** e **DB_PORT**. Se alguma porta estiver em uso, altere-o.
3. Execute o comando docker no terminal: `docker-compose up -d --build`
4. Crie um banco de dados com o nome `wepayout` ou conforme no arquivo `src/.env` que deve ter sido criado pelo docker.

## Executar os testes

1. Crie um arquivo sqlite em `src/database/database.sqlite`
2. Para executar os testes, entre com o comando docker no terminal: `docker-compose exec app bash`. Agora rode o comando `php artisan test ` ou `vendor/bin/phpunit`.

### Observações

As Queue do Laravel já estão prontas para executar. Não precisa fazer nada mais.

Siga documentação da Api:

[https://documenter.getpostman.com/view/163974/UVR4MUoi]()
