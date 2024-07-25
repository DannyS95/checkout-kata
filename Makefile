DOCKER_COMPOSE := docker compose
DOCKER_EXEC := $(DOCKER_COMPOSE) exec

# Replace 'your-container-name' with the actual name of your Docker container
CONTAINER_NAME := fluro-checkout

# Makefile target to enter the Docker container and run 'composer install'
php-artisan:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php artisan $(command)'

migrate:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php artisan migrate:fresh --seed'

composer-i:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'composer install --no-interaction'

composer-u:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'composer update --no-interaction --with-all-dependencies {-W}'

composer-add:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'composer require i$(package)'

fix-laravel-perms:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'chmod -R 777 storage database'


install: composer-i fix-laravel-perms migrate

test:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'vendor/bin/pest'

sh:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh
