.PHONY: run down exec migrate test-unit install-deps

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
	docker-compose exec app npm install

test-unit:
	docker-compose exec app php artisan test --testsuite=Unit
