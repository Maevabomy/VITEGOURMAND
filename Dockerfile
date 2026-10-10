
# Image de base : PHP 8.4 avec Apache
FROM php:8.4-apache

# Installation des bibliothèques nécessaires
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libssl-dev \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP nécessaires à Vite & Gourmand
RUN docker-php-ext-install pdo_mysql mbstring

# Installation de l'extension MongoDB
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Activation de la réécriture des URL Apache
RUN a2enmod rewrite

# Apache doit pointer vers le dossier public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf

# Autoriser les règles du fichier .htaccess
RUN printf '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n' \
    > /etc/apache2/conf-available/vitegourmand.conf \
    && a2enconf vitegourmand

# Dossier de travail
WORKDIR /var/www/html

# Copier le projet dans le conteneur
COPY . .

RUN mkdir -p /var/www/html/storage/cache/geocoding \
    /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage

# Port HTTP utilisé par Apache
EXPOSE 80

# Démarrer Apache
CMD ["apache2-foreground"]
