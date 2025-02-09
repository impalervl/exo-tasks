export

.PHONY: start stop restart tests

DC := docker-compose exec
FPM := $(DC) php-fpm
CLI := $(FPM) php cli.php

start:
	@docker-compose up -d

stop:
	@docker-compose down

restart: stop start

ssh:
	@$(FPM) bash

env:
	cp ./.env.example ./.env

composer-install:
	@$(FPM) composer install

create-db-structure:
	@$(CLI) create-ab-test-storage-table
	@$(CLI) create-tv-series-tables

populate-db:
	@$(CLI) populate-tv-series

test:
	@$(FPM) vendor/bin/phpunit

dump-autoload:
	@$(FPM) composer dump-autoload

sleep-15:
	sleep 15

install: env start sleep-15 composer-install create-db-structure populate-db
