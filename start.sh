#bin/bash

sudo apt install composer -y
sudo apt install npm -y
sudo chmod -R 777 .
composer install
npm install
npm run dev

php artisan key:generate

php artisan config:cache

php artisan serve