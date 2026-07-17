cs-fix:
	php-cs-fixer fix --diff -v --using-cache=no src
	php-cs-fixer fix --diff -v --using-cache=no tests
test:
	bin/phpunit
cover:
	XDEBUG_MODE=coverage bin/phpunit  --coverage-html="coverage"
php:
	docker exec -it symfony-t-price-checker-api-php bash
build:
	docker-compose up -d --build
up:
	docker-compose up -d
debug-build:
	docker compose -f docker-compose.debug.yml up -d --build
debug:
	#docker compose -f docker-compose.yml -f docker-compose.debug.yml up -d
	docker compose -f docker-compose.debug.yml up -d
build:
	docker-compose up --build -d
cs-check:
	vendor/bin/php-cs-fixer check && vendor/bin/phpcs ./src
cs-fix:
	vendor/bin/php-cs-fixer fix && vendor/bin/phpcbf ./src
