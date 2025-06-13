1. If .env file is missing create it in project directory
2. Create your database and put credentials in .env file under DATABASE_URL key (For example: DATABASE_URL="pgsql://iot:iot@localhost:5432/iot?serverVersion=15&charset=utf8")
3. run command: 'composer install'
4. run command: 'bin/console doctrine:schema:create'
5. Build up your server with one of these two command: 'php -S localhost:8000' or 'symfony serve'
