# news-aggregator

## Dependencies:

- docker [install guide](https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-on-ubuntu-20-04)
- docker-compose [install guide](https://linuxhostsupport.com/blog/how-to-install-and-configure-docker-compose-on-ubuntu-20-04/)
- mysql [install guide](https://www.digitalocean.com/community/tutorials/how-to-install-mysql-on-ubuntu-20-04)
- copy .env.example to .env and set the correct values
  
## Setup and run with docker:

```
docker-compose up -d --build
```

## To stop docker:

```
docker-compose down
```
## Run Migrations

```
docker-compose exec app php artisan migrate
```
## API Documentation Swagger:
http://localhost:8000/api/documentation


## Postman Collections with examples:
https://documenter.getpostman.com/view/46076791/2sB2xBDVva

## to regenerate swagger docs:
```
php artisan l5-swagger:generate
```
## setup and run with composer:
```
composer install
composer serve
```
## check & fix lint: 
```
composer lint
``` 
## run tests:
```
composer test
```
## 
