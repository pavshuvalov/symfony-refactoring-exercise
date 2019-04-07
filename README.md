# `symfony-refactoring-exercise`

Install composer dependencies:

```bash
composer update
```

Start docker:

```bash
cd docker
sudo docker-compose up
```

Apply migrations:

```bash
sudo docker exec -w /app -it docker_php-fpm_1 php bin/console doctrine:migrations:migrate
```

Start tests:

```bash
sudo docker exec -w /app -it docker_php-fpm_1 php bin/phpunit tests/Controller/TodosController.php
```
