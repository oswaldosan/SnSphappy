#!/usr/bin/env bash
#
# Railway entrypoint shim for the `wordpress:apache` image.
#
# Responsibilities:
#   1. Switch Apache's listen port to Railway's `$PORT` (falls back
#      to 80 for local runs).
#   2. Inject a tiny `wp-config` snippet that trusts the
#      `X-Forwarded-Proto` header so WordPress emits https URLs
#      behind Railway's TLS-terminating edge.
#   3. Hand off to the stock WordPress entrypoint, which wires up
#      `wp-config.php` from the `WORDPRESS_DB_*` env vars and then
#      execs Apache (via `CMD`).
#
# Nothing here is WordPress-Docker-image-version specific — it's
# purely a pre-flight configuration patch.

set -euo pipefail

# ── 1. Apache listens on $PORT ────────────────────────────────────
PORT="${PORT:-80}"

sed -ri "s/^Listen [0-9]+$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s|<VirtualHost \\*:[0-9]+>|<VirtualHost *:${PORT}>|" \
    /etc/apache2/sites-available/000-default.conf

# ── 2. Trust Railway's edge TLS terminator ────────────────────────
FORWARDED_PROTO_SNIPPET=$'if (isset($_SERVER[\'HTTP_X_FORWARDED_PROTO\']) && $_SERVER[\'HTTP_X_FORWARDED_PROTO\'] === \'https\') { $_SERVER[\'HTTPS\'] = \'on\'; }'

# Preserve any user-provided extras; append only if not already set.
: "${WORDPRESS_CONFIG_EXTRA:=}"
if ! grep -q 'HTTP_X_FORWARDED_PROTO' <<<"$WORDPRESS_CONFIG_EXTRA"; then
    export WORDPRESS_CONFIG_EXTRA="${WORDPRESS_CONFIG_EXTRA}
${FORWARDED_PROTO_SNIPPET}"
fi

# ── 3. Hand off to the official WP entrypoint ─────────────────────
exec docker-entrypoint.sh "$@"
