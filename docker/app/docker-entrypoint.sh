#!/bin/bash
if [ ! "$(ls -A)" ]
then
  cd ..
  composer create-project laravel/laravel html
  cd html
  chmod -R 775 storage/
else
  composer install
  npm install
fi

if [ ! -e .env ] && [ -e .env.example ]
then
  envsubst < .env.example > .env
  php artisan key:generate
fi

php artisan migrate --force 

php-fpm