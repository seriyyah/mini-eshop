# Defend API

This is the Defend API project – a Symfony 7 application with JWT authentication and documented API endpoints using NelmioApiDoc/Swagger.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Running the Application](#running-the-application)
- [Accessing the API and Swagger Documentation](#accessing-the-api-and-swagger-documentation)
- [Running Tests](#running-tests)
- [Project Structure](#project-structure)

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- Git (to clone the repository)

## Installation

1. **Clone the Repository**

   ```bash
   git clone https://github.com/your-username/defend-api.git
   cd defend-api
2. **Install Composer Dependencies**

   ```bash
   composer install

## Running the Application

1. **Build and Start the Containers**

   ```bash
   docker-compose up --build -d

2. **Check Container Status**

   ```bash
   docker-compose ps

3. **Accessing the Application**

   ```bash
   docker exec -it defend-api-app-1 bash  

## Accessing the API and Swagger Documentation

1. **Swagger UI**
   1. Generate a JWT token 
       ```bash
      php bin/console lexik:jwt:generate-keypair
   2. Open your browser and go to: http://localhost:9000/api/doc
   3. Register a user 
       ```bash
      {
       "email": "user@example.com",
       "password": "your_password"
       }
   4. Login with your credentials
   5. Copy the JWT token from the response

## Running Tests

1. **Check Container Status**

   ```bash
   bin/console doctrine:fixtures:load --env=test
   ./vendor/bin/phpunit

## Project Structure
    -app is simplified and uses standart symfony structure.
 ```bash
   defend-api/
├── config/                  # Symfony configuration files
├── docker/                  # Docker related files including Dockerfile and nginx config
├── public/                  # Publicly accessible directory (document root)
├── src/                     # PHP source code (Controllers, DTOs, Entities, etc.)
├── tests/                   # Automated tests (functional and unit)
├── translations/            # Translation files (if any)
├── var/                     # Cache, logs, and temporary files
├── vendor/                  # Composer dependencies
├── .env                     # Environment variables configuration
├── .gitignore               # Git ignore rules (managed via rebase and conflicts resolution)
├── docker-compose.yaml      # Docker Compose configuration file
└── README.md                # This file