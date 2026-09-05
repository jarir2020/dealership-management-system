#!/usr/bin/env bash
#
# start.sh — boot the Dealership Management System (Laravel 5.8) dev server.
#
# Usage:
#   ./start.sh                # serve on http://127.0.0.1:8000
#   ./start.sh 8080           # serve on http://127.0.0.1:8080
#   ./start.sh 0.0.0.0 8080   # serve on a custom host:port
#
# Notes:
#   - This is a Laravel 5.8 project that needs PHP 7.1+, but this machine only
#     has PHP 8.3 and 8.5. The vendor/ tree has been patched to work on PHP 8.x
#     (HandleExceptions.php silences deprecations, Carbon's Creator.php coerces
#     false → []). The server uses the same PHP binary that artisan has been
#     verified against.
#   - First-time setup is separate: copy .env.example → .env, set DB creds,
#     `composer install --ignore-platform-req=php`, import dms.sql, run
#     `php artisan migrate`, `php artisan key:generate`. start.sh assumes all
#     of that is already done.

set -euo pipefail

# --- Configuration ---------------------------------------------------------

# Pick the PHP binary. Prefer PHP 8.3 (closer to Laravel 5.8's expectations
# than 8.5 and what we've been testing against). Fall back to whatever `php`
# resolves to in the user's PATH.
PHP_BIN=""
for candidate in /home/jarir-ahmed/.local/bin/php /usr/bin/php; do
    if [ -x "$candidate" ]; then
        PHP_BIN="$candidate"
        break
    fi
done
if [ -z "$PHP_BIN" ] && command -v php >/dev/null 2>&1; then
    PHP_BIN="$(command -v php)"
fi
if [ -z "$PHP_BIN" ]; then
    echo "Error: no 'php' binary found in PATH" >&2
    exit 1
fi

HOST="${1:-127.0.0.1}"
PORT="${2:-8000}"

# --- Pre-flight checks -----------------------------------------------------

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

if [ ! -f ".env" ]; then
    echo "Error: .env not found in $SCRIPT_DIR" >&2
    echo "       Run: cp .env.example .env  (then set DB_DATABASE, DB_USERNAME, DB_PASSWORD)" >&2
    exit 1
fi

if [ ! -d "vendor" ]; then
    echo "Error: vendor/ directory missing. Run: composer install --ignore-platform-req=php" >&2
    exit 1
fi

if [ ! -x "artisan" ]; then
    echo "Note: 'artisan' is not executable; chmod +x artisan"
    chmod +x artisan
fi

# --- Boot ------------------------------------------------------------------

echo "Starting Laravel dev server on http://${HOST}:${PORT}"
echo "  PHP: $($PHP_BIN -r 'echo PHP_VERSION;')  ($PHP_BIN)"
echo "  App: $(grep '^APP_NAME=' .env | cut -d= -f2-)"
echo "  Env: $(grep '^APP_ENV=' .env | cut -d= -f2-)"
echo
echo "Press Ctrl+C to stop."
echo

exec "$PHP_BIN" artisan serve --host="$HOST" --port="$PORT"
