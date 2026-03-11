FROM php:8.2-apache

# Cài đặt extension mysqli (bắt buộc cho mysqli_connect / Prepared Statements)
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Bật mod_rewrite của Apache (hữu ích cho URL đẹp sau này)
RUN a2enmod rewrite

# Cấu hình Apache cho phép .htaccess override
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80
