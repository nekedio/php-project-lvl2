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

gtest:
	clear
	php bin/gendiff -h
	php bin/gendiff -help
	php bin/gendiff -v
	php bin/gendiff -version
	php bin/gendiff --format json test1 test2
	php bin/gendiff test1 test2
