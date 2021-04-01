all: lint test call

install:
	composer install

lint:
	composer run-script phpcs -- --standard=PSR12 src bin tests

test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

test:
	composer exec --verbose phpunit tests

call:
	php bin/gendiff --format stylish tests/fixtures/treeBefore.json tests/fixtures/treeAfter.json

