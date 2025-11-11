.PHONY: help qa cs cs-fix rector rector-fix stan psalm dead-code test db cc

# Colors for output
GREEN := \033[0;32m
YELLOW := \033[0;33m
RED := \033[0;31m
BLUE := \033[0;34m
NC := \033[0m # No Color

help:
	@echo "$(BLUE)Exercices App - Available tasks$(NC)"
	@echo ""
	@echo "$(GREEN)QA & Code Quality:$(NC)"
	@echo "  make qa          - Run full QA suite (ECS, Rector, PHPStan, Psalm)"
	@echo "  make cs          - Run ECS code style check (read-only)"
	@echo "  make cs-fix      - Run ECS code style fixer with --fix"
	@echo "  make rector      - Run Rector in dry-run mode"
	@echo "  make rector-fix  - Run Rector and apply fixes"
	@echo "  make stan        - Run PHPStan (level 9)"
	@echo "  make psalm       - Run Psalm static type checker"
	@echo "  make dead-code   - Find dead code with Psalm"
	@echo ""
	@echo "$(GREEN)Testing:$(NC)"
	@echo "  make test        - Run all tests (PHPUnit)"
	@echo ""
	@echo "$(GREEN)Database:$(NC)"
	@echo "  make db          - Create database and run migrations"
	@echo ""
	@echo "$(GREEN)Cache:$(NC)"
	@echo "  make cc          - Clear Symfony cache"
	@echo ""

# Full QA Suite: ECS → Rector → PHPStan → Psalm
qa:
	@echo "$(BLUE)🔍 Starting QA suite...$(NC)\n"
	@start_time=$$(date +%s); \
	\
	echo "$(GREEN)📋 Running ECS...$(NC)"; \
	$(MAKE) cs-fix; \
	cs_time=$$(($$(date +%s) - start_time)); \
	echo "$(GREEN)✅ ECS finished in $$cs_time seconds$(NC)\n"; \
	\
	echo "$(GREEN)♻️  Running Rector (dry-run)...$(NC)"; \
	$(MAKE) rector; \
	rector_time=$$(($$(date +%s) - start_time - cs_time)); \
	echo "$(GREEN)✅ Rector finished in $$rector_time seconds$(NC)\n"; \
	\
	echo "$(GREEN)📊 Running PHPStan (level 9)...$(NC)"; \
	$(MAKE) stan; \
	stan_time=$$(($$(date +%s) - start_time - cs_time - rector_time)); \
	echo "$(GREEN)✅ PHPStan finished in $$stan_time seconds$(NC)\n"; \
	\
	echo "$(GREEN)🔐 Running Psalm...$(NC)"; \
	$(MAKE) psalm; \
	psalm_time=$$(($$(date +%s) - start_time - cs_time - rector_time - stan_time)); \
	echo "$(GREEN)✅ Psalm finished in $$psalm_time seconds$(NC)\n"; \
	\
	total_time=$$(($$(date +%s) - start_time)); \
	echo "$(BLUE)✨ QA SUITE FINISHED WITHOUT ERROR$(NC)"; \
	echo "$(YELLOW)⏱️  Total execution time: $$total_time seconds$(NC)"

# ECS - Code Style Check (read-only)
cs:
	@echo "$(GREEN)📋 Running ECS (check only)...$(NC)"
	@vendor/bin/ecs check src tests
	@echo "$(GREEN)✅ ECS check finished$(NC)"

# ECS - Code Style Fix
cs-fix:
	@echo "$(GREEN)📋 Running ECS with --fix...$(NC)"
	@vendor/bin/ecs check src tests --fix
	@echo "$(GREEN)✅ ECS fix finished$(NC)"

# Rector - Dry Run (no changes)
rector:
	@echo "$(GREEN)♻️  Running Rector (dry-run)...$(NC)"
	@vendor/bin/rector --dry-run
	@echo "$(GREEN)✅ Rector dry-run finished (no changes applied)$(NC)"

# Rector - Apply Fixes
rector-fix:
	@echo "$(GREEN)♻️  Running Rector with fixes...$(NC)"
	@vendor/bin/rector
	@echo "$(GREEN)✅ Rector fixes applied$(NC)"
	@echo ""
	@echo "$(GREEN)📋 Running ECS after Rector...$(NC)"
	@vendor/bin/ecs check src tests --fix
	@echo "$(GREEN)✅ ECS fix finished$(NC)"

# PHPStan - Static Analysis (Level 9)
stan:
	@echo "$(GREEN)📊 Running PHPStan (level 9)...$(NC)"
	@vendor/bin/phpstan analyse --memory-limit=512M --level 9 src tests
	@echo "$(GREEN)✅ PHPStan finished$(NC)"

# Psalm - Type Checking
psalm:
	@echo "$(GREEN)🔐 Running Psalm...$(NC)"
	@vendor/bin/psalm --no-cache --show-info=false --threads=1
	@echo "$(GREEN)✅ Psalm finished$(NC)"

# Psalm - Find Dead Code
dead-code:
	@echo "$(GREEN)🔍 Finding dead code with Psalm...$(NC)"
	@vendor/bin/psalm --find-dead-code --no-cache --show-info=false --threads=1
	@echo "$(GREEN)✅ Dead code analysis finished$(NC)"

# PHPUnit - All Tests
test:
	@echo "$(GREEN)🧪 Running all tests...$(NC)"
	@php bin/phpunit --colors=always --testdox
	@echo "$(GREEN)✅ Tests finished$(NC)"

# Create Database & Run Migrations
db:
	@echo "$(GREEN)🗄️  Creating database...$(NC)"
	@php bin/console doctrine:database:create --if-not-exists
	@echo "$(GREEN)✅ Database created$(NC)"
	@echo ""
	@echo "$(GREEN)📦 Running migrations...$(NC)"
	@php bin/console doctrine:migrations:migrate -n
	@echo "$(GREEN)✅ Migrations finished$(NC)"

# Clear Symfony Cache
cc:
	@echo "$(GREEN)🧹 Clearing Symfony cache...$(NC)"
	@php bin/console cache:clear
	@php bin/console doctrine:cache:clear-metadata
	@php bin/console doctrine:cache:clear-query
	@php bin/console doctrine:cache:clear-result
	@rm -rf var/cache/*
	@echo "$(GREEN)✅ Cache cleared$(NC)"