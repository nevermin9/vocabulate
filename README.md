# Vocabulate
A modern language learning flashcard web app, built with vanilla PHP, htmx, web components, and modern JavaScript. It features dynamic card management, spaced repetition, and a mobile-friendly user interface for efficient vocabulary retention.

## Development
via docker compose, inside docker/ dir:
```bash
docker compose -p vocabulate up -d --build

```


## Build App image
from the dir root run:

```bash
docker build -t [image-tag] -f ./docker/Dockerfile[.dev] .
```

## App structure
app/
├── public/
├── src/
├── assets/
├── templates/
├── tests/
├── vendor/
├── composer.json
docker/
├── Dockerfile
├── Dockerfile.dev
├── docker-compose.yml
├── nginx/
    └── nginx.conf
