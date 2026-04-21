# Snazzy Sprocket — Custom WordPress Theme

A production-grade custom WordPress theme for **Snazzy Sprocket**, a fictional design and development agency. Built with **Timber/Twig**, **Tailwind CSS**, **ACF**, **Alpine.js**, and shipped with a Dockerfile for one-click **Railway** deployment.

**🔗 Live preview:** <https://snsphappy-production.up.railway.app/>

WordPress PHP Tailwind Timber Railway

---

## Table of Contents

1. [Stack](#stack)
2. [Local Development](#local-development)
3. [Architecture](#architecture)
4. [CMS Model](#cms-model)
5. [Admin Behavior](#admin-behavior)
6. [Pages & Features](#pages--features)
7. [Production Deployment · Railway](#production-deployment--railway)
8. [Environment Variables](#environment-variables)
9. [Commands Cheatsheet](#commands-cheatsheet)
10. [AI Tools Usage](#ai-tools-usage)
11. [License](#license)

---

## Stack


| Layer              | Technology                | Purpose                                                     |
| ------------------ | ------------------------- | ----------------------------------------------------------- |
| CMS                | WordPress 6.x             | Content management, editor experience                       |
| Templating         | Timber v2 + Twig          | MVC split — PHP controllers, Twig views                     |
| Styling            | Tailwind CSS 3.4          | Utility-first, custom design tokens mirrored from Figma     |
| Interactivity      | Alpine.js 3.x             | Mobile nav, case-study filters                              |
| Custom Fields      | Advanced Custom Fields    | Structured editor content, version-controlled via JSON sync |
| Contact Form       | Contact Form 7            | Styled via Tailwind, seeded shortcode `[cf7-main]`          |
| Build Tool         | Vite 5                    | CSS/JS bundling, produces hashed `dist/assets/…` artifacts  |
| Local Dev          | @wordpress/env            | Docker-powered zero-config WordPress environment            |
| Production Runtime | Docker (Nginx + PHP-FPM 8.2) | `serversideup/php:8.2-fpm-nginx` — production-grade, non-root, no mod_php |
| Hosting            | Railway                   | Managed container + MySQL, TLS-terminated at the edge       |


---

## Local Development

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) running
- [Node.js 20+](https://nodejs.org/) (use `nvm` or `fnm`)
- [Composer 2](https://getcomposer.org/)

### Setup

```bash
# 1. Install dependencies
composer install
npm install

# 2. Build front-end assets
npm run build

# 3. Start the local WordPress environment
npm run wp:start
```

Visit [http://localhost:8888](http://localhost:8888) — admin at `/wp-admin` with `admin` / `password`.

### Seed Demo Content

Populate pages, case studies, team members, menus, taxonomy terms, ACF meta, and the CF7 form:

```bash
wp-env run cli bash /var/www/html/wp-content/themes/snazzy-sprocket/bin/seed.sh
```

### Day-to-Day Workflow

```bash
npm run dev      # Vite dev server for CSS/JS
npm run build    # Production bundle (required after CSS changes if not on dev server)
npm run wp:cli   # Run arbitrary WP-CLI commands
```

> **Note on Vite + WordPress:** the theme loads built assets straight from `dist/` (driven by `dist/.vite/manifest.json`). The Vite dev server is convenient for watching CSS changes, but WordPress does **not** inject the Vite HMR client — run `npm run build` to see CSS updates in the WP-rendered pages.

---

## Architecture

### File Structure

```
snazzy-sprocket/
├── src/                          PHP classes (PSR-4 autoloaded)
│   ├── StarterSite.php           Theme bootstrap — setup, context, assets
│   ├── PostTypes/                CPT registrations
│   │   ├── CaseStudy.php
│   │   └── TeamMember.php
│   └── Taxonomies/               Taxonomy registrations
│       ├── Industry.php
│       └── Technology.php
├── inc/                          Procedural helpers and admin tweaks
│   ├── helpers.php               Hero-headline sanitization helpers
│   ├── acf-location.php          Custom ACF `page_slug` location rule
│   └── admin-editor.php          Gutenberg off on pages, classic editor hidden
├── views/                        Twig templates
│   ├── base.twig                 Master layout
│   ├── front-page.twig           Homepage
│   ├── page-about.twig           About page
│   ├── page-contact.twig         Contact page
│   ├── archive-case_study.twig   Case study listing + filters
│   ├── single-case_study.twig    Single case study
│   ├── components/               Reusable UI components
│   │   ├── button.twig
│   │   ├── callout.twig
│   │   ├── case-study-hero.twig
│   │   ├── home-hero.twig
│   │   ├── image-gallery.twig
│   │   ├── meta-item.twig
│   │   ├── page-hero.twig
│   │   ├── section-header.twig
│   │   ├── stat-card.twig
│   │   └── testimonial.twig
│   └── partials/
│       ├── header.twig
│       ├── footer.twig
│       ├── case-study-card.twig
│       └── team-member-card.twig
├── acf-json/                     ACF field groups (git-tracked)
├── src/css/main.css              Tailwind entry
├── src/js/app.js                 Alpine.js entry
├── bin/seed.sh                   Demo-content seeder (WP-CLI)
├── docker/
│   └── wp-config.php             Env-driven wp-config (DB + salts + X-Forwarded-Proto trust)
├── Dockerfile                    Production image (multi-stage)
├── .dockerignore                 Keep image slim
├── .wp-env.json                  Local dev environment
├── composer.json / composer.lock PHP deps (Timber)
├── package.json / package-lock   JS deps (Vite, Tailwind, Alpine)
├── tailwind.config.js            Design tokens
├── vite.config.js                Bundle config
├── front-page.php                Homepage controller
├── page-about.php                About controller
├── page-contact.php              Contact controller
├── archive-case_study.php        Case-study archive controller
├── single-case_study.php         Single-case-study controller
└── functions.php                 Theme entrypoint (autoload, Timber init, includes)
```

### MVC Flow (Timber)

```
HTTP request  →  PHP controller  →  Timber context  →  Twig template  →  HTML
                 (page-*.php)       (data + helpers)   (views/*.twig)
```

Each controller is a thin layer that:

1. Builds a context via `Timber::context()`.
2. Pulls ACF meta + related queries into that context.
3. Renders a Twig template.

---

## CMS Model

### Custom Post Types


| Post Type   | Slug          | Purpose                                               |
| ----------- | ------------- | ----------------------------------------------------- |
| Case Study  | `case_study`  | Portfolio projects with client, gallery, testimonials |
| Team Member | `team_member` | Staff profiles (surfaced on the About page team grid) |


### Custom Taxonomies


| Taxonomy   | Slug         | Attached To  | Style                        |
| ---------- | ------------ | ------------ | ---------------------------- |
| Industry   | `industry`   | `case_study` | Hierarchical (category-like) |
| Technology | `technology` | `case_study` | Flat (tag-like)              |


### ACF Field Groups

All groups live in `acf-json/` and are loaded via ACF's JSON sync.


| Field Group                         | Location rule                   | Purpose                                        |
| ----------------------------------- | ------------------------------- | ---------------------------------------------- |
| `group_homepage_fields`             | `page_type == front_page`       | Hero copy, stats, services, featured work, CTA |
| `group_about_page_fields`           | `page_slug == about`            | About hero, story, values, team, join CTA      |
| `group_contact_page_fields`         | `page_slug == contact`          | Contact hero + CF7 shortcode                   |
| `group_case_studies_archive_fields` | `page_slug == case-studies`     | Archive hero (eyebrow, headline, lede)         |
| `group_case_study_details`          | `post_type == case_study`       | Client meta, gallery, testimonial, results     |
| `group_team_member_details`         | `post_type == team_member`      | Role, bio, LinkedIn, Twitter                   |
| `group_site_settings`               | `options_page == site-settings` | Global: email, phone, address, social, footer  |


> **Custom location rule — `page_slug`**
> ACF's stock `page` rule compares numeric page IDs, which drift between environments. `inc/acf-location.php` registers a `page_slug` rule that compares `$post->post_name` so field-group targeting is portable across local / staging / prod.

---

## Admin Behavior

A few conventions applied in `inc/admin-editor.php`:

- **Gutenberg disabled for the `page` post type** — pages are ACF-driven, the block editor would be noise above the field tabs.
- **Classic editor removed on pages** — `remove_post_type_support('page', 'editor')` so the blank content box doesn't tempt editors to paste body copy that would never render in Twig.
- **Posts + `case_study` keep Gutenberg** — long-form content types where the block editor earns its keep.

To reintroduce body content on a page, add a WYSIWYG ACF field rather than re-enabling the editor.

---

## Pages & Features

### Homepage `/`

Dark 2-column hero (`components/home-hero.twig`) with animated marquee headline, lede, dual CTAs, and a 4-card stats rail. Followed by services, featured case studies (editor-picked, fallback to latest 3), and a CTA banner.

### About `/about`

Narrative hero reused from `components/page-hero.twig`, two-column story section, 4-card values grid, team roster (`team_member` CPT, ordered by `menu_order`), and a “Join the team” CTA.

### Case Studies `/case-studies`

Front-end filters (Alpine.js) with **combined AND logic** across Industry + Technology. Empty state with a “Clear Filters” action. Hero copy is editable via the shadow **Case Studies** page record (slug `case-studies`) → ACF group `group_case_studies_archive_fields`.

### Single Case Study `/case-studies/{slug}`

Dark editorial hero (`components/case-study-hero.twig`) with tags + title + lede, overlapping hero image, client/timeline/services meta row, body copy, Impact callout (`components/callout.twig`), client testimonial (`components/testimonial.twig`), optional gallery (`components/image-gallery.twig`), and related work.

### Contact `/contact`

Two-column layout: Contact Form 7 + sidebar with company info from the Site Settings options page. Google Maps embed below the form.

---

## Production Deployment · Railway

The repo ships a production-ready `Dockerfile` built on [`serversideup/php`](https://serversideup.net/open-source/docker-php/) — a hardened PHP-FPM + Nginx image used in production by many managed-container deployments. Non-root, no Apache MPM drama, opcache on by default.

### What the image contains

- `serversideup/php:8.2-fpm-nginx` base (Nginx + PHP-FPM 8.2)
- WordPress core (fetched from `wordpress.org/latest` at build time)
- Plugins: Advanced Custom Fields, Contact Form 7
- This theme at `wp-content/themes/snazzy-sprocket`
- Built Tailwind / Alpine bundle at `wp-content/themes/snazzy-sprocket/dist`
- Composer vendor (Timber) at `wp-content/themes/snazzy-sprocket/vendor`
- Custom `wp-config.php` that reads DB + salts from env and trusts Railway's TLS edge
- WP-CLI (`wp`) for one-off maintenance via `railway run`

### Deploy in ~5 steps

1. **Push this repo to GitHub.**
2. **Create a Railway project** and add two services:
  - **MySQL** (Railway template).
  - **Web** — “Deploy from GitHub repo”, point at this repository. Railway will detect the `Dockerfile` and build automatically.
3. **Set the public port to `8080`** in the Web service → Settings → Networking. The image listens on 8080 (Nginx non-root).
4. **Add a persistent volume** to the Web service, mounted at:
   ```
   /var/www/html/wp-content/uploads
   ```
   This keeps user-uploaded media across deploys.
5. **Set the Web service environment variables** (see [Environment Variables](#environment-variables)). At minimum, wire WP's DB vars to Railway's MySQL service:
   ```
   WORDPRESS_DB_HOST      = ${{MySQL.MYSQLHOST}}:${{MySQL.MYSQLPORT}}
   WORDPRESS_DB_NAME      = ${{MySQL.MYSQLDATABASE}}
   WORDPRESS_DB_USER      = ${{MySQL.MYSQLUSER}}
   WORDPRESS_DB_PASSWORD  = ${{MySQL.MYSQLPASSWORD}}
   WORDPRESS_TABLE_PREFIX = wp_
   ```
   Then generate salts at <https://api.wordpress.org/secret-key/1.1/salt/> and set each as `WORDPRESS_AUTH_KEY`, `WORDPRESS_SECURE_AUTH_KEY`, `WORDPRESS_LOGGED_IN_KEY`, `WORDPRESS_NONCE_KEY`, `WORDPRESS_AUTH_SALT`, `WORDPRESS_SECURE_AUTH_SALT`, `WORDPRESS_LOGGED_IN_SALT`, `WORDPRESS_NONCE_SALT`.
6. **Deploy.** Visit the Railway URL, run the WordPress installer, then activate:
   - Theme: **Snazzy Sprocket**
   - Plugins: **Advanced Custom Fields**, **Contact Form 7**

### Local test of the production image

```bash
# Build
docker build -t snazzy-sprocket:prod .

# Run against a local MySQL (adjust credentials)
docker run --rm -p 8080:8080 \
  -e WORDPRESS_DB_HOST=host.docker.internal:3306 \
  -e WORDPRESS_DB_NAME=snazzy \
  -e WORDPRESS_DB_USER=root \
  -e WORDPRESS_DB_PASSWORD=root \
  snazzy-sprocket:prod
```

Visit [http://localhost:8080](http://localhost:8080).

### How the image is wired

- Nginx listens on **8080** (serversideup default, non-root).
- `NGINX_WEBROOT=/var/www/html` points Nginx at the WP install instead of the default `/var/www/html/public`.
- `docker/wp-config.php` pulls DB credentials from `WORDPRESS_DB_*` envs, honors `WORDPRESS_DEBUG`, evaluates `WORDPRESS_CONFIG_EXTRA`, and trusts `X-Forwarded-Proto` so WP emits `https://` URLs behind Railway's edge.

---

## Environment Variables


| Name                     | Required | Purpose                                                                                                                                   |
| ------------------------ | -------- | ----------------------------------------------------------------------------------------------------------------------------------------- |
| `WORDPRESS_DB_HOST`      | Yes      | `host:port` of the MySQL service. Map to `${{MySQL.MYSQLHOST}}:${{MySQL.MYSQLPORT}}` on Railway.                                          |
| `WORDPRESS_DB_NAME`      | Yes      | Database name.                                                                                                                            |
| `WORDPRESS_DB_USER`      | Yes      | Database user.                                                                                                                            |
| `WORDPRESS_DB_PASSWORD`  | Yes      | Database password.                                                                                                                        |
| `WORDPRESS_TABLE_PREFIX` | No       | Table prefix. Defaults to `wp_`.                                                                                                          |
| `WORDPRESS_DEBUG`        | No       | Set `1` for `WP_DEBUG = true`. Leave unset on production.                                                                                 |
| `WORDPRESS_CONFIG_EXTRA` | No       | Extra PHP eval'd by `wp-config.php` after constants are defined. The `X-Forwarded-Proto` trust is already wired — use this for custom tweaks. |
| `WORDPRESS_AUTH_KEY` … `WORDPRESS_NONCE_SALT` | Recommended | The 8 WP salts. If unset the config falls back to insecure placeholders; sessions will reset on every restart. Generate at <https://api.wordpress.org/secret-key/1.1/salt/>. |


Optional hardening for production — set in `WORDPRESS_CONFIG_EXTRA`:

```php
define('DISALLOW_FILE_EDIT', true);
define('AUTOMATIC_UPDATER_DISABLED', true);
define('WP_AUTO_UPDATE_CORE', false);
```

---

## Commands Cheatsheet

### Local

```bash
npm run wp:start      # Start local WordPress
npm run wp:stop       # Stop it
npm run wp:destroy    # Tear down (drops DB)
npm run wp:logs       # Tail logs
npm run wp:cli        # WP-CLI passthrough
npm run dev           # Vite dev server (CSS/JS watch)
npm run build         # Production bundle
```

### Docker / Railway

```bash
docker build -t snazzy-sprocket:prod .         # Build image
docker run --rm -p 8080:8080 snazzy-sprocket:prod # Smoke test
railway run wp <any-wp-cli-command>            # WP-CLI in the deployed container
railway logs                                   # Tail production logs
```

---

## AI Tools Usage

### Tools Used

- **Claude (Anthropic)** — architecture planning, code generation, documentation, and iterative debugging.

### What AI was used for

- Designing the CPT / taxonomy / ACF schema for the editor experience.
- Generating the Timber/Twig template scaffolding and component split.
- Translating Figma tokens into the Tailwind config (colors, typography, spacing).
- Building the Alpine-powered case-study filter logic.
- Drafting the demo-content seeder (`bin/seed.sh`).
- Authoring this README and the Dockerfile / Railway entrypoint.

### What required manual work

- Figma-to-code translation, pixel-level adjustments, and contrast tuning.
- Mobile responsiveness debugging (hero padding, mobile nav stacking, Alpine `x-cloak` flashes).
- Diagnosing the ACF `page` → `page_slug` location-rule mismatch.
- Resolving the accidental double-render caused by a page controller `require_once`-ing the homepage controller (extracted helpers into `inc/helpers.php`).
- Verifying each Twig component against its Figma frame and reworking the stats rail to match.

### Quality verification

- All field groups tested with and without ACF data (graceful fallbacks in every controller).
- Responsive testing at 375px, 768px, 1024px, and 1440px.
- `WP_DEBUG` enabled throughout local development to surface notices.
- Built image smoke-tested locally against a disposable MySQL before Railway deploy.

---

## License

GPL-2.0-or-later — [License](http://www.gnu.org/licenses/gpl-2.0.html)