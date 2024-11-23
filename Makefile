.PHONY: vendor-install
vendor-install: 
	@docker run --rm \
		-u "$$(id -u):$$(id -g)" \
		-v "$$(pwd):/var/www/html" \
		-w /var/www/html \
		laravelsail/php83-composer:latest \
		composer install --ignore-platform-reqs && \
		npm install

.PHONY: run
run:
	@./vendor/bin/sail up -d && npm run dev

.PHONY: run-build
run-build:
	@./vendor/bin/sail up --build -d && npm run dev

.PHONY: stop
stop:
	@./vendor/bin/sail down
