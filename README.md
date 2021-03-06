# football-league

A Symfony 4 based RESTful API which is secured with JWT authentication without FOSRestBundle, Nelmio and Swagger. 

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

```
PHP >=7.0
```

### Installing

```
$ composer install
```

### Generate the SSH keys :

```
$ mkdir config/jwt
$ openssl genrsa -out config/jwt/private.pem -aes256 4096
$ openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

In case first ```openssl``` command forces you to input password use following to get the private key decrypted
``` bash
$ openssl rsa -in config/jwt/private.pem -out config/jwt/private2.pem
$ mv config/jwt/private.pem config/jwt/private.pem-back
$ mv config/jwt/private2.pem config/jwt/private.pem
```

### Environment variables

* Update JWT_PASSPHRASE in .env file 

### Database

```
$ php bin/console doctrine:database:create
$ php bin/console doctrine:schema:create
$ php bin/console doctrine:fixtures:load
```

## Running the tests

```
$ php bin/console server:start
$ php bin/phpunit
```

![alt text](public/readme-phpunit.png)

## TODO

* Find an alternative to LexikBundle
* Use a specific SQLite database for tests

## Authors

* **Christopher Arzur** - *Initial work*