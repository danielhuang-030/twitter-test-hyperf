# hyperf test

### Introduction
hyperf test project

### Packages
- [ha-ni-cc/hyperf-watch](https://github.com/ha-ni-cc/hyperf-watch) - hyperf watch
- [qbhy/hyperf-auth](https://github.com/qbhy/hyperf-auth) - auth of hyperf
- [hyperf-ext/hashing](https://github.com/hyperf-ext/hashing) - hash of hyperf
- [hyperf/validation](https://github.com/hyperf/validation) - validation of hyperf

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
php bin/hyperf.php migrate

# start hyperf with hot reload
php ./watch.php
```
