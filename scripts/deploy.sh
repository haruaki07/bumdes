#!/bin/sh
set -e

VENDOR_ARCHIVE=$1
ASSETS_ARCHIVE=$2

main() {
    # unzip composer deps
    if [ -f "$VENDOR_ARCHIVE" ]; then
        log "Extracting composer dependencies..."
        tar -xJf $VENDOR_ARCHIVE
        rm -f $VENDOR_ARCHIVE
    fi

    # unzip assets
    if [ -f "$ASSETS_ARCHIVE" ]; then
        log "Extracting assets..."
        tar -xJf $ASSETS_ARCHIVE
        rm -f $ASSETS_ARCHIVE
    fi

    log "Deploying app with commit ($(git rev-parse --short HEAD))..."

    # if .env was not found, abort
    if [ ! -f ".env" ]; then
        log "Deploy cancelled!\nThe .env file is missing! Please configure it manually and then redeploy."
        return
    fi

    log "Putting app into maintenance mode..."
    php artisan down --no-ansi --no-interaction

    log "Installing composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader

    log "Migrating database..."
    php artisan migrate --force --no-ansi --no-interaction

    log "Clearing cache bootstrap files..."
    php artisan optimize:clear --no-ansi --no-interaction

    log "Caching bootstrap files..."
    php artisan optimize --no-ansi --no-interaction
    php artisan view:cache --no-ansi --no-interaction

    log "Restoring app..."
    php artisan up --no-ansi --no-interaction

    log "Deploy finished!"
}

log() {
    local message="$1"
    local log_file="$PWD/storage/logs/deploy.log"
    local timestamp="$(date +"%Y-%m-%d %H:%M:%S")"
    local log_entry="[$timestamp] $message"

    echo $message
    echo "$log_entry" | tee -a "$log_file" > /dev/null
}

main "$@"