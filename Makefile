.PHONY: up down restart bash shell migrate seed fresh logs test build artisan npm-install npm-dev npm-build

up:
	docker-compose up -d --build

down:
	docker-compose down

restart:
	docker-compose restart

bash:
	docker-compose exec app bash

migrate:
	docker-compose exec app php artisan migrate --force

seed:
	docker-compose exec app php artisan db:seed --force

fresh:
	docker-compose exec app php artisan migrate:fresh --seed

logs:
	docker-compose logs -f

test:
	docker-compose exec app php artisan test

build:
	docker-compose build --no-cache

shell:
	docker-compose exec app bash

artisan:
	docker-compose exec app php artisan $(CMD)

npm-install:
	docker-compose exec app npm install

npm-dev:
	docker-compose exec app npm run dev

npm-build:
	docker-compose exec app npm run build
