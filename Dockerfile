FROM php:8.2-apache

# Copia todo el código al contenedor
COPY . /var/www/html

# Establece el DocumentRoot de Apache a la carpeta public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Habilita mod_rewrite y configura Apache
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    a2enmod rewrite && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Mueve el .htaccess a la carpeta public/ (si no está ya allí)
RUN mv /var/www/html/.htaccess /var/www/html/public/.htaccess