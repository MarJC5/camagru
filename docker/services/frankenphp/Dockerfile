FROM dunglas/frankenphp

# Install Postfix and PHP extensions
RUN install-php-extensions \
  pdo_mysql \
  gd \
  intl \
  zip \
  opcache

RUN apt-get update && \
    apt-get install -y postfix mailutils libnss3-tools && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install MailPit (SMTP testing tool)
RUN curl -sL https://raw.githubusercontent.com/axllent/mailpit/develop/install.sh | bash

# Set the working directory
WORKDIR /var/www/html
