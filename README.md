# Backend Developer Assessment / KUMU Exam

This API aims to take a list of github usernames and retrieve user data
This project is made using [Lumen](https://lumen.laravel.com). Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs).


## API Endpoints
| Request Type | URI | Description|
| ------ | ------ | ------ |
| POST | /login | User authentication - retrieve API token |
| POST | /register | User registration|
| GET | /github/users/{username} | get user profile, username(s) can be separated by comma 

## Curl Examples

```bash
curl --location --request POST 'http://localhost:8100/login' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "jplacsinto2@gmail.com",
    "password": "password"
}'
```
```bash
curl --location --request POST 'http://localhost:8100/register' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "jplacsinto2@gmail.com",
    "password": "password"
}'
```
```bash
curl --location --request GET 'http://localhost:8100/github/users/mojombo,defunkt,jplacsinto' \
--header 'Authorization: Bearer {API_TOKEN}'
```

## Installation
This project includes docker files, you can setup the project using `docker-compose`

```sh
docker-compose build --no-cache
docker-compose up -d
```
Migrate database
```sh
docker-compose exec app php artisan migrate
```

rename or copy .env.example to .env or run command

```sh
cp .env.example .env
```

Install vendor files

```sh
docker-compose exec app composer install
```

By default, the Docker will expose port 8100, so change this within the `docker-compose.yml` if necessary.
```ssh
http://localhost:8100
```
