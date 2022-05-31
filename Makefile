SHELL := /bin/bash

docker_fresh:
	docker-compose stop
	yes | docker-compose rm
	yes | docker volume prune
	docker-compose up -d --force-recreate
.PHONY: docker_fresh

refresh_database:
	symfony console doctrine:database:create --if-not-exists
	symfony console doctrine:schema:update --force
	yes | symfony console doctrine:fixtures:load
.PHONY: refresh_database

git_push:
	git add . && git commit -m'$(commit)' && git push origin
.PHONY: git_push