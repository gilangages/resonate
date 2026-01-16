# Gunakan PHP 8.2 dengan Apache
FROM php:8.2-apache

# Install dependency system & Driver PostgreSQL (PENTING BUAT NEON)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql bcmath zip

# Aktifkan mod_rewrite untuk URL Laravel yang cantik
RUN a2enmod rewrite

# Ganti Document Root Apache ke folder public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf .htaccess

# Copy Composer (Manajer Paket PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set folder kerja
WORKDIR /var/www/html

# Copy semua kodingan ke dalam container
COPY . .

# Install paket PHP via Composer
RUN composer install --no-dev --optimize-autoloader

# Beri izin agar server bisa nulis ke folder storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Buka Port 80
EXPOSE 80
