# hyperf test

### Introduction
hyperf test project

### Packages
- [ha-ni-cc/hyperf-watch](https://github.com/ha-ni-cc/hyperf-watch) - hyperf watch
- [qbhy/hyperf-auth](https://github.com/qbhy/hyperf-auth) - auth of hyperf

### Installation

```shell
# git clone
git clone https://github.com/danielhuang-030/hyperf-test.git

# composer install
composer install

# copy .env and setting db/redis
cp .env.example .env
vi .env

# db migrate
php artisan migrate

# start hyperf with hot reload
php ./watch.php
```
