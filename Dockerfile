#Base image
FROM php:7.2-apache

#Install musqli
RUN docker-php-ext-install mysqli

# Optionally, copy your PHP application files into the container
 COPY cp /var/lib/jenkins/workspace/test_php/target/php-java-app-1.0.0.jar nginx-container:/var/www/html

# Expose port 80 to allow incoming traffic
EXPOSE 80

# Start Apache server in the foreground
CMD ["apache2-foreground"]
