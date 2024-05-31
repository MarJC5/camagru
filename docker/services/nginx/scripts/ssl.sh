#!/bin/bash

# Request the host name
echo "Please enter the host name:"
read host

# Define the directory where the SSL certificates will be stored
ssl_dir="/etc/nginx/ssl"

# Check if the SSL directory exists, if not, create it
if [ ! -d "$ssl_dir" ]; then
    echo "Creating SSL directory at $ssl_dir"
    mkdir -p "$ssl_dir"
fi

# Install the OpenSSL package if it is not already installed
if ! command -v openssl &> /dev/null; then
    echo "Installing OpenSSL"
    apk update
    apk add openssl
fi

# Generate the SSL certificate and key with the provided host name
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout "$ssl_dir/nginx.key" \
    -out "$ssl_dir/nginx.crt" \
    -subj "/C=CH/ST=Vaud/L=Lausanne/O=Dis/CN=$host"

echo "SSL certificate generated for $host at $ssl_dir/nginx.crt"