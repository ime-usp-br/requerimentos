# Multi-stage build for Laravel application with Inertia and React
FROM node:20-alpine AS node-builder

WORKDIR /app

# Copy package files and install dependencies
COPY package*.json ./
RUN npm ci

# Copy source files and build assets
COPY . .
RUN npm run build

# PHP production image
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
	nginx \
	supervisor \
	mysql-client \
	zip \
	unzip \
	libzip-dev \
	libpng-dev \
	libjpeg-turbo-dev \
	freetype-dev \
	icu-dev \
	oniguruma-dev \
	unixodbc-dev \
	curl \
	autoconf \
	g++ \
	make \
	&& rm -rf /var/cache/apk/*

# Install Microsoft ODBC Driver 18 for SQL Server


RUN curl https://download.microsoft.com/download/fae28b9a-d880-42fd-9b98-d779f0fdd77f/msodbcsql18_18.5.1.1-1_amd64.apk -o msodbcsql18.apk \
	&& curl https://download.microsoft.com/download/7/6/d/76de322a-d860-4894-9945-f0cc5d6a45f8/mssql-tools18_18.4.1.1-1_amd64.apk -o mssql-tools18.apk \
	&& apk add --allow-untrusted msodbcsql18.apk mssql-tools18.apk \
	&& rm -f msodbcsql18.apk mssql-tools18.apk

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) \
	pdo_mysql \
	mysqli \
	zip \
	gd \
	intl \
	mbstring \
	opcache \
	bcmath

# Install SQL Server PHP drivers (pdo_sqlsrv and sqlsrv)
RUN pecl install sqlsrv pdo_sqlsrv \
	&& docker-php-ext-enable sqlsrv pdo_sqlsrv \
	&& apk del autoconf g++ make

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files and install dependencies
COPY composer*.json ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application files
COPY . .

# Copy built assets from node builder
COPY --from=node-builder /app/public/build ./public/build

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
	&& chmod -R 775 /var/www/html/storage \
	&& chmod -R 775 /var/www/html/bootstrap/cache \
	&& find /var/www/html/storage -type f -exec chmod 664 {} \; \
	&& find /var/www/html/storage -type d -exec chmod 775 {} \;

# Finalize composer install
RUN composer dump-autoload --optimize --no-dev

# Configure PHP for production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
	&& { \
	echo 'opcache.enable=1'; \
	echo 'opcache.memory_consumption=256'; \
	echo 'opcache.interned_strings_buffer=16'; \
	echo 'opcache.max_accelerated_files=20000'; \
	echo 'opcache.validate_timestamps=0'; \
	echo 'upload_max_filesize=20M'; \
	echo 'post_max_size=20M'; \
	echo 'memory_limit=512M'; \
	} > $PHP_INI_DIR/conf.d/custom.ini

# Copy configuration files
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

# Create required directories
RUN mkdir -p /var/log/nginx /run/nginx

# Expose port
EXPOSE 80

# Start supervisor to manage nginx and php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
