# gosarilestari
CodeIgniter3 Based Trash Bank Progressive Web Apps

# GO SARI Lestari - Local Development Setup

## Requirements

-   Podman
-   Podman Compose
-   VS Code (optional)
-   TablePlus (optional)

## Clone repository

``` bash
git clone git@github.com:susantohenri/gosarilestari.git
cd gosarilestari
```

## Start Podman machine (macOS only)

``` bash
podman machine start
```

## Build and start containers

``` bash
podman-compose up --build -d
```

## Check running containers

``` bash
podman ps
```

Expected containers:

-   gosarilestari-app
-   gosarilestari-db

## Access application

Open:

``` txt
http://localhost:8080
```

## Database connection

Use these credentials if connecting from TablePlus or another database
client:

``` txt
Host: 127.0.0.1
Port: 3306
User: root
Password: root
Database: gosarilestaridb
```

## Stop containers

``` bash
podman-compose down
```

## Stop and remove containers + database volume

``` bash
podman-compose down -v
```

# Production Deployment
## Setup env
add following line into public_html/.htaccess (or create if not exists)
```
SetEnv CI_ENV production
```
## Setup Database Connection
copy application/config/development/database.php  
to application/config/production/database.php  
change the database credentials

## Setup CRON for notification broadcast (UTC+0)
```
0 0 * * * /usr/bin/php /home/u940399048/domains/gosarilestari.com/public_html/index.php Cli BroadcastNotifikasi
```