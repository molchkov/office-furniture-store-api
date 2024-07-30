# Office furniture store API

## Setup

Make sure to install the dependencies:

```shell
composer install
```

```shell
cp .env.example .env
```

Add this code to your `.env` file:
```dotenv
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

[Laravel Telescope](https://laravel.com/docs/11.x/telescope) was used to debug the application.
To disable it, you can add this code to the `.env` file:

```dotenv
TELESCOPE_ENABLED=false
```

[Running Sail](https://laravel.com/docs/11.x/sail#starting-and-stopping-sail)
```shell
sail up -d
```

[Laravel Encryption](https://laravel.com/docs/11.x/encryption#configuration)
```shell
sail artisan key:generate
```

[Running Migrations](https://laravel.com/docs/11.x/migrations#running-migrations)
```shell
sail artisan migrate
```

[Running Seeders](https://laravel.com/docs/11.x/seeding#running-seeders)
```shell
sail artisan db:seed
```

[Setup Laravel Passport](https://laravel.com/docs/11.x/passport#deploying-passport)
```shell
sail artisan passport:keys
```

[Creating a Password Grant Client](https://laravel.com/docs/11.x/passport#creating-a-password-grant-client)
```shell
sail artisan passport:client --password
```

[Requesting Access Token](https://laravel.com/docs/11.x/passport#requesting-password-grant-tokens)
```shell
curl --request POST \
  --url http://localhost/oauth/token \
  --header 'Content-Type: application/json' \
  --header 'User-Agent: insomnia/9.3.2' \
  --data '{
	"grant_type": "password",
	"client_id": " {Client ID} ",
	"client_secret": " {Client secret} ",
	"username": "test@example.com",
	"password": "password",
	"scope": ""
}'
```

[Requesting Refresh Token](https://laravel.com/docs/11.x/passport#refreshing-tokens)
```shell
curl --request POST \
  --url http://localhost/oauth/token \
  --header 'Content-Type: application/json' \
  --header 'User-Agent: insomnia/9.3.2' \
  --data '{
	"grant_type": "refresh_token",
	"client_id": " {Client ID} ",
	"client_secret": " {Client secret} ",
	"refresh_token": " {The Refresh Token} ",
	"scope": ""
}'
```

## API Documentation

To view the [Swagger](https://github.com/DarkaOnLine/L5-Swagger) API Documentation, go to the url http://localhost/api/documentation

