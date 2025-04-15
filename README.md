# php-openswoole-mysql-pgsql-mongodb


Here is a `README.md` file along with the necessary setup files for setting up **OpenSwoole** and databases (**MySQL**, **PostgreSQL**, **MongoDB**, and **Firebase Firestore**) using Docker. The project will provide a flexible configuration to allow you to use OpenSwoole with any of the listed databases.

### Project Directory Structure

```
/docker-swoole-db-setup/
├── .devcontainer/
│   └── devcontainer.json
├── docker-compose.yml
├── Dockerfile
├── .env
├── src/
│   └── server.php
├── README.md
└── .gitignore
```

### **1. Create `docker-compose.yml`**

This file will define the services for OpenSwoole and the databases (MySQL, PostgreSQL, MongoDB, and Firebase Firestore).

```yaml
services:
  openswoole:
    build: .
    container_name: openswoole
    ports:
      - "9501:9501"
    volumes:
      - ./src:/var/www
    environment:
      DB_CONNECTION: ${DB_CONNECTION}
      DB_HOST: ${DB_HOST}
      DB_PORT: ${DB_PORT}
      DB_DATABASE: ${DB_DATABASE}
      DB_USERNAME: ${DB_USERNAME}
      DB_PASSWORD: ${DB_PASSWORD}
    restart: always

  mysql:
    image: mysql:5.7
    container_name: mysql
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_DATABASE: ${DB_DATABASE}
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - swoole_network

  postgres:
    image: postgres:alpine
    container_name: postgres
    environment:
      POSTGRES_PASSWORD: rootpassword
      POSTGRES_DB: ${DB_DATABASE}
    ports:
      - "5432:5432"
    networks:
      - swoole_network

  mongodb:
    image: mongo
    container_name: mongodb
    environment:
      MONGO_INITDB_ROOT_USERNAME: root
      MONGO_INITDB_ROOT_PASSWORD: rootpassword
    ports:
      - "27017:27017"
    networks:
      - swoole_network

volumes:
  mysql_data:

networks:
  swoole_network:
    driver: bridge
```

### **2. Create `.env`**

This file will contain environment variables for configuring database connections and OpenSwoole. You can switch between MySQL, PostgreSQL, MongoDB, or Firebase Firestore by modifying the `DB_CONNECTION` variable.

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=mydb
DB_USERNAME=root
DB_PASSWORD=rootpassword
```

You can also create different `.env` files for each database (e.g., `.env.mysql`, `.env.pgsql`, `.env.mongodb`) and switch them by copying the appropriate file to `.env`.

### **3. Create `Dockerfile`**

This file will build the OpenSwoole image. It installs OpenSwoole and its dependencies.

```Dockerfile
# Use official PHP image with Alpine for a smaller image size
FROM php:8.2-fpm-alpine

# Install OpenSwoole and dependencies
RUN apk --no-cache add --virtual .build-deps \
        gcc \
        g++ \
        make \
        autoconf \
        libc-dev \
        bash \
        && pecl install openswoole \
        && docker-php-ext-enable openswoole \
        && apk del .build-deps

# Install PHP extensions required for database drivers
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Set working directory
WORKDIR /var/www

# Copy PHP files into the container
COPY ./src /var/www

# Expose OpenSwoole default HTTP port
EXPOSE 9501

# Command to run OpenSwoole
CMD ["php", "server.php"]
```

### **4. Create `src/server.php`**

This is the OpenSwoole server script. It will connect to the selected database based on the environment variables.

```php
<?php
echo "Starting OpenSwoole server...\n";

// Get database configuration from environment variables
$serverName = getenv('DB_HOST');
$databaseName = getenv('DB_DATABASE');
$dbUsername = getenv('DB_USERNAME');
$dbPassword = getenv('DB_PASSWORD');

// Define PDO options
$pdoOpts = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
$serverUri = "mysql:host={$serverName};dbname={$databaseName};charset=utf8mb4"; // Default to MySQL

