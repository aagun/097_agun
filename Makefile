up:
	docker compose up -d
down:
	docker compose down -v --remove-orphans
down-purge:
	@make down
	sudo rm -rf ./docker/mysql/volumes/
