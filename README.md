# GEO Coordinates API

This API tries to load GEO Coordinates by the given query.

Primarily it tries to load data from internal database. If the database does not contain data for the location then it tries
to load them from external Open Street Map API.

## How to run

### Prerequisites

- Docker
- Docker-compose
- curl (for test running application) or you can se any other way how to send requests to the API.

### Restart RoadRunner workers on file-change

RoadRunner needs to be restarted everytime when any file has been changed. There is a prepared shell script
which restarts RoadRunner _reset.sh_. You can run it manualy `docker exec geo-api ./reset.sh` or set up
file watcher in your favourite IDE. Here is a guide for PHPStorm

- Go to Preferences > Tools > File Watchers
- Click on + (plus sign) and select custom
- File type is PHP
- Scope Project Files
- Program: docker
- Arguments: exec geo-api ./reset.sh

### Run application

If you have docker and docker-compose installed just run `docker-compose up --build -d api` in a terminal.

Default port of the running container is 8999 (if you haven't changed it in docker-compose.yml).

To check the application is up and running just send a request to the API.

```bash
curl -X GET "127.0.0.1:8999/status"
{"status":"ok"} # Response
```

There is one more end point which provides geo coordinates by the
given address. You can simply call it like below

```bash
curl -X GET "127.0.0.1:8999/v1/search/coordinates?query=Praha"
{"latitude":"50.0596288","longitude":"14.446459273258009"} # response
```

Logs are being sent to standard output, and you can see them in this way.

```bash
docker logs -f geo-api
```

## TODO

- Swagger
- Loading data from internal database is not implemented yet.

## Useful commands

Information about current workers

```bash
docker exec geo-api /usr/local/bin/rr workers -c /etc/roadrunner/.rr.yaml
```

## Code quality

### phpstan

```bash
docker exec geo-api vendor/bin/phpstan analyse ./src -l 9 -c phpstan.neon
```
