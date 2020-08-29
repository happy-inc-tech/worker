VERBOSITY =

v:
	$(eval VERBOSITY := -v)

vv:
	$(eval VERBOSITY := -vv)

vvv:
	$(eval VERBOSITY := -vvv)

vendor: composer.json
	composer install
	touch vendor

test: vendor
	$(EXEC_PHP) vendor/bin/phpunit $(VERBOSITY)

cs: vendor
	$(EXEC_PHP) vendor/bin/php-cs-fixer fix --allow-risky=yes --dry-run --diff --diff-format=udiff $(VERBOSITY)

fixcs: vendor
	$(EXEC_PHP) vendor/bin/php-cs-fixer fix --allow-risky=yes $(VERBOSITY)

psalm: vendor
	$(EXEC_PHP) vendor/bin/psalm $(file)

composer-check-require: vendor
	$(EXEC_PHP) vendor/bin/composer-require-checker check $(VERBOSITY)

composer-unused: vendor
	composer unused $(VERBOSITY)

composer-validate:
	composer validate $(VERBOSITY)

check: test cs psalm composer-check-require composer-unused composer-validate

.PHONY: v vv vvv test cs fixcs psalm composer-check-require composer-unused composer-validate check
