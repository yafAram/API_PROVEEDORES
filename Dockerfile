FROM php:8.1-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite para Apache
RUN a2enmod rewrite

# Copiar el código de la aplicación al directorio de Apache
COPY . /var/www/html/

WORKDIR /var/www/html/

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html/
