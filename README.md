# omnik-inverter-to-pvoutput
php script to send data from an Omnik inverter to Pvoutput

## Installation
- clone repository
- composer install
- copy .env.example to .env and modify the variables in it
- create crontab to run ./report_status.php from time to time (for example every 15 minutes)
