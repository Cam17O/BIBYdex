# Use an image with PHP 7.4 and Apache
FROM php:7.4-apache

# Install the PDO MySQL extension
RUN docker-php-ext-install pdo_mysql

# Set the working directory
WORKDIR /var/www/html

# Copy the application files into the container
COPY . .

# Expose the port the Apache server is listening on
EXPOSE 80

# Command to run the Apache server
CMD ["apache2-foreground"]