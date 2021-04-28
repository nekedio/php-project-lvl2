all: lint test call

install:
	composer install

lintOld:
	composer run-script phpcs -- --standard=PSR12 src bin tests

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src tests
	composer exec --verbose phpstan -- --level=8 analyse src tests

test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

test:
	composer exec --verbose phpunit tests

call:
	php bin/gendiff --format stylish tests/fixtures/treeBefore.json tests/fixtures/treeAfter.json

tree:
	tree --prune -I vendor
