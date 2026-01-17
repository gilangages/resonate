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

# === BAGIAN PENTING: CONFIG APACHE ===
# Copy config Apache buatan kita ke dalam server
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
# =====================================

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set folder kerja
WORKDIR /var/www/html

# Copy semua kodingan ke dalam container
COPY . .

# Install paket PHP via Composer
RUN composer install --no-dev --optimize-autoloader

# Beri izin agar deploy.sh bisa dijalankan
RUN chmod +x deploy.sh

# Buat folder views agar artisan view:cache tidak error
RUN mkdir -p /var/www/html/resources/views

# Beri izin folder storage agar Laravel bisa nulis log & cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Buka Port 80
EXPOSE 80
