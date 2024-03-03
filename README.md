
# Api Adoorei

Este projeto tem como objetivo fornecer uma api para interagir com produtos e venda para o teste de desenvolvedor back-end da empresa Adoorei.

Sobre a arquitetura:

Criei uma classe BaseModel que possui implementação de regras em comum a todos os outros models como por exemplo: validação.
Ela disponibiliza métodos como beforeSave e afterSave para quem implementá-la.
Criei uma camada Service para centralizar a regra de neǵócio da aplicação.
Também criei um controle de transação ao criar a venda, se acontecer algum problema ao salvar o produto, a venda é desefeita.


### Documentação para postman

https://documenter.getpostman.com/view/14197155/2sA2xb6vSF#0130e239-7abb-4386-b18a-66556ba7a3de

### Passo a passo para rodar a Api

Crie o Arquivo .env
```sh
cp .env.example .env
```


Atualize as variáveis de ambiente do arquivo .env
```dosini
APP_NAME="Api"
APP_URL=http://localhost:8989

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=adoorei
DB_USERNAME=root
DB_PASSWORD=root
```


Suba os containers do projeto
```sh
docker-compose up -d
```


Acessar o container
```sh
docker-compose exec app bash
```


Instalar as dependências do projeto
```sh
composer install
```


Gerar a key do projeto Laravel
```sh
php artisan key:generate
```

Gerar a key do projeto Laravel
```sh
php artisan key:generate
```

Executar migrations
```sh
php artisan migrate
```

Executar seed
```sh
php artisan db:seed
```

Executar testes unitários
```sh
php artisan test
```
