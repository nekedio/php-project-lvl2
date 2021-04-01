all: lint test call

install:
	composer install

lint:
	composer run-script phpcs -- --standard=PSR12 src bin tests

test:
	composer exec --verbose phpcbf -- --standard=PSR12 src tests

call:
	php bin/gendiff --format stylish tests/fixtures/treeBefore.json tests/fixtures/treeAfter.json

