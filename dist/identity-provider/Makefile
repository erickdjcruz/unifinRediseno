default: test

###################
# Install #
###################

# Install all 3rd-party library dependencies
.PHONY: deps
deps:
	composer install

###################
# Test #
###################

# Run all tests
.PHONY: test
test: test-code-standards test-unit

# Run unit tests
.PHONY: test-unit
test-unit:
	./vendor/bin/phpunit --testsuite 'Unit Tests'

# Run PSR code-style check test
.PHONY: test-code-standards
test-code-standards:
	./vendor/bin/php-cs-fixer fix --config=.php_cs.dist --diff --dry-run --verbose --using-cache=no
