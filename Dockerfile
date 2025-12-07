# Zuck# Programming Language Docker Image
# "Move fast and break things" - but in a container, so nothing actually breaks

FROM php:8.3-cli-alpine AS base

# Install Composer
RUN apk add --no-cache git curl && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    apk del curl

WORKDIR /app

# Copy composer files first for layer caching
COPY composer.json composer.lock* ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy application code
COPY . .

# Make binary executable
RUN chmod +x /app/bin/zuck

# Development/test stage
FROM base AS dev

# Install dev dependencies for testing
RUN composer install --optimize-autoloader

# Production stage
FROM php:8.3-cli-alpine AS production

WORKDIR /app

# Copy only what we need from base
COPY --from=base /app/bin /app/bin
COPY --from=base /app/src /app/src
COPY --from=base /app/vendor /app/vendor
COPY --from=base /app/examples /app/examples

# Make binary executable
RUN chmod +x /app/bin/zuck

# Add zuck to PATH
ENV PATH="/app/bin:${PATH}"

ENTRYPOINT ["/app/bin/zuck"]
CMD ["--help"]
