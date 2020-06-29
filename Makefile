install:
	composer install

lint:
	composer run-script phpcs -- --standard=PSR12 src bin

test:
	composer run-script phpunit tests

ftest:
	php bin/gendiff tests/fixtures/before.yml tests/fixtures/after.yml
	php bin/gendiff --format json tests/fixtures/before.yml tests/fixtures/after.yml
	php bin/gendiff --format yml tests/fixtures/before.yml tests/fixtures/after.yml