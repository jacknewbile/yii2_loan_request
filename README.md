# Yii2 Project

This is a Yii2 project configured to run inside 3 different Docker containers. Below are the instructions on how to build, run the containers, and perform necessary database migrations.

## Author

Sultan Feizov

## Prerequisites

- Docker
- Docker Compose

Ensure that you have Docker and Docker Compose installed on your system.

## API Documentation

The OpenAPI 3.0 specification for this project's API is available [here](./ProjectSpec.yaml).


## Getting Started

### 1. Build the Docker Containers

Before running the project, you need to build the containers. Navigate to the root directory of the project and execute the following command:

```bash
docker-compose build
```

This command will build all the Docker containers required for the project.

### 2. Run the Docker Containers
Once the containers are built, start them using the following command:

```bash
docker-compose up -d
```
This will start all the necessary containers: yii_php, yii_postgres, yii_nginx.

### 3. Run Database Migrations and Composer Install
After the containers are up and running, you need to run the database migrations to set up the database schema.

Steps to run migrations:

* Enter the yii_php container:
    ```bash
    docker exec -it yii_php bash
    ``` 
  This command opens a bash shell inside the container where the Yii2 application is running.

* Navigate to the project folder
    ```bash
    cd yii_project
    ```
* Run the Composer Install:
    ```bash
    composer install
  
* Run the migrations:
    ```bash
    php yii migrate
    ```
    This will execute all the pending migrations and set up the database.

### 4. Access the Application
Once the migrations are completed, you can access the application API at:

http://admin.localhost


### Note

Parallel Processing and Transaction Isolation

To implement parallel processing of request handling in the application, I have used transaction isolation to ensure that concurrent processes don't conflict with each other and Selective Query Processing. 
I have implemented a query that only processes requests (pending status) from customers who do not already have an approved request. This was done using SQL query with subquery to exclude customers who already have an approved request.

###
Time Spent on the Project: 8h