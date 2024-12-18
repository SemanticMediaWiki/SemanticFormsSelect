-include .env-39
export

# setup for docker-compose-ci build directory
# delete "build" directory to update docker-compose-ci

ifeq (,$(wildcard ./build/))
    $(shell git submodule update --init --remote)
endif

EXTENSION := SemanticFormsSelect

# docker images
MW_VERSION?=1.39
PHP_VERSION?=7.4
DB_TYPE?=sqlite
DB_IMAGE?=""

# extensions
SMW_VERSION?=4.2.0
PF_VERSION?=5.9.0

# composer
# Enables "composer update" inside of extension
COMPOSER_EXT?=true

# nodejs
# Enables node.js related tests and "npm install"
NODE_JS?=true

# check for build dir and git submodule init if it does not exist
include build/Makefile

