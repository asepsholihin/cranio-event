# Stage 1: Build the frontend
FROM public.ecr.aws/docker/library/node:lts-alpine AS frontend

WORKDIR /app

# Copy only package.json, yarn.lock and .yarnrc.yml first to leverage Docker caching
COPY package.json yarn.lock .yarnrc.yml ./

# Install Yarn v4 (using the official Yarn installer)
RUN corepack enable && corepack prepare yarn@stable --activate

# Install dependencies
RUN yarn install

# Copy the application code
COPY . .

# Build frontend distribution
RUN FREE_MEMORY=$(free -m | awk '/^Mem:/ {print $2 - 512}') && \
    export NODE_OPTIONS="--max-old-space-size=${FREE_MEMORY:-4086}" && \
	yarn production


# Stage 2: Set up the production environment
FROM 667803110472.dkr.ecr.ap-southeast-1.amazonaws.com/platform-sistem-jejak-imani-base:latest
LABEL maintainer="Surya Akhmad me@surya-alive.com"

WORKDIR /var/www/

# Copy only composer.json and composer.lock first to leverage Docker caching
COPY ./composer.json ./composer.lock ./

# Copy application files into the container
COPY --chown=nginx . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Supervisor config & start script
COPY ./deploy/supervisord.conf /etc/supervisord.conf
COPY ./deploy/start.sh /usr/bin/
RUN chmod +x /usr/bin/start.sh

# Override nginx's default config
COPY ./deploy/default.conf /etc/nginx/conf.d/default.conf
COPY ./deploy/nginx.conf /etc/nginx/nginx.conf
COPY ./deploy/php.ini /etc/php/8.1/fpm/php.ini

# Copy frontend distribution
COPY --from=frontend --chown=nginx /app/public /var/www/public

EXPOSE 80

CMD ["/usr/bin/start.sh"]