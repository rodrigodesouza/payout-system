# Imagem PHP
FROM php:8.0-fpm

# Id do usuário
ARG USER_ID=1000

# Instala dependências
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    gettext-base \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala extensões
RUN docker-php-ext-install pdo_mysql zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd
RUN docker-php-ext-install bcmath

# Instala composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala Supervisor.
RUN apt-get --allow-releaseinfo-change update && \
    apt-get install -y supervisor

# Copia arquivos de configuração do Supervisor
COPY ./docker/app/supervisord.conf /etc/supervisor
COPY ./docker/app/laravel-worker.conf /etc/supervisor/conf.d

# Instala node
# RUN curl -sL https://deb.nodesource.com/setup_12.x | bash - &&\
#     apt-get install -y nodejs

# Copiando scripts e config necessários para dentro da imagem.
COPY ./docker/app/docker-entrypoint.sh /docker/docker-entrypoint.sh
COPY ./docker/app/php.ini /usr/local/etc/php/conf.d/custom.ini

# Altera permissão de execução para o script entrypoint
RUN chmod +x /docker/docker-entrypoint.sh

# Cria grupo, usuário e o atribui ao grupo
RUN useradd -u ${USER_ID} -g www-data --shell /bin/bash --create-home wepayout

# Seta diretório de trabalho
WORKDIR /var/www/html

COPY --chown=wepayout:www-data ./src /var/www/html

# Altera o usuário para "wepayout"
USER wepayout

RUN mkdir /home/wepayout/supervisor

# Expõe porta 9000
VOLUME /var/www/html
EXPOSE 9000

RUN supervisord -c /etc/supervisor/supervisord.conf

# Script de inicialização do container
ENTRYPOINT ["/docker/docker-entrypoint.sh"]