install:
	composer install

lint:
	composer run-script phpcs -- --standard=PSR12 src bin tests

test:
	composer run-script phpunit tests

ftest:
	php bin/gendiff tests/fixtures/before.yml tests/fixtures/after.yml
	php bin/gendiff --format json tests/fixtures/before.yml tests/fixtures/after.yml
	php bin/gendiff --format plain tests/fixtures/before.yml tests/fixtures/after.yml
	php bin/gendiff --format stylish tests/fixtures/before.yml tests/fixtures/after.yml
