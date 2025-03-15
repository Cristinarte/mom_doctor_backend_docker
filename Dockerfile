FROM php:8.2-fpm

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    psmisc \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar los archivos de configuración de Nginx
COPY ./nginx/default.conf /etc/nginx/sites-available/default

# Copiar los archivos de la aplicación Laravel
COPY . /var/www

# Establecer el directorio de trabajo
WORKDIR /var/www

# Configurar permisos y ejecutar Composer
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && composer install --no-scripts --no-interaction

# Copiar y configurar el script de entrada
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Exponer el puerto 80 para Nginx
EXPOSE 80

# Usar el script como punto de entrada
CMD ["/usr/local/bin/entrypoint.sh"]