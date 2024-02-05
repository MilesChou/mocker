#!/usr/bin/make -f

.PHONY: all
all: test

.PHONY: clean
clean:
	rm -rf ./build

.PHONY: clean-all
clean-all: clean
	rm -rf ./vendor
	rm -rf ./composer.lock

.PHONY: check
check:
	php vendor/bin/phpcs

.PHONY: test
test: clean check
	php vendor/bin/phpunit

.PHONY: coverage
coverage: test
	@if [ "`uname`" = "Darwin" ]; then open build/coverage/index.html; fi
