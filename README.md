## Instalação

1. Baixe o projeto do Github
2. Na raiz do projeto, verifique no arquivo **.env** os valores **APP_PORT** e **DB_PORT**. Se alguma porta estiver em uso, altere-o.
3. Execute o comando docker no terminal: `docker-compose up -d --build`
4. Crie um banco de dados com o nome `wepayout` ou conforme no arquivo `src/.env` que deve ter sido criado pelo docker.
5. Na primeira vez, talvez seja necessário executar os seguintes comandos:
   Para entrar no terminal do container:
   `docker-compose exec app bash`
   Em seguida execute os comandos:
   ```
   php artisan migrate
   php artisan migrate --env=testing
   ```

## Executar os testes

Para executar os testes, entre com o comando docker no terminal: `docker-compose exec app bash`. Agora rode o comando `php artisan test ` ou `vendor/bin/phpunit`. Certifique-se que exista o arquivo `src/database/database.sqlite` (banco de dados para os testes).

### Observações

As Queue do Laravel já estão prontas para executar. Não precisa fazer nada mais.

Siga documentação da Api:

[https://documenter.getpostman.com/view/163974/UVR4MUoi]()
