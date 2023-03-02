FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    jpegoptim optipng pngquant gifsicle \
    libmagickwand-dev --no-install-recommends

RUN pecl install imagick && docker-php-ext-enable imagick

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
RUN apt-get update
RUN apt-get install -y libpq-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip sockets

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ARG UNAME=www-data
ARG UGROUP=www-data
ARG UID=1000
ARG GID=1001
RUN usermod  --uid $UID $UNAME
RUN groupmod --gid $GID $UGROUP

WORKDIR /var/www
CMD ["php-fpm"]
