install:
	composer install

lint:
	composer run-script phpcs -- --standard=PSR12 src bin

gendiffh:
	clear
	php bin/gendiff -h

gendiffv:
	clear
	php bin/gendiff --version
