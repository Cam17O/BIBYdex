# Utiliser une image PHP avec Apache
FROM php:7.4-apache

# Installation de l'extension PDO MySQL
RUN docker-php-ext-install pdo_mysql

# Installation de bcrypt
RUN apt-get update && apt-get install -y \
    libicu-dev \
    && docker-php-ext-install intl \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install opcache \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install pdo_mysql \
    && pecl install bcrypt \
    && docker-php-ext-enable bcrypt

# Créer un répertoire de travail dans l'image
WORKDIR /var/www/html

# Copier les fichiers PHP de votre application dans l'image
COPY . .

# Exposer le port sur lequel le serveur Apache écoute
EXPOSE 80

# Commande pour démarrer le serveur Apache
CMD ["apache2-foreground"]