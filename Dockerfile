# syntax=docker/dockerfile:1

# -----------------------------------------------------------------------------
# 1. Use official PHP 8.2 CLI image as the base. This variant includes the
#    built-in web server required for `php artisan serve`.
# -----------------------------------------------------------------------------
FROM php:8.2-cli

# -----------------------------------------------------------------------------
# 2. Install system packages, PHP extensions, Node.js (latest LTS), and Python.
#    Laravel needs common extensions like mbstring/xml/pdo_mysql; Python runtime
#    is required for PDF conversion scripts.
# -----------------------------------------------------------------------------
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libicu-dev \
        libonig-dev \
        libxml2-dev \
        python3 \
        python3-pip \
        curl \
        gnupg \
        ca-certificates; \
    curl -fsSL https://deb.nodesource.com/setup_22.x | bash -; \
    apt-get install -y --no-install-recommends nodejs; \
    docker-php-ext-install mbstring pdo_mysql xml; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

# -----------------------------------------------------------------------------
# 3. Install Composer globally for dependency management.
# -----------------------------------------------------------------------------
RUN curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin \
        --filename=composer

# -----------------------------------------------------------------------------
# 4. Set the working directory for the application code.
# -----------------------------------------------------------------------------
WORKDIR /app

# -----------------------------------------------------------------------------
# 5. Copy Composer manifests first to leverage Docker layer caching, then
#    install PHP dependencies without running scripts.
# -----------------------------------------------------------------------------
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-scripts --no-interaction

# -----------------------------------------------------------------------------
# 6. Copy Node manifests and install front-end dependencies.
# -----------------------------------------------------------------------------
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts

# -----------------------------------------------------------------------------
# 7. Copy the entire application source into the image.
# -----------------------------------------------------------------------------
COPY . .

# -----------------------------------------------------------------------------
# 8. Build front-end assets now that source files are present.
# -----------------------------------------------------------------------------
RUN npm run build

# -----------------------------------------------------------------------------
# 9. Install required Python packages for PDF conversion utilities.
# -----------------------------------------------------------------------------
RUN pip3 install --no-cache-dir --break-system-packages \
    pymupdf \
    google-genai \
    pillow

# -----------------------------------------------------------------------------
# 10. Ensure storage, cache, and log directories are writable inside the image.
# -----------------------------------------------------------------------------
RUN set -eux; \
    mkdir -p storage/app/public storage/framework/cache storage/framework/sessions \
             storage/framework/views storage/logs bootstrap/cache; \
    chown -R www-data:www-data storage bootstrap/cache; \
    chmod -R ug+rwX storage bootstrap/cache

# -----------------------------------------------------------------------------
# 11. Expose the port that Laravel will listen on inside Railway.
# -----------------------------------------------------------------------------
EXPOSE 8080

# -----------------------------------------------------------------------------
# 12. Default command: run Laravel's built-in server bound to 0.0.0.0:8080.
# -----------------------------------------------------------------------------
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
