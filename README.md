# The apple world
Запуск
```bash 
docker-compose up -d
docker-compose run --rm frontend composer install
docker-compose run --rm backend yii migrate
```
Приложение (компонент фронтенд): http://localhost:20080/