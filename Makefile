.PHONY: run down exec migrate test-unit install-deps lint

run:
	docker-compose up -d --build

down:
	docker-compose down

exec:
	docker-compose exec app bash

migrate:
	docker compose exec app php artisan migrate

install-deps:
	docker-compose exec app composer install

test-unit:
	docker-compose exec app php artisan test --testsuite=Unit

lint:
	docker-compose exec app vendor/bin/php-cs-fixer fix --dry-run --diff
