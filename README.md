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
sudo docker exec -w /app -it docker_php-fpm_1 php bin/console doctrine:migrations:migrate --em=default --configuration config/packages/doctrine_migrations/default.yaml
sudo docker exec -w /app -it docker_php-fpm_1 php bin/console doctrine:migrations:migrate --em=stat --configuration config/packages/doctrine_migrations/stat.yaml
sudo docker exec -w /app -it docker_php-fpm_1 php bin/console doctrine:migrations:migrate --em=service --configuration config/packages/doctrine_migrations/service.yaml
```

Run tests:

```bash
sudo docker exec -w /app -it docker_php-fpm_1 php bin/phpunit tests/Controller/TodosController.php
```
