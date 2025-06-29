# Image de base avec Apache et PHP
FROM php:8.1-apache

# Copie les fichiers de l'application dans le conteneur
COPY . /var/www/html

# Active le module Apache rewrite
RUN a2enmod rewrite

# Configuration d'Apache pour activer le .htaccess
RUN echo '<Directory /var/www/html/>\n\
    AllowOverride All\n\
</Directory>' > /etc/apache2/conf-available/override.conf \
    && a2enconf override

# Installation de l'extension mysqli pour MySQL + pdo_mysql
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Définition du port exposé
EXPOSE 80

# Lancement du serveur Apache au démarrage
CMD ["apache2-foreground"]