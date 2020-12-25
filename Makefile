install:
	composer install

lint:
	composer run-script phpcs -- --standard=PSR12 src bin tests

test:
	composer run-script phpunit tests

ftest:
	php bin/gendiff tests/fixtures/before.json tests/fixtures/after.json
	php bin/gendiff --format json tests/fixtures/before.json tests/fixtures/after.json
	php bin/gendiff --format plain tests/fixtures/before.json tests/fixtures/after.json
	php bin/gendiff --format stylish tests/fixtures/before.json tests/fixtures/after.json
