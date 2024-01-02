.PHONY: install
install:
	composer update

.PHONY: qa
qa: phpstan cs

.PHONY: cs
cs:
ifdef GITHUB_ACTION
	vendor/bin/phpcs --standard=ruleset.xml --encoding=utf-8 --extensions="php,phpt" --colors -nsp -q --report=checkstyle src tests | cs2pr
else
	vendor/bin/phpcs --standard=ruleset.xml --encoding=utf-8 --extensions="php,phpt" --colors -nsp src tests
endif

.PHONY: csf
csf:
	vendor/bin/phpcbf --standard=ruleset.xml --encoding=utf-8 --extensions="php,phpt" --colors -nsp src tests

.PHONY: phpstan
phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon

.PHONY: tests
tests:
	vendor/bin/codecept run

.PHONY: coverage
coverage:
ifdef GITHUB_ACTION
	vendor/bin/codecept run --coverage --coverage-xml
else
	vendor/bin/codecept run --coverage --coverage-html
endif
