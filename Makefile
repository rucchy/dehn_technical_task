#!/bin/bash

help: # Show help
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'
build:	##Build and run the docker
	docker-compose up -d --build
	docker-compose exec cli sh -c "cd dehn_technical_task/cli && composer install"
	docker-compose exec cli sh -c "echo '{}' > dehn_technical_task/cli/files/json/task.json"
run: 	##Run the docker
	docker-compose up -d
composer-install: ##Run the composer install
	docker-compose exec cli sh -c "cd dehn_technical_task/cli && composer install"
tests:	##Run all tests
	docker-compose exec cli ./dehn_technical_task/cli/vendor/bin/phpunit dehn_technical_task/cli/tests
stop:	##Stop the docker
	docker-compose stop
restart: ##Restart the docker
	docker-compose restart
remove:	##Remove the docker
	docker-compose down
shell:	##Enter in the shell of docker
	docker-compose exec cli bash -l
init:	##Build and run the docker, run the composer install and enter in the shell of docker
	docker-compose up -d --build
	docker-compose exec cli sh -c "cd dehn_technical_task/cli && composer install"
	docker-compose exec cli sh -c "echo '{}' > dehn_technical_task/cli/files/json/task.json"
	docker-compose exec cli bash -l


