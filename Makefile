# ─── Docker shortcuts ─────────────────────────────────────────────────────────
.PHONY: help up down build restart shell artisan tinker test migrate seed logs

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-16s\033[0m %s\n", $$1, $$2}'

up: ## Start all services in background
	docker compose up -d --build

down: ## Stop all services
	docker compose down

build: ## Rebuild app image without cache
	docker compose build --no-cache app

restart: ## Restart app container
	docker compose restart app

shell: ## Open bash shell in app container
	docker compose exec app bash

artisan: ## Run artisan command: make artisan CMD="migrate:status"
	docker compose exec app php artisan $(CMD)

tinker: ## Open Laravel Tinker
	docker compose exec app php artisan tinker

test: ## Run PHPUnit tests
	docker compose exec app php artisan test --parallel

migrate: ## Run migrations
	docker compose exec app php artisan migrate

seed: ## Seed the database
	docker compose exec app php artisan db:seed

logs: ## Tail all service logs
	docker compose logs -f --tail=100

logs-app: ## Tail app container logs only
	docker compose logs -f --tail=100 app

ps: ## Show container status
	docker compose ps
