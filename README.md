# Vocabulate
A modern language learning flashcard web app, built with Laravel, MySQL and Vue.js. It features dynamic card management, spaced repetition, and a mobile-friendly user interface for efficient vocabulary retention.

## Development
via Laravel Sail:
```bash
cd ./src
./vendor/bin/sail build --no-cache
./vendor

```


## Build App image
from the dir root run:

```bash
docker build -t [image-tag] -f ./docker/Dockerfile[.dev] .
```
