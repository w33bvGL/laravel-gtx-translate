FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    openssl \
    git \
    wget \
    libssl-dev \
    pkg-config \
    libpng-dev \
    bash \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd xml \
    && wget https://phpdoc.org/phpDocumentor.phar -O /usr/local/bin/phpdoc \
    && chmod +x /usr/local/bin/phpdoc

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY _docker/php.ini /usr/local/etc/php/

WORKDIR /data

COPY . .

RUN composer install

EXPOSE 8000
