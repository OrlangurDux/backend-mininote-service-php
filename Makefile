THIS_FILE := $(lastword $(MAKEFILE_LIST))
.PHONY: help artisan composer build up start down destroy stop restart logs logs-service ps login login-service
#ifeq (test,$(firstword $(MAKECMDGOALS)))
RUN_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
#$(eval $(RUN_ARGS):;@:)
#endif
help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-16s\033[0m %s\n", $$1, $$2}'
artisan: ## Artisan command use as "make artisan '[COMMAND]'"
	docker-compose -f docker-compose.dev.yml run dev-mininote-artisan $(RUN_ARGS)
composer: ## Composer command use as "make composer '[COMMAND]'"
	docker-compose -f docker-compose.dev.yml run dev-mininote-composer $(RUN_ARGS)
npm: ## NPM command use as "make npm '[COMMAND]'"
	docker-compose -f docker-compose.dev.yml run dev-mininote-npm $(RUN_ARGS)
build: ## Build docker container user as "make build [DOCKER-COMPOSE SERVICE]" e.g. make build courts-php-fpm-dev
	docker-compose -f docker-compose.dev.yml build $(RUN_ARGS)
up: ## Start docker compose service use as "make up [DOCKER-COMPOSE SERVICE]" e.g. make up courts-mysql-dev
	docker-compose -f docker-compose.dev.yml up -d $(RUN_ARGS)
start: ## Short link for start courts-service-dev use as "make start"
	docker-compose -f docker-compose.dev.yml up -d dev-mininote-service
down: ## Stop docker compose service use as "make down [DOCKER-COMPOSE SERVICE]" e.g. make down courts-mysql-dev
	docker-compose -f docker-compose.dev.yml down $(RUN_ARGS)
destroy:
	docker-compose -f docker-compose.dev.yml down -v $(RUN_ARGS)
stop: ## Stop all service in running docker-compose file use as "make stop"
	docker-compose -f docker-compose.dev.yml down
restart: ## Restart service use as "make restart [DOCKER-COMPOSE SERVICE]"
	docker-compose -f docker-compose.dev.yml stop $(RUN_ARGS)
	docker-compose -f docker-compose.dev.yml up -d $(RUN_ARGS)
logs: ## Show logs all docker-compose service in real time use as "make logs"
	docker-compose -f docker-compose.dev.yml logs --tail=100 -f $(RUN_ARGS)
logs-service: ## Show logs primary service in real time use as "make logs-service"
	docker-compose -f docker-compose.dev.yml logs --tail=100 -f dev-mininote-service
ps: ## Show running docker service from docker-compose file use as "make ps"
	docker-compose -f docker-compose.dev.yml ps
login: ## Login cli sh to container use as "make login [DOCKER-COMPOSE SERVICE]"
	docker-compose -f docker-compose.dev.yml exec $(RUN_ARGS) /bin/sh
login-service: ## Login cli sh to primary container use as "make login-service"
	docker-compose -f docker-compose.dev.yml exec dev-mininote-service /bin/sh
development: ## Development method for deploy
	@mkdir -p /mysql
	docker-compose -f docker-compose.dev.yml down
	docker-compose -f docker-compose.dev.yml build dev-mininote-php-fpm
	docker-compose -f docker-compose.dev.yml run dev-mininote-composer install
	docker-compose -f docker-compose.dev.yml run dev-mininote-artisan optimize
	docker-compose -f docker-compose.dev.yml run dev-mininote-artisan migrate
	docker-compose -f docker-compose.dev.yml up -d dev-mininote-service
production: ## Production method for deploy
	docker-compose down
	docker-compose rm -f
	docker-compose up -d --build