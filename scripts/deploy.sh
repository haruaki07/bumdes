#!/usr/bin/env bash
set -euo pipefail
trap 'log "Error on line $LINENO. Deployment aborted."; exit 1' ERR

VENDOR_ARCHIVE=${1:-}
ASSETS_ARCHIVE=${2:-}

main() {
    for cmd in git composer tar; do
        command -v "$cmd" >/dev/null 2>&1 || { echo "Missing required command: $cmd"; exit 1; }
    done

    PHP_BIN=$(detect_php)
    log "Using PHP binary: $PHP_BIN"

    # unzip composer deps
    if [ -f "$VENDOR_ARCHIVE" ]; then
        log "Extracting composer dependencies..."
        rm -rf vendor
        tar -xJf "$VENDOR_ARCHIVE"
        rm -f "$VENDOR_ARCHIVE"
    fi

    # unzip assets
    if [ -f "$ASSETS_ARCHIVE" ]; then
        log "Extracting assets..."
        tar --overwrite -xJf "$ASSETS_ARCHIVE"
        rm -f "$ASSETS_ARCHIVE"
    fi

    log "Deploying app with commit ($(git rev-parse --short HEAD))..."

    # if .env was not found, abort
    if [ ! -f ".env" ]; then
        log "Deploy cancelled!\nThe .env file is missing! Please configure it manually and then redeploy."
        return
    fi

    log "Putting app into maintenance mode..."
    "$PHP_BIN" artisan down --no-ansi --no-interaction

    log "Installing composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader

    log "Migrating database..."
    "$PHP_BIN" artisan migrate --force --no-ansi --no-interaction

    log "Clearing cache bootstrap files..."
    "$PHP_BIN" artisan optimize:clear --no-ansi --no-interaction

    log "Caching bootstrap files..."
    "$PHP_BIN" artisan optimize --no-ansi --no-interaction
    "$PHP_BIN" artisan view:cache --no-ansi --no-interaction

    log "Restoring app..."
    "$PHP_BIN" artisan up --no-ansi --no-interaction

    log "Restarting queue workers..."
    "$PHP_BIN" artisan queue:restart

    log "Deploy finished!"
}

log() {
    local message="$1"
    local log_file="$PWD/storage/logs/deploy.log"
    local timestamp="$(date +"%Y-%m-%d %H:%M:%S")"
    local log_entry="[$timestamp] $message"

    echo -e "\033[36m$message\033[0m"
    echo "$log_entry" | tee -a "$log_file" > /dev/null
}

detect_php() {
    if [[ -n "${PHP_CMD:-}" && -x "$(command -v "$PHP_CMD")" ]]; then
        echo "$PHP_CMD"
        return
    fi

    for bin in php php8.4 php8.3 php8.2 php8.1 php8.0; do
        if command -v "$bin" >/dev/null 2>&1; then
            echo "$bin"
            return
        fi
    done

    log "No PHP binary found! Please install PHP or set PHP_CMD explicitly."
    exit 1
}

main "$@"