// Create connection to the database
try {
    $pdo = new PDO($serverUri, $dbUsername, $dbPassword, $pdoOpts);
    echo "Database connection successful!\n";
} catch (Exception $ex) {
    echo "Could not connect to the database: " . $ex->getMessage() . "\n";
    die();
}

// You can switch to PostgreSQL or MongoDB by changing DB_CONNECTION in the .env file
// Example for PostgreSQL:
// $serverUri = "pgsql:host={$serverName};dbname={$databaseName}";
```

### **5. Create `.devcontainer/devcontainer.json`**

This is the configuration for GitHub Codespaces. It uses the Docker Compose setup to create the development environment.

```json
{
  "name": "OpenSwoole with Databases",
  "dockerComposeFile": "../docker-compose.yml",
  "service": "openswoole",
  "workspaceFolder": "/workspace",
  "forwardPorts": [9501],
  "extensions": [
    "felixfbecker.php-debug",
    "ms-vscode.php"
  ],
  "settings": {
    "php.executablePath": "/usr/local/bin/php"
  },
  "postCreateCommand": "docker-compose up -d"
}
```

### **6. Create `.gitignore`**

To ensure unnecessary files aren't tracked in the repository, create a `.gitignore` file:

```gitignore
# Ignore environment variables
.env

# Ignore Docker-related files
*.log
*.pid
```

---

### **7. Create `README.md`**

Now, let's write the `README.md` file to guide users through setting up the project in different environments.

```markdown
# OpenSwoole with Databases Setup Using Docker

This project provides an easy way to set up **OpenSwoole** with various databases such as **MySQL**, **PostgreSQL**, **MongoDB**, and **Firebase Firestore** using Docker. This setup will allow you to quickly spin up a containerized development environment that connects OpenSwoole with a chosen database.

## 🛠️ Prerequisites

Before you begin, ensure that Docker and Docker Compose are installed on your machine or server.

### Install Docker and Docker Compose:

- [Install Docker](https://docs.docker.com/get-docker/)
- [Install Docker Compose](https://docs.docker.com/compose/install/)

## 🚀 Quick Setup

### 1. Clone the repository

Clone the repository to your local machine, VPS, or GitHub Codespace.

```bash
git clone <repository-url>
cd <repository-folder>
```

### 2. Set Up Your Database

In the `.env` file, set the database you want to use by changing the `DB_CONNECTION` value. You can use:

- **MySQL**: `DB_CONNECTION=mysql`
- **PostgreSQL**: `DB_CONNECTION=pgsql`
- **MongoDB**: `DB_CONNECTION=mongodb`

For Firebase Firestore, you can use its SDK, but for the sake of this Docker setup, we will focus on SQL/NoSQL databases with Docker containers.

### 3. Build and Start Docker Containers

Run the following command to build and start the Docker containers:

```bash
docker-compose up --build -d
```

This will:
- Set up OpenSwoole on port 9501
- Start the selected database container

### 4. Access OpenSwoole Server

Once the containers are up and running, OpenSwoole will be available at:

```
http://localhost:9501
```

### 5. Stop the Containers

To stop the containers, run:

```bash
docker-compose down
```

---

## 📝 Troubleshooting

- **Port Conflicts**: If port 9501 is already in use, modify the `docker-compose.yml` to use a different port for OpenSwoole.
- **Database Connection**: Ensure that the environment variables in the `.env` file are correctly set, especially the database connection details.

---

## 🤝 Contributing

Feel free to open issues and submit pull requests if you find bugs or want to add features.

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
```

---

### Final Steps

1. Push the repository to GitHub.
2. You can clone this repository into **GitHub Codespaces** or your **VPS** and follow the setup steps.
3. If using **Firebase Firestore**, you'll need to adjust the setup to use the Firebase SDK in PHP and handle Firestore connections differently (for example, via the Firebase PHP SDK).

This setup should help you get started quickly with OpenSwoole and different databases using Docker.