# Utiliser l'image PHP avec Apache
FROM php:8.2-apache

# Installer les extensions nécessaires (exemple Symfony + MySQL)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql zip


# Installer l'extension MongoDB
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer





# Ajouter la configuration Apache pour le VirtualHost
COPY ./apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Activer mod_rewrite (nécessaire pour Symfony)
RUN a2enmod rewrite

# Copier les fichiers de l'application
WORKDIR /var/www/html
COPY . /var/www/html

# Donner les droits à Apache
RUN chown -R www-data:www-data /var/www/html

# Donner les droits à Apache et s'assurer que les fichiers sont accessibles
RUN chmod -R 755 /var/www/html


# Exposer le port 80
EXPOSE 80

# Lancer le serveur Apache
CMD ["apache2-foreground"]
