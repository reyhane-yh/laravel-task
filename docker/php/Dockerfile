FROM php:8.3-cli

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libonig-dev \
    unzip curl zip \
    && docker-php-ext-install pdo_mysql zip mbstring

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy Laravel app code
COPY . .

# Install dependencies
RUN composer install

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
