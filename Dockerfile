# Use an official PHP image as the base
FROM php:8.1-apache

# Install necessary extensions (e.g., mysqli for database connectivity)
RUN docker-php-ext-install mysqli

# Copy your project files to the container
COPY . /var/www/html

# Set the working directory
WORKDIR /var/www/html

# Give write permissions for uploads and logs
RUN chmod -R 777 /var/www/html/uploads
