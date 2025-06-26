## Blog API test 

Steps to run:
1. Install docker/docker-compose (if it's not installed yet)
2. Copy .env.example into .env file of project folder and set it up
3. Open a terminal and run the following commands from the project folder: 
	- docker-compose up -d
    - docker-compose exec -u laravel app bash
4. In the opened window run the following commands:
   -  composer install   
   -  php artisan key:generate
   -  php artisan migrate
   - 
After this site will be available on http://localhost:8000 
    
To run tests use the following line from `app` container:
   - php artisan test
    
or for parallel testing mode:
   - php artisan test -p 
    


	



