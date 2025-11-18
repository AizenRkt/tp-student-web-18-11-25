FROM php:8.2-apache

# Installer PDO pour MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Activer mod_rewrite pour Flight
RUN a2enmod rewrite

# Appliquer la configuration Apache personnalisée
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Droits
RUN chown -R www-data:www-data /var/www/html
