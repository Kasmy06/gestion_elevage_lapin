# syntax=docker/dockerfile:1

# --- Étape 1 : assets front (Vite + Tailwind) ---------------------------------
FROM node:20-bookworm-slim AS assets
WORKDIR /app

# Registre npm joignable depuis le réseau du FabLab (npmjs.org y est bloqué).
ARG NPM_REGISTRY=https://registry.yarnpkg.com
ENV NPM_CONFIG_REGISTRY=${NPM_REGISTRY}

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
# Tailwind scanne les vues Blade et les classes Livewire.
COPY app ./app
COPY public ./public
RUN npm run build

# --- Étape 2 : dépendances PHP --------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# Scripts désactivés ici : le code applicatif n'est pas encore copié.
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist \
    --no-interaction --ignore-platform-reqs

# --- Étape 3 : image finale (FrankenPHP) ---------------------------------------
FROM dunglas/frankenphp:1-php8.3-bookworm AS app
WORKDIR /app

RUN install-php-extensions pdo_mysql gd exif intl bcmath zip opcache \
    && apt-get update \
    && apt-get install -y --no-install-recommends unzip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --no-dev --optimize --classmap-authoritative \
    && mkdir -p storage/framework/cache/data storage/framework/sessions \
        storage/framework/views storage/logs storage/app/public bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache \
    && chmod +x docker/start.sh \
    && mkdir -p /data/caddy /config/caddy \
    && chown -R www-data:www-data /data /config

# Le hoster fournit PORT ; on écoute en HTTP simple (TLS terminé par Cloudflare/Traefik).
ENV PORT=8080 \
    APP_ENV=production \
    SERVER_NAME=:8080
EXPOSE 8080

USER www-data
CMD ["docker/start.sh"]
