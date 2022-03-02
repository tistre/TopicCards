TopicCards
==========

Build the PHP Docker image:

```
$ docker compose build php
```

Install dependencies:

```
$ docker compose run --workdir /opt/app php composer install
```
