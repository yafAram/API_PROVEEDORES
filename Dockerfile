# Usamos una imagen ligera basada en Alpine con PHP y Apache
FROM php:8.1-apache-alpine

# Instalar dependencias y extensiones necesarias
RUN apk add --no-cache libzip-dev unzip && \
    docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite en Apache
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/httpd.conf && \
    sed -i 's/DirectoryIndex index.html/DirectoryIndex index.php/g' /etc/apache2/httpd.conf

# Copiar el código de la aplicación al directorio de Apache
COPY . /var/www/html/

WORKDIR /var/www/html/

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html/