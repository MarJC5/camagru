# COLORS
GREEN		= \033[1;32m
RED 		= \033[1;31m
ORANGE		= \033[1;33m
CYAN		= \033[1;36m
RESET		= \033[0m

# FOLDER
SRCS_DIR	= ./
ENV_FILE	= ${SRCS_DIR}.env
DOCKER_DIR	= ${SRCS_DIR}docker-compose.yml


# COMMANDS
DOCKER		=  docker compose -f ${DOCKER_DIR} --env-file ${ENV_FILE} -p camagru

%:
	@:

all: start up

start:
	@echo "${GREEN}Starting containers...${RESET}"
	@${DOCKER} up -d --remove-orphans

up: install
	@echo "${GREEN}Starting containers...${RESET}"
	@${DOCKER} up -d --remove-orphans

down:
	@echo "${RED}Stopping containers...${RESET}"
	@${DOCKER} down

stop:
	@echo "${RED}Stopping containers...${RESET}"
	@${DOCKER} stop

rebuild: install
	@echo "${GREEN}Rebuilding containers...${RESET}"
	@${DOCKER} up -d --remove-orphans --build

delete:
	@echo "${RED}Deleting containers...${RESET}"
	@${DOCKER} down -v --remove-orphans

rebuild-no-cache:
	@echo "${GREEN}Rebuilding containers...${RESET}"
	@${DOCKER} build --no-cache
	@${DOCKER} up -d --remove-orphans --build

frankenphp:
	@echo "${GREEN}Running frankenphp ...${RESET}"
	@${DOCKER} exec frankenphp sh

mysql8:
	@echo "${GREEN}Running mysql 8 ...${RESET}"
	@${DOCKER} exec mysql_8 bash

seed:
	@echo "${GREEN}Running seed cmd ...${RESET}"
	@${DOCKER} exec frankenphp sh -c "php /var/www/html/camagru/seed"

migrate-seed: migrate seed
	@echo "${GREEN}Migration and seeding completed${RESET}"

install: migrate-seed
	@echo "${GREEN}Installation completed${RESET}"

migrate:
	@echo "${GREEN}Running migrate cmd ...${RESET}"
	@${DOCKER} exec frankenphp sh -c "php /var/www/html/camagru/migrate reset"

.PHONY: all start up down stop rebuild delete rebuild-no-cache frankenphp mysql8