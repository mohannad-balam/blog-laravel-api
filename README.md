Get Started
clone or fork the repo
copy .env.exmaple file copy .env.example .env
generate app key php artisan key:generate
add database credentials
install dependencies composer install
link storage php artisan storage:link
run migration php artisan migrate
Running on a specific ip php artisan serv --host <YOUR_IP> --port <YOUR_PORT>
