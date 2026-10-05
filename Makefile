DOCKER_COMPOSE := docker compose
DB_CONTAINER   = database
MARIADB_ROOT_PASS = password
MYSQL_USER     = app

.PHONY: up down console

up:
	$(DOCKER_COMPOSE) up -d --build

down:
	$(DOCKER_COMPOSE) down

## Accorde les droits de création de base de données à l'utilisateur app
db-grant:
	$(DOCKER_COMPOSE) exec -T $(DB_CONTAINER) mariadb -u root -p$(MARIADB_ROOT_PASS) -e 'GRANT ALL PRIVILEGES ON app_api_japan_test.* TO "$(MYSQL_USER)"@"%"; GRANT ALL PRIVILEGES ON app_api_japan.* TO "$(MYSQL_USER)"@"%"; FLUSH PRIVILEGES;'

## Initialise la base de données de test
db-test-create: db-grant 
	php bin/console doctrine:database:create --env=test --if-not-exists

## Crée la base de données dev
db-create: 
	php bin/console doctrine:database:create --if-not-exists