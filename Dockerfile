# syntax=docker/dockerfile:1.7
#
# Snazzy Sprocket — production image for Railway
# ===============================================
# Multi-stage build:
#   1. `assets`  — builds Tailwind + Alpine bundle with Vite (Node 20).
#   2. `vendor`  — installs PHP dependencies via Composer (no dev).
#   3. `runtime` — official WordPress + Apache + PHP 8.2 image, with
#                  the theme, built assets, vendor, and pinned
#                  plugins (ACF, Contact Form 7) baked in.
#
# The runtime entrypoint patches Apache to listen on Railway's
# dynamic `$PORT` and trusts the `X-Forwarded-Proto` header from
# the Railway edge proxy so WordPress generates https URLs.

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
FROM wordpress:php8.2-apache AS runtime

ENV THEME_DIR=/usr/src/wordpress/wp-content/themes/snazzy-sprocket \
    PLUGIN_DIR=/usr/src/wordpress/wp-content/plugins

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        unzip \
        curl \
        less; \
    rm -rf /var/lib/apt/lists/*

# WP-CLI (handy for one-off admin tasks: `docker exec <container> wp …`)
RUN set -eux; \
    curl -fsSL -o /usr/local/bin/wp \
        https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar; \
    chmod +x /usr/local/bin/wp

# Pinned plugins. Update these URLs to lock specific versions on
# release branches; `latest-stable` is fine for rolling deployments.
RUN set -eux; \
    cd "$PLUGIN_DIR"; \
    curl -fsSL -o acf.zip https://downloads.wordpress.org/plugin/advanced-custom-fields.latest-stable.zip; \
    unzip -q acf.zip && rm acf.zip; \
    curl -fsSL -o cf7.zip https://downloads.wordpress.org/plugin/contact-form-7.latest-stable.zip; \
    unzip -q cf7.zip && rm cf7.zip; \
    chown -R www-data:www-data "$PLUGIN_DIR"

# Theme source (respects `.dockerignore`).
COPY --chown=www-data:www-data . "$THEME_DIR"

# Built artifacts from the earlier stages.
COPY --from=assets --chown=www-data:www-data /build/dist "$THEME_DIR/dist"
COPY --from=vendor --chown=www-data:www-data /build/vendor "$THEME_DIR/vendor"

# Drop files the container will never need.
RUN set -eux; \
    cd "$THEME_DIR"; \
    rm -rf \
        Dockerfile \
        .dockerignore \
        .wp-env.json \
        docker \
        node_modules \
        package.json \
        package-lock.json \
        postcss.config.js \
        tailwind.config.js \
        vite.config.js \
        bin/seed.sh

RUN a2enmod rewrite headers expires

COPY docker/railway-entrypoint.sh /usr/local/bin/railway-entrypoint.sh
RUN chmod +x /usr/local/bin/railway-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["railway-entrypoint.sh"]
CMD ["apache2-foreground"]
