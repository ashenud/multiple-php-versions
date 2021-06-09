# RUN TWO DIFFERENT PHP VIRSIONS AT SAME TIME

For my personal projects, I needed to have two different PHP versions running at the same time of my server. And those project needed mysqli and msql extensions separately.

## Getting Started

Here's how to run PHP version 5.6 and PHP version 7.4 in two ports simultaneously on Ubuntu server.

### Prerequisites


In order to run this container you'll need docker installed.

* [Linux](https://docs.docker.com/linux/started/)
* [Windows](https://docs.docker.com/windows/started) with [WSL](https://docs.microsoft.com/en-us/windows/wsl)

### Usage

#### Preparation of prerequisites

If you are running  **apache** on your local server, remove it first. then install **nginx**

```shell
:~# apt update && apt upgrade -y
:~# apt-get install -y software-properties-common
:~# apt install -y nginx
```
Create mount folders structure  for each PHP versions root directory

```shell
:~# mkdir -p /var/www/7.4/html/
:~# mkdir -p /var/www/5.6/html/
:~# echo "<?php phpinfo(); ?>" > /var/www/7.4/html/index.php
:~# echo "<?php phpinfo(); ?>" > /var/www/5.6/html/index.php
:~# chown -R [useranme] /var/www/
```

#### Docker configurations

Create docker network first. by setting up a docker network, you can allow multiple docker containers to communicate with each other (eg - mysql database container connect)

```shell
:~# docker network create database-link
```

Create PHP version 5.6 container using ubuntu-18.04 and run in port 8080 with apache server

```shell
:~# docker run --restart=always --name php-5.6 --network=database-link -p 8080:80 -v /var/www/5.6:/var/www -d ashenud/multiple-php-versions:1st-bulid-5.6
```

Create PHP version 7.4 container using ubuntu-18.04 and run in port 9090 with apache server

```shell
:~# docker run --restart=always --name php-7.4 --network=database-link -p 9090:80 -v /var/www/7.4:/var/www -d ashenud/multiple-php-versions:1st-bulid-7.4
```

### MySql Configuration

#### In both linux and windows ([WSL](https://docs.microsoft.com/en-us/windows/wsl))

* You can install another [mysql-server](https://hub.docker.com/r/mysql/mysql-server) container and connect using its **_container-name_** as host. that's why docker network was made 

```shel
:~# docker run --restart=always --name=mysql-8.0 --network=database-link -e MYSQL_ROOT_PASSWORD=my-secret-pw -d mysql/mysql-server:8.0 mysqld --default-authentication-plugin=mysql_native_password --character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci
:~# docker exec -it mysql-8.0 mysql -uroot -pmy-secret-pw
mysql> CREATE USER 'username'@'%' IDENTIFIED BY 'password';
mysql> GRANT ALL PRIVILEGES ON *.* TO 'username'@'%';
mysql> flush privileges;
mysql> exit;
```
* Create sample php 7.4 connection

```shel
<?php
    $servername = "mysql-8.0"; // container-name of mysql-server container
    $username = "username";
    $password = "password";
    // Create connection
    $conn = mysqli_connect($servername, $username, $password);
    // Check connection
    if (!$conn) {
          die("Connection failed: " . mysqli_connect_error());
    }
    echo "Connected successfully";
?>
```
> paste and save above code in below connection.php file
```shel
:~#  nano /var/www/7.4/html/connection.php
```
#### In linux also you can try this

* You can install [mysql-server](https://dev.mysql.com/doc/mysql-getting-started/en/) on your local server and use your server **_ip_** as each PHP version mysql connection host.

>**_ip_** - This should not be your [localhost](http://127.0.0.1) ip address. This should be the ip address that your network gave you

```shell
:~# ip addr
```
[comment]: # (* Also you can install mysql-server inside window and use your server ip as each PHP version mysql connection host.)

### Useful File Locations

* `/var/www/5.6/` - PHP 5.6 version document root
  
* `/var/www/7.4/` - PHP 7.4 version document root

## Testing

* You can also use your server **_ip_** instead of [localhost](http://127.0.0.1) ip address

>`http://127.0.0.1:8080/`

>`http://127.0.0.1:9090/`

>`http://127.0.0.1:9090/connection.php`

## Author

* **[Ashen Udithamal](https://www.linkedin.com/in/ashenud/)** 