cs-fix:
	php-cs-fixer fix --diff -v --using-cache=no src
	php-cs-fixer fix --diff -v --using-cache=no tests
test:
	bin/phpunit
cover:
	XDEBUG_MODE=coverage bin/phpunit  --coverage-html="coverage"
php:
	docker exec -it symfony-t-price-checker-api-php bash
up:
	docker-compose up -d
build:
	docker-compose up --build -d
cs-check:
	vendor/bin/php-cs-fixer check
cs-fix:
	vendor/bin/php-cs-fixer fix
