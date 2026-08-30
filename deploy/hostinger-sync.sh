#!/usr/bin/env bash
#
# Syncs the tripdesh theme and tripdesh-core plugin from a git checkout of
# this repo into a live WordPress install's wp-content directories.
#
# This script does NOT clone or pull anything itself — that's Hostinger's
# native Git feature's job (hPanel -> Advanced -> Git), pulling this repo
# into $SRC_DIR on a schedule or via its own webhook. This script just runs
# after that pull (from a Hostinger Cron Job) and mirrors the two relevant
# folders into place with rsync --delete, so removed files get cleaned up
# too, not just new/changed ones.
#
# It touches ONLY wp-content/themes/tripdesh and
# wp-content/plugins/tripdesh-core under $WP_ROOT — nothing else on the
# server (not wp-config.php, not the database, not other plugins/themes).
#
# See deploy/HOSTINGER.md for the one-time hPanel setup this depends on.
#
# Configure via environment variables (set them in the Cron Job command
# itself, or export them above the call in a wrapper — never hardcode a
# real path with credentials in this file):
#   SRC_DIR  - absolute path Hostinger's Git tool clones this repo into
#              e.g. /home/u123456789/tripdesh-src
#   WP_ROOT  - absolute path to the WordPress install root
#              e.g. /home/u123456789/domains/example.com/public_html
#
# Example Cron Job command:
#   SRC_DIR=/home/u123456789/tripdesh-src WP_ROOT=/home/u123456789/domains/example.com/public_html bash /home/u123456789/tripdesh-src/deploy/hostinger-sync.sh

set -euo pipefail

if [ -z "${SRC_DIR:-}" ] || [ -z "${WP_ROOT:-}" ]; then
	echo "ERROR: SRC_DIR and WP_ROOT must both be set. See the header of this script." >&2
	exit 1
fi

if [ ! -d "$SRC_DIR/wp-content/themes/tripdesh" ]; then
	echo "ERROR: $SRC_DIR/wp-content/themes/tripdesh not found — has Hostinger's Git tool pulled this repo into SRC_DIR yet?" >&2
	exit 1
fi

if [ ! -d "$WP_ROOT/wp-content" ]; then
	echo "ERROR: $WP_ROOT/wp-content not found — WP_ROOT does not look like a WordPress install." >&2
	exit 1
fi

mkdir -p "$WP_ROOT/wp-content/themes/tripdesh" "$WP_ROOT/wp-content/plugins/tripdesh-core"

echo "[$(date -u +%FT%TZ)] Syncing theme..."
rsync -a --delete \
	"$SRC_DIR/wp-content/themes/tripdesh/" \
	"$WP_ROOT/wp-content/themes/tripdesh/"

echo "[$(date -u +%FT%TZ)] Syncing plugin..."
rsync -a --delete \
	"$SRC_DIR/wp-content/plugins/tripdesh-core/" \
	"$WP_ROOT/wp-content/plugins/tripdesh-core/"

echo "[$(date -u +%FT%TZ)] Sync complete."
