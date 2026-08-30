# Deploying to Hostinger via Git

This repo's root doesn't map 1:1 onto a WordPress install — it also has
`README.md`, `ARCHITECTURE.md`, and `tools/`, which don't belong inside
`wp-content/`. So rather than pointing Hostinger's Git tool straight at
your live site (risky: cloning into a directory that already has
`wp-config.php`, `wp-admin/`, a database-backed `wp-content/uploads/`,
etc. is not something to do with a fresh `git clone`), this setup clones
the repo into a **separate staging folder outside your web root**, then a
Cron Job mirrors just the two folders that matter
(`wp-content/themes/tripdesh` and `wp-content/plugins/tripdesh-core`)
into your real WordPress install with `rsync`.

Nothing here ever touches `wp-config.php`, your database, uploads, or any
other plugin/theme. No GitHub secrets, no SSH keys checked into this
repo, no credentials of any kind live in this codebase — everything
below is configured entirely inside Hostinger's own hPanel UI, which
already has legitimate access to your own server.

**I (Claude) cannot access your Hostinger account.** The steps below are
the one-time setup only *you* can do, in hPanel. After that, updates
merged to this repo's deployment branch reach your live site automatically
on the schedule you pick — no further manual uploads.

## One-time setup (do this once in hPanel)

### 1. Find your paths

Log into hPanel → your website → **File Manager**, and note two absolute
paths (they usually look like this, with your real username/domain):

- Your home directory, e.g. `/home/u123456789`
- Your WordPress root (where `wp-config.php` lives), e.g.
  `/home/u123456789/domains/yourdomain.com/public_html`

### 2. Connect the repository (hPanel → Advanced → Git)

- **Repository URL**: `https://github.com/mrahmanrumel-bot/bd-name-service`
- **Branch**: `claude/bangladesh-travel-agency-ota-njbjv0` (or `main`,
  once this branch is merged there — use whichever branch you want live)
- **Install path**: a *new, empty* folder outside `public_html`, e.g.
  `tripdesh-src` (resolves to `/home/u123456789/tripdesh-src`) — do **not**
  point this at `public_html` or any existing WordPress files.
- If the repo is private, Hostinger's Git tool will prompt for a deploy
  key or GitHub App connection at this step — follow Hostinger's own
  prompt; that credential stays inside hPanel, never in this repo.
- Click **Pull/Deploy** once to confirm it clones successfully. If
  Hostinger's plan supports it, enable the option to auto-pull on a
  webhook or schedule — otherwise the Cron Job below (step 4) also
  handles pulling.

### 3. Do the first sync manually once

Over SSH (hPanel → Advanced → SSH Access) or hPanel's Terminal:

```bash
SRC_DIR=/home/u123456789/tripdesh-src \
WP_ROOT=/home/u123456789/domains/yourdomain.com/public_html \
bash /home/u123456789/tripdesh-src/deploy/hostinger-sync.sh
```

(substitute your real paths from step 1). This copies the theme and
plugin into place for the first time.

### 4. Activate in WordPress (one-time, manual — this is the one step you must do yourself)

WordPress needs a human click to activate a theme/plugin the *first*
time; it won't auto-activate from a file sync. In **WP Admin**:

- **Appearance → Themes** → activate **Tripdesh**
- **Plugins** → activate **Tripdesh Core**
- **Settings → Permalinks** → click Save once (flushes rewrite rules)

Everything else in the main README's install steps (creating pages,
Settings → Tripdesh, etc.) still applies — this automation only replaces
the "upload the ZIP" step, not the WordPress-side configuration.

### 5. Automate future syncs with a Cron Job

hPanel → **Advanced → Cron Jobs** → Create a new cron job:

- **Schedule**: every 15–30 minutes is plenty for a low-traffic content
  site; hourly is fine too.
- **Command**:
  ```bash
  cd /home/u123456789/tripdesh-src && git pull && SRC_DIR=/home/u123456789/tripdesh-src WP_ROOT=/home/u123456789/domains/yourdomain.com/public_html bash /home/u123456789/tripdesh-src/deploy/hostinger-sync.sh
  ```
  (again, substitute your real paths)

From now on: push a commit to the deployment branch on GitHub → within
one cron cycle, Hostinger pulls it and the script mirrors the theme/plugin
into your live site. No manual ZIP upload needed for code changes.
Content you create in WP Admin (destinations, tours, pages, settings) is
database-stored and completely unaffected by this — the sync only ever
touches the two code folders.

## What this does NOT automate

- **Activating a brand-new theme/plugin for the first time** (step 4
  above) — a one-time manual click.
- **Database changes** — none of this touches the database; there's
  nothing to migrate in Phase 1.
- **Content authoring** — destinations, tours, pages, settings are your
  content, created through WP Admin, not through git.
- **Rollback** — if a bad commit reaches your live site, the fastest fix
  is `git revert` on GitHub (or push a fixed commit) and let the next
  cron cycle sync the corrected files; there's no automatic health check
  or auto-rollback here.

## Before you turn this on

Take a Hostinger backup/snapshot (hPanel → **Backups**) of your site
first. This automation only ever writes to `wp-content/themes/tripdesh`
and `wp-content/plugins/tripdesh-core`, but a backup costs nothing and
means any mistake is trivially reversible.
