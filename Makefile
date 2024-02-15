.PHONY: help

dc := docker-compose

up: ## Start all containers
	$(dc) up -d

enter: ## Enter php container
	$(dc) exec php sh

stop: ## Stop all containers
	$(dc) stop

down: ## Down all containers
	$(dc) down

php-rebuild:
	$(dc) rm -fvs php && $(dc) up -d --build php