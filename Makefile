SHELL=/bin/sh
.DEFAULT_GOAL := help
current-dir := $(dir $(abspath $(lastword $(MAKEFILE_LIST))))

.PHONY: .SILENT
help: ## Outputs this help screen
	grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

##
## 🐳 Docker
.PHONY: start stop destroy-api start-mysql destroy-mysql
start: ## Start all Docker services
	docker-compose up -d

stop: ## Stop all Docker services
	docker-compose down

destroy-api: ## Remove API container and volumes
	docker-compose rm --stop --force --volumes api

start-mysql: ## Start MySQL service
	docker-compose up -d mysql

destroy-mysql: ## Remove MySQL container and volumes
	docker-compose rm --stop --force --volumes mysql

##
## ⚒️ Quality Tools
.PHONY: phpstan enter
phpstan: ## Run PHPStan for static analysis
	docker exec daily-focus composer phpstan

enter: ## Enter the Docker container
	docker exec -it daily-focus bash

##
## 🚦️ Tests
.PHONY: tests
tests: ## Run project tests
	docker exec daily-focus bash -c "composer test"

##
## 🚀 Installation
.PHONY: installation prepare-permissions run-inside
installation: start prepare-permissions
	docker exec daily-focus bash -c "\
		composer install && \
		php app/bin/console doctrine:database:create --if-not-exists && \
		php app/bin/console doctrine:migrations:migrate --no-interaction && \
		php app/bin/console doctrine:fixtures:load --no-interaction"

prepare-permissions: ## Prepare necessary permissions outside Docker
	sudo mkdir -p app/var/cache app/var/log
	sudo chown -R www-data:www-data app/var/cache app/var/log
	sudo chmod -R 775 app/var/cache app/var/log
