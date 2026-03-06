.PHONY: install test lint coverage coverage-check migrate smoke benchmark serve

COMPOSER ?= ./scripts/composerw

install:
	$(COMPOSER) install

test:
	$(COMPOSER) test

lint:
	$(COMPOSER) lint

coverage:
	$(COMPOSER) coverage

coverage-check:
	$(COMPOSER) coverage:check

migrate:
	$(COMPOSER) migrate

smoke:
	$(COMPOSER) smoke

benchmark:
	$(COMPOSER) benchmark

serve:
	php -S 127.0.0.1:$${APP_PORT:-9091} -t public
