FROM php:8.3-fpm

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
    jpegoptim optipng pngquant gifsicle webp \
    libmagickwand-dev --no-install-recommends

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip sockets

# Imagick
ARG IMAGICK_PHP83_FIX_COMMIT=9df92616f577e38625b96b7b903582a46c064739
RUN apt-get -q update \
    && apt-get -yq upgrade \
    && export PHP_DEV_DEPS='libmagickwand-dev' \
    && apt-get -yq install --no-install-recommends libmagickwand-6.q16-6 $PHP_DEV_DEPS \
    # Install Imagick from specific archive (PR's #616 code)
    && curl -L https://github.com/remicollet/imagick/archive/${IMAGICK_PHP83_FIX_COMMIT}.zip -o /tmp/imagick-issue-php83.zip \
    && unzip /tmp/imagick-issue-php83.zip -d /tmp \
    && pecl install /tmp/imagick-${IMAGICK_PHP83_FIX_COMMIT}/package.xml \
    && apt-get purge -y $PHP_DEV_DEPS $PHPIZE_DEPS \
    && apt-get autoremove -y --purge \
    && apt-get clean all \
    && rm -Rf /tmp/*

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
RUN apt-get update
RUN apt-get install -y libpq-dev

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
