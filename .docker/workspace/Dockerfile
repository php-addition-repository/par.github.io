###########################################################################
# Workspace image
###########################################################################
FROM php:8.2-cli-buster as workspace
COPY "./.docker/shared" "/opt/build"

#--------------------------------------------------------------------------
# Setup
#--------------------------------------------------------------------------

RUN apt-get update && apt-get install -y \
    bash \
    git \
    gpg \
    wget \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

#--------------------------------------------------------------------------
# Set Timezone
#--------------------------------------------------------------------------

RUN /opt/build/set-timezone.sh "UTC"

#--------------------------------------------------------------------------
# non-root user + access
#--------------------------------------------------------------------------

RUN addgroup --gid 1000 app \
  	&& adduser --uid 1000 -gid 1000 --disabled-password --gecos "" app \
    && /opt/build/create-user-dir.sh 1000 1000 "/app"

#--------------------------------------------------------------------------
# PHP extras
#--------------------------------------------------------------------------

# https://github.com/mlocati/docker-php-extension-installer
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions curl json mbstring intl xdebug-^3.3 opcache iconv

#--------------------------------------------------------------------------
# Composer
#--------------------------------------------------------------------------

COPY --from=composer:lts /usr/bin/composer /usr/bin/composer

#--------------------------------------------------------------------------
# Project
#--------------------------------------------------------------------------

# Execute below as non-root user
USER app

# Set workdir
WORKDIR /app

# Create composer directory
RUN mkdir /home/app/.composer

# Make sure PATH contains project bin and project composer bin
ENV PATH="/app/bin:/app/vendor/bin:${PATH}"
