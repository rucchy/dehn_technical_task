FROM php:8.2-fpm

# Install system dependencies and clean cache
RUN apt-get update && apt-get install -y \
 git \
 curl \
 libpng-dev \
 libonig-dev \
 libxml2-dev \
 zip \
 unzip

RUN useradd -ms /bin/bash  dehn
USER dehn

# Copy Composer from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /usr/local/src