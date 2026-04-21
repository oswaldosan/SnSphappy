# syntax=docker/dockerfile:1.7
#
# Snazzy Sprocket — production image (Railway)
# =============================================
# Based on `serversideup/php` — a production-grade PHP-FPM + Nginx
# image maintained specifically for managed container platforms
# (Railway, Fly, DO App Platform). No mod_php, no MPM conflicts,
# runs as non-root, healthchecks built in.
#
# Three stages:
#   1. `assets`  — Tailwind + Alpine bundle via Vite (Node 20).
#   2. `vendor`  — Composer install (no dev).
#   3. `runtime` — WordPress + plugins + our theme on top of
#                  `serversideup/php:8.2-fpm-nginx`.
#
# Target port: 8080 (serversideup default). In Railway, set the
# service's public port to 8080 — no dynamic-PORT gymnastics needed.

# ─── Stage 1 · Front-end assets ───────────────────────────────────
FROM node:20-alpine AS assets
WORKDIR /build
COPY package.json package-lock.json* ./
RUN npm ci --no-audit --no-fund
COPY tailwind.config.js postcss.config.js vite.config.js ./
COPY src ./src
COPY views ./views
COPY *.php ./
RUN npm run build

# ─── Stage 2 · PHP dependencies ───────────────────────────────────
FROM composer:2 AS vendor
WORKDIR /build
COPY composer.json composer.lock* ./
RUN composer install \
        --no-dev \
        --no-scripts \
        --optimize-autoloader \
        --prefer-dist

# ─── Stage 3 · WordPress runtime ──────────────────────────────────
FROM serversideup/php:8.2-fpm-nginx AS runtime

# Tell serversideup's Nginx to serve from the WP root (default is
# `/var/www/html/public`, which doesn't match WordPress layout).
ENV NGINX_WEBROOT=/var/www/html \
    PHP_OPCACHE_ENABLE=1 \
    SSL_MODE=off \
    LOG_OUTPUT_LEVEL=info

USER root

# serversideup ships `pdo_mysql` + `mysqlnd` but NOT the `mysqli`
# extension, and WordPress explicitly requires `mysqli` (see
# `wp-includes/class-wpdb.php`). `install-php-extensions` is baked
# into every serversideup image for exactly this purpose.
RUN set -eux; \
    install-php-extensions mysqli; \
    apt-get update; \
    apt-get install -y --no-install-recommends unzip curl less; \
    rm -rf /var/lib/apt/lists/*

# WP-CLI for post-deploy admin tasks (`railway run wp …`).
RUN set -eux; \
    curl -fsSL -o /usr/local/bin/wp \
        https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar; \
    chmod +x /usr/local/bin/wp

# WordPress core.
WORKDIR /var/www/html
RUN set -eux; \
    curl -fsSL https://wordpress.org/latest.tar.gz \
        | tar -xzf - --strip-components=1; \
    rm -rf wp-content/themes/* \
           wp-content/plugins/akismet \
           wp-content/plugins/hello.php

# Plugins (pinned to `.latest-stable` — swap to exact versions for
# strict release branches).
RUN set -eux; \
    cd wp-content/plugins; \
    curl -fsSL -o acf.zip https://downloads.wordpress.org/plugin/advanced-custom-fields.latest-stable.zip; \
    unzip -q acf.zip && rm acf.zip; \
    curl -fsSL -o cf7.zip https://downloads.wordpress.org/plugin/contact-form-7.latest-stable.zip; \
    unzip -q cf7.zip && rm cf7.zip

# Our theme (respects `.dockerignore`).
COPY --chown=www-data:www-data . /var/www/html/wp-content/themes/snazzy-sprocket
COPY --from=assets --chown=www-data:www-data /build/dist \
    /var/www/html/wp-content/themes/snazzy-sprocket/dist
COPY --from=vendor --chown=www-data:www-data /build/vendor \
    /var/www/html/wp-content/themes/snazzy-sprocket/vendor

# Drop build-only files from inside the theme dir.
RUN set -eux; \
    cd /var/www/html/wp-content/themes/snazzy-sprocket; \
    rm -rf Dockerfile .dockerignore .wp-env.json .github .gitignore \
           docker node_modules package.json package-lock.json \
           postcss.config.js tailwind.config.js vite.config.js \
           bin/seed.sh README.md

# Custom `wp-config.php` reads DB + salts from env vars and trusts
# Railway's TLS-terminating edge proxy.
COPY docker/wp-config.php /var/www/html/wp-config.php

RUN chown -R www-data:www-data /var/www/html

USER www-data

EXPOSE 8080
