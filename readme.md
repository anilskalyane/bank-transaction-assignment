# Lumen with JWT Authentication
Basically this is a starter kit for the application to integrate Lumen with [JWT Authentication](https://jwt.io/).

## What's Added

- [Lumen 5.4](https://github.com/laravel/lumen/tree/v5.4.0).
- [JWT Auth](https://github.com/tymondesigns/jwt-auth) for Lumen Application. <sup>[1]</sup>
- [Dingo](https://github.com/dingo/api) to easily and quickly build your own API. <sup>[1]</sup>
- [Lumen Generator](https://github.com/flipboxstudio/lumen-generator) to make development even easier and faster.
- [CORS and Preflight Request](https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS) support.

## Quick Start

- Clone this repo or download it's release archive and extract it somewhere
- You may delete `.git` folder if you get this code via `git clone`
- Run `composer install`
- Run `php artisan jwt:generate`
- Configure your `.env` file for authenticating via database
- Set the `API_PREFIX` parameter in your .env file (usually `api`).
- Run `php artisan migrate --seed`

## A Live PoC

- Run a PHP built in server from your root project:

```sh
php -S localhost:8000 -t public/
```

Or via artisan command:

```sh
php artisan serve
```

To authenticate a user, make a `POST` request to `/api/auth/login` with parameter as mentioned below:

```
email: anilk@example.com
password: anil@2019
```

Note: If you are running API in strict mode then should be pass accept header

```bash
[{"key":"Accept","value":"application/vnd.mini_assignment.v1+json","description":"","enabled":true}]
```

Request:

```sh
curl -X POST -F "email=anilk@example.com" -F "password=anil@2019" "http://localhost:8000/api/auth/login"
```

Response:

```
{
  "success": {
    "message": "token_generated",
    "token": "a_long_token_appears_here"
  }
}
```

- With token provided by above request, you can check authenticated user by sending a `GET` request to: `/api/auth/user`.

Request:

```sh
curl -X GET -H "Authorization: Bearer a_long_token_appears_here" "http://localhost:8000/api/auth/user"
```

Response:

```
{
  "success": {
    "user": {
      "id": 1,
      "name": "Anilkumar Kalyane",
      "email": "anilk@example.com",
      "created_at": null,
      "updated_at": null
    }
  }
}
```
- For customer Transaction details with search and sorting, you can check by sending a `GET` request to: `api/customer/transaction`.

Request:

```sh
curl -X GET http://localhost:8000/api/customer/transaction -H 'Accept: application/vnd.mini_assignment.v1+json' -H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hcGkvYXV0aC9sb2dpbiIsImlhdCI6MTU2MTY2MTE3NSwiZXhwIjoxNTYxNjY0Nzc1LCJuYmYiOjE1NjE2NjExNzUsImp0aSI6InpyVk96RzYzdVVRM1B5TXQifQ.Y6owvscYYnTPTarnxWlDMdalDw6OrzWH4KH9pn6XLX4' -H 'Content-Type: application/json' -H 'Postman-Token: bc79fc36-22e9-4d54-9ecc-868dccf14cc6' -H 'cache-control: no-cache' -d '{ "filters": { "transaction_type":"deposit", "transaction_amount": {"condition":">=", "value":"2000"}, "current_balance": {"condition":">", "value":"590000000"}, "dateFilter": {"from": "2019-06-06", "to": "2019-07-06"}}, "orderBy": [{"type": "transaction_amount","order": "desc"}, {"type": "transaction_type","order": "desc"}]}'
```

Response:

```
{
    "message": "Data processed successfully",
    "data": {
        "current_page": 1,
        "data": [
            {
                "customer_id": 1,
                "account_id": 1,
                "branch_id": 1,
                "transaction_type": "withdrawal",
                "transaction_amount": -1000,
                "other_details": null,
                "created_at": "2019-06-27 08:01:38",
                "updated_at": "2019-06-27 08:13:46",
                "account_details": {
                    "account_number": "20195042931",
                    "account_status": 0,
                    "account_type": 1,
                    "current_balance": 59000,
                    "other_details": null,
                    "created_at": "2019-06-27 07:53:10",
                    "updated_at": "2019-06-27 09:06:46",
                    "account_type_details": {
                        "account_type_code": "saving",
                        "description": "saving",
                        "created_at": "2019-06-27 07:57:58",
                        "updated_at": "2019-06-27 07:57:58"
                    }
                }
            }
        ],
        "from": 1,
        "last_page": 2,
        "next_page_url": "http://localhost:8000/api/customer/transaction?page=2",
        "path": "http://localhost:8000/api/customer/transaction",
        "per_page": "10",
        "prev_page_url": null,
        "to": 10,
        "total": 19
    }
}                
```

- To refresh your token, simply send a `PATCH` request to `/api/auth/refresh`.
- Last but not least, you can also invalidate token by sending a `DELETE` request to `/api/auth/invalidate`.
- To list all registered routes inside your application, you may execute `php artisan route:list`

```
⇒  php artisan route:list
+----------+------------------------------+----------------------+-----------------------------------------------------+----------------------------+-----------------------------------+
| Verb     | Path                         | NamedRoute           | Controller                                          | Action                     | Middleware                        |
+----------+------------------------------+----------------------+-----------------------------------------------------+----------------------------+-----------------------------------+
| POST     | api/auth/login               | api.auth.login       | App\Http\Controllers\Auth\AuthController            | postLogin                  | api.controllers                   |
| GET|HEAD | api/customer                 | api.index            | App\Http\Controllers\APIController                  | getIndex                   | api.controllers|api.auth          |
| GET|HEAD | api/customer/auth/user       | api.auth.user        | App\Http\Controllers\Auth\AuthController            | getUser                    | api.controllers|api.auth          |
| GET|HEAD | api/customer/transaction     | api.auth.transaction | App\Http\Controllers\Customer\TransactionController | getCustomerTranscationData | api.controllers|api.auth|api.auth |
| PATCH    | api/customer/auth/refresh    | api.auth.refresh     | App\Http\Controllers\Auth\AuthController            | patchRefresh               | api.controllers|api.auth          |
| DELETE   | api/customer/auth/invalidate | api.auth.invalidate  | App\Http\Controllers\Auth\AuthController            | deleteInvalidate           | api.controllers|api.auth          |
+----------+------------------------------+----------------------+-----------------------------------------------------+----------------------------+-----------------------------------+
```