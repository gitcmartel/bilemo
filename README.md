Mobile Phone catalog API

Installation instructions :

1 - If the following tools are not installed on your computer then folow the links below to install them :

Git : https://git-scm.com/
Composer : https://getcomposer.org/
Scoop : https://scoop.sh/
Symfony CLI : https://symfony.com/download
2 - Start the git bash application

3 - In the directory of your choice on your web server, clone the github repository, here is the command : git clone https://github.com/gitcmartel/bilemo.git

4 - Run the following command to get the project dependencies : composer update

5 - At the root of the project duplicate the .env file and rename it to .env.local

6 - Create the mysql database 'bilemo' 

7 - Enter your database connection string in the DATABASE_URL variable of the .env.local file.

8 - Execute this command to create the table structure : php bin/console doctrine:migrations:migrate

9 - Insert the DataFixtures into the database : php bin/console doctrine:fixtures:load

10 - Create jwt folder in config folder

11 - Create the public jwt key by executing this command in the gitbash console (and enter and save a strong passphrase)

openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096

12 - Create the private jwt key by executing this command in the gitbash console (and enter and save a strong passphrase)

openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout

13 - Enter a login and password of an existing user in the NELMIO_API_LOGIN and NELMIO_API_PASSWORD variables of the .env.local file.