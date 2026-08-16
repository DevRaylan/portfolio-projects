##########################################################################
#
# Dockerfile para criacao de uma imagem 
#
##########################################################################

# Imagem inicial - DEBIAN
FROM debian:bullseye

ARG ENV_FOLDER

# Autor
LABEL author="SEAD - SETIC"

# Muda o mirror
RUN sed -i 's/deb.debian.org/debian.c3sl.ufpr.br/g' /etc/apt/sources.list

# Atualiza e instala o apache2 e o php
RUN apt-get update && apt-get install -y \
        locales \
        libfreetype6-dev \
        apache2 \
        php \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        php-gd \
        php-mysql \
        libapache2-mod-php \
        php-sqlite3 \
        php-curl \
        php-sybase \
        php-zip \
        php-mbstring \
        php-xml \
        libnet-ldap-perl \
        libunicode-string-perl \
        php-ldap \
        php-soap \
        php-pgsql \
        php-dev \
        php-pear \
        curl \
        gnupg \
        gcc \
        g++ \
        libc-dev \
        pkg-config \
        make \
        autoconf
        
# Instala libs do SQLServer
RUN curl -s https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
RUN bash -c "curl -s https://packages.microsoft.com/config/ubuntu/22.04/prod.list > /etc/apt/sources.list.d/mssql-release.list"
RUN apt-get update
RUN ACCEPT_EULA=Y apt-get install -y msodbcsql17 mssql-tools
RUN apt-get install -y unixodbc-dev
RUN pecl install sqlsrv-5.10.1
RUN pecl install pdo_sqlsrv-5.10.1
RUN echo "extension=sqlsrv.so" | tee /etc/php/7.4/mods-available/sqlsrv.ini
RUN echo "extension=pdo_sqlsrv.so" | tee /etc/php/7.4/mods-available/pdo_sqlsrv.ini
RUN phpenmod sqlsrv pdo_sqlsrv
RUN rm -f /etc/apache2/conf-enabled/other-vhosts-access-log.conf
RUN chmod -R 777 /var/run/apache2/

# Copia a configuracao do VirtualHost para a imagem
ADD build/${ENV_FOLDER}/ports.conf /etc/apache2/
ADD build/${ENV_FOLDER}/apache2.conf /etc/apache2/
ADD build/${ENV_FOLDER}/000-default.conf /etc/apache2/sites-available/
ADD build/${ENV_FOLDER}/php.ini /etc/php/apache2/
ADD build/${ENV_FOLDER}/php.ini /etc/php/cli/
ADD build/${ENV_FOLDER}/ldap.conf /etc/ldap/
ADD build/${ENV_FOLDER}/locale.gen /etc/
ADD build/${ENV_FOLDER}/apache2-foreground /usr/local/bin/

# Copia aplicacao
ADD config /var/www/config/
ADD libs /var/www/libs/
ADD modules /var/www/modules/
ADD public /var/www/public/

# Habilita o modo de rewrite
RUN a2enmod rewrite

# Ajusta timezone
RUN rm -f /etc/localtime
RUN ln -s /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime
RUN locale-gen 

###### FIM DA INSTALACAO - Parametros de Inicializacao

# Definicao de log do apache
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_RUN_USER    www-data
ENV APACHE_RUN_GROUP   www-data
ENV APACHE_PID_FILE    /var/run/apache2.pid
ENV APACHE_RUN_DIR     /var/run/apache2
ENV APACHE_LOCK_DIR    /var/lock/apache2
ENV LC_CTYPE          pt_BR.UTF-8
ENV LC_TIME           pt_BR.UTF-8
ENV LC_NAME           pt_BR.UTF-8
ENV LC_PAPER          pt_BR.UTF-8
ENV LC_ADDRESS        pt_BR.UTF-8
ENV LC_MONETARY       pt_BR.UTF-8
ENV LC_NUMERIC        pt_BR.UTF-8
ENV LC_TELEPHONE      pt_BR.UTF-8
ENV LC_IDENTIFICATION pt_BR.UTF-8
ENV LC_MEASUREMENT    pt_BR.UTF-8

# Fazer o container -apresentar- a porta 8080
EXPOSE 8080 

ENTRYPOINT ["apache2-foreground"]