all: lint test call

install:
	composer install

lint:
	composer run-script phpcs -- --standard=PSR12 src bin tests

test:
	composer run-script phpunit tests

call:
	php bin/gendiff --format stylish tests/fixtures/treeBefore.json tests/fixtures/treeAfter.json

