# The Real Emiu (Telegram Bot)
## Project system env
```
php >= 8.0
swoole >= 4.8.0
mysql >= 5.7
redis
```
## How to run?
```
git clone git@github.com:gm-live/emiu-bot.git
cd emiu-bot
composer install

# change bot info and webhook url in .env
cp .env.example .env

php bin/hyperf.php migrate
php bin/hyperf.php start
```
