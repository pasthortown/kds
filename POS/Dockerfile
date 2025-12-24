# Usar la imagen oficial de PHP 8.3 con Apache
FROM php:8.3-apache

# Set main params
ARG environment
ENV ENV=production
ENV ACCEPT_EULA=Y
ENV TZ=America/Bogota
ENV APP_URL=/var/www/html
ENV APP_NAME=pos


# Instalar las dependencias necesarias
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    libonig-dev \
    unixodbc-dev \
    curl \
    gnupg \
    tzdata \
    libicu-dev

# Configurar la zona horaria de Ubuntu
RUN ln -fs /usr/share/zoneinfo/$TZ /etc/localtime && dpkg-reconfigure -f noninteractive tzdata

# Configurar el PHP segun ambiente
#COPY ./docker/php/php.ini-${ENV} /usr/local/etc/php/php.ini

# Instalar las extensiones de PHP
RUN docker-php-ext-install curl mbstring pdo pdo_mysql intl
RUN docker-php-ext-enable intl
RUN pecl install sqlsrv pdo_sqlsrv
RUN docker-php-ext-enable sqlsrv pdo_sqlsrv 

RUN apt-get install -y --no-install-recommends \
        locales \
        apt-transport-https \
    && echo "es_ES.UTF-8 UTF-8" > /etc/locale.gen \
    && locale-gen

# Configurar la zona horaria de PHP
RUN echo "date.timezone = $TZ" > /usr/local/etc/php/php.ini

RUN echo "AddDefaultCharset UTF-8" >> /etc/apache2/conf-available/charset.conf && \
    a2enconf charset

# Instalar el driver ODBC Driver for SQL Server
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
RUN curl https://packages.microsoft.com/config/ubuntu/20.04/prod.list > /etc/apt/sources.list.d/mssql-release.list
RUN apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql17

# Cambiar el directorio de trabajo
WORKDIR $APP_URL/$APP_NAME

# Copiar el cÃ³digo fuente de la aplicaciÃ³n al directorio
COPY . .

# Otorgar permisos
RUN chmod 777 -R /var/www/html/

RUN cd ..

RUN mkdir /var/www/html/reportes

RUN chmod 777 -R /var/www/html/reportes

COPY ./reportesReact $APP_URL/reportes

# Exponer el puerto 80
EXPOSE 80