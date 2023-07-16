<p style="text-align: center">
  <img src="doc/readme/images/icflogo.png" alt="ICF Logo"/>
</p>

# Backend Trial Task

## Table of contents:

- [Backend Trial Task](#backend-trial-task)
    * [General Description](#general-description)
    * [ARTIC API](#artic-api)
    * [Authentication](#authentication)
    * [Purchasing Artworks](#purchasing-artworks)
    * [Database](#database)
    * [Languages and Frameworks](#languages-and-frameworks)
        + [Node](#node)
        + [Java](#java)
        + [Kotlin](#kotlin)
        + [PHP](#php)
    * [Other Requirements](#other-requirements)
- [Installation](#installation)
    * [Getting files](#getting-files)
    * [Environment](#environment)
    * [Running docker containers](#running-docker-containers)
    * [Database setup](#database-setup)
- [Usage](#usage)
    * [Swagger and Documentation](#swagger-and-documentation)
    * [API Endpoints, Postman and Documentation](#api-endpoints-postman-and-documentation)
        + [Set he token for all requests](#set-he-token-for-all-requests)
        + [Requests](#requests)
            - [Unauthorized request](#unauthorized-request)
            - [Get token for User](#get-token-for-user)
            - [Single Artwork Success](#single-artwork-success)
            - [Non Existent Artwork](#non-existent-artwork)
            - [Pagination](#pagination)
            - [Purchase an artwork](#purchase-an-artwork)
            - [Purchase of sold artwork](#purchase-of-sold-artwork)
            - [Purchase with a user without buy rights](#purchase-with-a-user-without-buy-rights)
            - [Purchase non-existent artwork](#purchase-non-existent-artwork)
            - [Purchase with bad request](#purchase-with-bad-request)
            - [List purchases for a user](#list-purchases-for-a-user)
            - [List purchases with bad email](#list-purchases-with-bad-email)
            - [List purchases with no buy role](#list-purchases-with-no-buy-role)
            - [Token expired](#token-expired)
            - [All other cases](#all-other-cases)
- [Tests](#tests)

<small><i><a href='http://ecotrust-canada.github.io/markdown-toc/'>Table of contents generated with markdown-toc</a></i></small>


## General Description

The task is to implement a REST API that communicates with the API of the Art Institute of
Chicago (ARTIC).

Your API will have endpoints for authentication, retrieval of a single artwork or paginated
artworks, and purchasing artworks.

## ARTIC API

You can take a look at the ARTIC API documentation here:
http://api.artic.edu/docs/#introduction.

This API doesn't use authentication for the endpoints you need to use and does not have
endpoints for buying an artwork either, these features will be implemented only in your API.

## Authentication

Implement authentication using JSON Web Tokens with a 30-min invalidation time.

You don’t need to create processes for registering new users, just seed the database with
users “user1@email.com” and “user2@email.com”. Both users should have the password
“password”.

##Artworks

Implement 2 artwork endpoints, one that can retrieve a single artwork by its ID and another
that can retrieve paginated artworks. You need to provide an option to set the page number
and page size.

An artwork needs to have an ID, title, author, and thumbnail fields if they exist.

## Purchasing Artworks

Implement an endpoint for buying an artwork and another for listing all artworks owned by
the user. Only one user can buy an artwork. You don’t need to implement the checkout
process or add prices to the artworks, the purchase is immediate if the artwork has no owner.

## Database

Use MySQL as your database management system. You should save the purchases in the
database.

## Languages and Frameworks

### Node

* You can implement the task from scratch or using either koa or NestJS frameworks
* Use TypeScript
* Node version: ≥ 14
 
### Java

* Use Spring and Spring Boot
* Java version: ≥ 11

### Kotlin

* Use Spring and Spring Boot
* Java compatibility version: ≥ 11

### PHP

* You can implement the task from scratch or using the Symfony framework
* PHP version: ≥ 7.3

## Other Requirements

Create a docker-compose.yml file that can be used to start the application’s “production”
version on a host machine where only Docker is installed and there are no other
development tools available. In other words, make the app runnable via “docker compose
up” without host machine dependencies. You can pack dependencies in your containers as
you see fit.

Write tests for your API. It’s not a requirement to reach a 100% in coverage, cover areas
with tests as it makes sense to you.

You can use 3rd-party dependencies to implement parts of your application.

You don’t need to implement a client for this task, only an API.

<hr style="height: 0; border-top: 3px solid #fefefe">

# Installation

It was a requirement to create an environment without any development tools, as if I had to run the project on a "virgin" machine via a `docker`.

Obviously, the `docker` must be up on the machine:

Please, refer to https://www.docker.com/

Since, there are no other development tools on the test machine, I did not change the default ports of services. So if you run PHP, MySql or any webserver what uses their default ports, set them to down.

## Getting files

Clone the repository:

`git clone git@github.com:vaso123/icft.git` into a working directory.

## Environment

Please add a new line to you `hosts` file (if it does not exist):

```shell
127.0.0.1   localhost
```

- Linux and Mac: `/etc/hosts`
- Windows: `[Windows Installation Dir]\System32\drivers\etc\hosts`

The API does not use SSL, so you can access it on:

```shell
http://localhost
```

## Running docker containers

In a terminal navigate to `[working_dir]/docker` and run:

`docker-compose up`

First time do not use `-d` switch, to see, is there any errors while installing images, and is initial scripts runs successfully.

Please, do not close this terminal, or run `docker-compose up -d`.

<p style="color: #bb1010">IMPORTANT!</p>

When you see, the containers created and started, wait until this message:

```shell
mysq  | [System] [MY-010931] [Server] /usr/sbin/mysqld: ready for connections. Version: '8.0.21'  socket: '/var/run/mysqld/mysqld.sock'  port: 3306  MySQL Community Server - GPL.
```

## Database setup

I've created some scripts to help you to set up the database. Navigate tp `[working_dir]/docker/scripts/` directory and run the script in this order:

```
./01_createdb.sh
./02_make_migration.sh
./03_migrate.sh
./04_loadfixtures.sh
./05_create_keypair.sh
```

Follow the instructions. When it asks in `04_loadfixtures.sh`:

`Careful, database "icf" will be purged. Do you want to continue? (yes/no)` say yes.

Now everything has set up, you can start to use the API.

***Note***: Of course, in production code, I never use `root` as database user, 
this project is for demonstration purposes.  

# Usage

For authentication, we use JSON Web Tokens.  I've chosen the `LexikJWTAuthenticationBundle` for this:

https://github.com/lexik/LexikJWTAuthenticationBundle

Since all requests (except token request) require authentication, we need to request a token:

http://localhost/api/login_check

Request body:

```http request
{"email":"[email address]","password":"[password]"}
```

Real example:

```http request
{"email":"user1@email.com","password":"password"}
```

For each request, the following header must be sent:

```
Authorization: Bearer {token}
```

## Swagger and Documentation

You can check documentation at http://localhost/api/doc

## API Endpoints, Postman and Documentation

To make the testing easier, I've made a `Postman` collection. Please refer to

https://www.postman.com/

* Create a workspace
* Import the file: [working_dir]/ICF.postman_collection.json

### Set he token for all requests

To set the token for all requests, select the `Get token for User 1 [can buy]` request, and send.

Copy the token from the response, select the collection, navigate to the `Authorization` tab, select the `Bearer Token` 
for type and paste the token into `Token` input.

Use only the token, not the whole JSON object. For example:

```
eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2ODgwMzA1MTEsImV4cCI6MTY4ODAzMjMxMSwicm9sZXMiOlsiUk9MRV9VU0VSIiwiUk9MRV9CVVkiXSwiZW1haWwiOiJ1c2VyMUBlbWFpbC5jb20ifQ.Cz8J0NjaZcddBBnVKG1HggLtjmnhOtwexBEo-8AFL_ZDlgH3zU5u5j4Ye0KeRiIeZ7ywQksV9D1iZW9b7RYHjzpAwJIlR9RVmqFzCJyV1nKpsH_dMskv1gBGg6SApKrunhiTb-9pquA84DYj4OvFpFaeNgulfM8Hyq3lBIStI81rBkrGpzLzW1KOzX0hAuXEYeatkx-0Qdy_RQz06ht6WsIfm4SoJj6viCnKt0HwPenFQUUif98tN7uWNy9txLcqSghpTrwN90rG4l0f3lLqjaRRiQ8OLLqu0ELuOBKBpcbbt9aofeMy5xtzWUoXPnZ-Glkxiicn4Tvyr2y8oC-NPQ
```

Then `Save`.

<p style="text-align: center">
  <img src="doc/readme/images/collection.png" alt="Set global Bearer token for all requests" />
</p>

There will be only three exceptions. When no token provided, when you ask a token, and when a user want to purchase an artwork,  
who does not have the right to buy.

See below.

### Requests

#### Unauthorized request

When you do not send the Bearer token intentionally. 

<p style="color: #bb1010">Do not send the token in header!</p>

URL: http://localhost/api/artworks/id/129884

**Postman:** *Unauthorized Request*

**Method**: *GET*

**Request body:** `none`

**Response:**

```
{
    "code": 401,
    "message": "JWT Token not found"
}
```

**Response status:** 401

<hr>

#### Get token for User

Get a token.

<p style="color: #bb1010">Do not send the token in header!</p>

URL: http://localhost/api/login_check

**Postman:** *Get token for User 1 [can buy]*

**Method**: *GET*

**Request body:** 

```
{"email":"[email_address]","password":"[password]"}
```
**Response:**

```
 "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2ODgwMzAwMjAsImV4cCI6MTY4ODAzMTgyMCwicm9sZXMiOlsiUk9MRV9VU0VSIiwiUk9MRV9CVVkiXSwiZW1haWwiOiJ1c2VyMUBlbWFpbC5jb20ifQ.ln8lXPt17pywBaRMLPVHhbpVzybu39BHfdM_0AtkGhy04OSag5jqmbaGikD7nsi8_YTmGWZ94SeG2taoWpWBuBMLVZn3n527jIr1yLvQcpfqpqoM_p3uef4MTX8ttkQ_Kbzk8utXAaaJ7UD9ZBer5dDtE1j9PBcjrIuupU755SbxLFbZewENQFXgAaKgoNuphaSMtYw_1eBzrWIcVTZO8KkDnKLWS7XpnPE6g5R9AQ9GegqM1T0I33MMPIHj593jKdhFMO5_YMT8h7tGux9euw5zS5JixRWOw_UBzX_sPIOwQEOKr7WrGqPHPS5u3KBDcZS9_MdZ8tV-RnZWzpEYrQ"
```

**Response status:** 200

<hr>

#### Single Artwork Success

Get the details about an artwork.

URL: http://localhost/api/artworks/id/129884

**Method**: *GET*

**Postman:** *Single Artwork Success*

**Method**: *GET*

**Request body:** `none`

**Response:**

```
 {
    "ID": 129884,
    "title": "Starry Night and the Astronauts",
    "author": "Alma Thomas",
    "thumbnail": {
        "lqip": "data:image/gif;base64,R0lGODlhBAAFAPQAABw/Zhg/aBRBaBZBahRCaxxBahxEahNIchZJcR9LdB9OdiZIZSBEbShLbjxRZyBPeipRcSpReUpWaitXgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAEAAUAAAURoMJIDhJAywAcAlEkxhNNTQgAOw==",
        "width": 5376,
        "height": 6112,
        "alt_text": "Abstract painting composed of small vertical dabs of multiple shades of blue with a small area of similar strokes of red, orange, and yellow in the upper right."
    }
}
```

**Response status:** 200

<hr>

#### Non Existent Artwork

If you are trying to get an artwork by id what is not exists.

URL: http://localhost/api/artworks/id/12

**Method**: *GET*

**Postman:** *Single Artwork Non Exists*

**Request body:** `none`

**Response:**

```
 {
    "status": 404,
    "message": "The item you requested cannot be found."
}
```

**Response status:** 404

<hr>

#### Pagination

Paginating artworks.

URL: http://localhost/api/artworks/pagination/page/20/size/10

**Method**: *GET*

**Postman:** *Artwork Pagination Success*

**Request body:** `none`

**Response:**

```
{
    "pagination": {
        "total": 119795,
        "limit": 10,
        "offset": 190,
        "total_pages": 11980,
        "current_page": 20,
        "prev_url": "https://api.artic.edu/api/v1/artworks?page=19&limit=10&fields=id%2Ctitle%2Cartist_title%2Cthumbnail",
        "next_url": "https://api.artic.edu/api/v1/artworks?page=21&limit=10&fields=id%2Ctitle%2Cartist_title%2Cthumbnail"
    },
    "data": [
        {
            "ID": 202853,
            "title": "Untitled (Nagoya), from the series \"Floods and Japanese\"",
            "author": "Shomei Tomatsu",
            "thumbnail": {
                "lqip": "data:image/gif;base64,R0lGODlhBQAFAPQAAF1XTFxXTV5YS19aTmJbTGFbUGReUmRfVmZgU2VgVWZgVGlkWWtlWGxmWW5oW29oW29pXXBqXXNuY3hxY3hyZnx2aoJ8b42HepGKewAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAFAAUAAAUV4BFUFyYVFvQkAxBNi+IYCMU0AhECADs=",
                "width": 7986,
                "height": 7985,
                "alt_text": "A work made of gelatin silver print."
            }
        },
        {
            "ID": 249723,
            "title": "Untitled (Still life with a statuette of the Venus de Milo)",
            "author": "John Beasley Greene",
            "thumbnail": {
                "lqip": "data:image/gif;base64,R0lGODlhBAAFAPQAAGNSQGRUQmVVQm9eSnVjUHZkUHdlUH5tV4BtV4BtWIJvW4l2YI16ZI97ZY99ZpN/aJaDbZmFbp2KdKKOdwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAEAAUAAAURoIAYQ0I0DwMsklNE0xEoUAgAOw==",
                "width": 7084,
                "height": 9277,
                "alt_text": "A work made of waxed paper negative."
            }
        },
        ...
    ]
}
```

**Response status:** 200

<hr>

#### Purchase an artwork

Purchase of artwork for the authenticated user.

URL: http://localhost/api/artworks/purchace/

**Method**: *POST*

**Postman:** *Purchase Artwork*

**Request body:**

```
{"id": "259722"}
```

**Response:**

```
{
    "user": "user1@email.com",
    "artworkId": 1
}
```

**Response status:** 200

<hr>

#### Purchase of sold artwork

When you want to purchase an artwork that someone else has already bought.

**Instruction:** Just resend the previous request. 

**Response:**

```
{
    "status": 400,
    "message": "Item is not available for purchace. It has already sold."
}
```

**Response status:** 400

<hr>

#### Purchase with a user without buy rights

When you want to purchase of an artwork by a user who does not have the right to buy. 

When we installed the API and ran the `04_loadfixtures.sh`, two users were automatically created.

`user1@email.com` has the right to buy an artwork, while `user2@email.com` does not.

**Instruction:** Get a new token for `user2@email.com`. Use the *Get token for User 2 [can not buy]* Postman request.

Go to the *Purchase Artwork* request and select the *Authorization* tab. Set the `Type` to `Bearer Token` and set the 
`Token` to the previously created token for  `user2@email.com`. Send the request.

<p style="color: #bb1010">Do not forget to set back the `Type` to `Inherit auth from parent` if you want to test other
purchaces request for `user2@email.com`!</p>

<p style="text-align: center">
  <img src="doc/readme/images/noright.png" alt="User have no right to purchase"/>
</p>

**Response:**

```
{
    "status": 400,
    "message": "This user does not have the right to buy."
}
```

**Response status:** 400

<hr>

#### Purchase non-existent artwork

When authenticated user wants to buy a non-existent artwork.

Create a regular purchase  

URL: http://localhost/api/artworks/purchace/

**Method**: *POST*

**Postman:** *Purchase Artwork*

**Request body:**

```
{"id": "12"}
```

**Response:**

```
{
    "status": 404,
    "message": "The item you requested cannot be found."
}
```

**Response status:** 404

<hr>

#### Purchase with bad request

When you send a malformed body or bad parameters for purchasing.

URL: http://localhost/api/artworks/purchace/

**Method**: *POST*

**Postman:** *Purchase Artwork Malformed Body*

**Request body:**

```
Bad request
```

**Response:**

```
{
    "status": 400,
    "message": "Malformed POST body. It is not a JSON. Syntax error"
}
```

**Response status:** 400

<hr>

#### List purchases for a user

List purchases for a user by a given ID.

URL: http://localhost/api/artworks/purchace/get/user1@email.com

**Method**: *GET*

**Postman:** *List Purchases User can not buy*

**Request body:** `none`

**Response:**

```
{
    "user": {
        "id": 1,
        "email": "user1@email.com"
    },
    "purchases": [
        {
            "itemId": 259722,
            "title": "Four Stones",
            "author": "Vija Celmins",
            "thumbnail": {
                "lqip": "data:image/gif;base64,R0lGODlhBgAFAPMAANbV0ejn5u3t7e3t7u7u7u/v7+/v8O/w8PDw8PDw8fHx8fHx8vHy8vX29gAAAAAAACH5BAAAAAAALAAAAAAGAAUAAAQTMB2kECpUVEIAaMmABMFiXAszRQA7",
                "width": 8503,
                "height": 6802,
                "alt_text": "A work made of two found objects and two made objects: alkyd oil on bronze, with artist's pedestal."
            }
        }
        ...
    ]
}
```

**Response status:** 200

<hr>

#### List purchases with bad email

When you are trying to list purchases relating to a user but the email malformed or user not exists.

URL: http://localhost/api/artworks/purchace/get/malformedemail

**Method**: *GET*

**Postman:** *List Purchases Malformed Email*

**Request body:** `none`

**Response:**

```
{
    "status": 400,
    "message": "User not found by email: malformedemail"
}
```

**Response status:** 400

<hr>

#### List purchases with no buy role

WHen you trying to list purchases for a user who has does not have right to buy.

See: [Purchase with a user without buy rights](#purchase-with-a-user-without-buy-rights)

URL: http://localhost/api/artworks/purchace/get/user2@email.com

**Method**: *GET*

**Postman:** *List Purchases User can not buy*

**Request body:** `none`

**Response:**

```
{
    "status": 400,
    "message": "This user has no buy permission: user2@email.com"
}
```

**Response status:** 400

<hr>

####  Token expired

The Bearer token expires in 30 minutes after you requested it.

URL: http://localhost/api/login_check

**Method**: *ANY*

**Postman:** *Any request*

**Request body:** For all requests where token expired.

**Response:**

```
{
    "code": 401,
    "message": "Expired JWT Token"
}
```

**Response status:** 401

<hr>

#### All other cases

In any other case, when something unexpected happens, the API will give you back exactly the same response status
and response text what it gets from the Chicago Artwork API.

# Tests

If you want to run tests, for the API tests you need to run from the working directory:

```shell
bin/phpunit --bootstrap ./tests/bootstrap.php --configuration ./phpunit.xml.dist ./tests --teamcity
```
Since, there is no special configuration for tests you can also use just:

```shell
bin/phpunit ./tests
```

Alternatively you can set up your IDE. For example, for PHPStorm:

https://www.jetbrains.com/help/phpstorm/using-phpunit-framework.html

