FROM php:8.2-apache

# Install SQLite dependencies and PHP extension
RUN apt-get update && apt-get install -y libsqlite3-dev sqlite3
RUN docker-php-ext-install pdo pdo_sqlite
RUN a2enmod rewrite

# Copy all project files directly into Apache web root
COPY . /var/www/html/

# Allow .htaccess overrides and set write permissions for SQLite
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Dynamically bind Apache to the deployment platform's $PORT variable
CMD sed -i "s/80/${PORT:-80}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf && apache2-foreground