# Dream Home Real Estate Application

This project is part of the Advanced Database Concepts class and demonstrates a real estate application using Oracle Database running in a Docker container (PL/SQL) alongside a PHP application. The project leverages Docker Compose to orchestrate both the Oracle Database container and the PHP application container.  
[Demo](https://dream-home-real-estate-comp214-group-3.zaghdad.com/)

## Repository Structure

```plaintext
├── client/
│   └── [PHP Application Files]
├── Dockerfile
├── docker-compose.yml
└── init-app-user.sql
```

- **client/**: Contains the PHP application files served by Apache.
- **Dockerfile**: Defines the PHP application container, including installation of the Oracle Instant Client, PHP OCI8 extension, and Apache configuration.
- **docker-compose.yml**: Orchestrates the multi-container application by defining the Oracle Database and PHP application services.
- **init-app-user.sql**: A SQL script that initializes the Oracle database with the required user.

## Setup Instructions

Follow these steps to build and run the project:

### 1. Clone the Repository

Clone this repository to your local machine using:

```bash
git clone https://github.com/Moath-Zaghdad/dream-home-real-estate-COMP214-group-3.git
cd dream-home-real-estate-COMP214-group-3
```

### 2. Prepare the Environment

Ensure you have Docker and Docker Compose installed. Verify installation by running:

```bash
docker --version
docker-compose --version
```

### 3. Build and Run the Containers

The project uses Docker Compose to manage the Oracle Database and PHP application containers.

- **Build and start the containers**:

  ```bash
  docker-compose up --build
  ```

- This command builds the PHP application container (using the provided Dockerfile) and pulls the Oracle Database image from Oracle's container registry. The Oracle container is set up with persistent storage (mapping the `./oradata` directory) and uses `init-app-user.sql` for initial configuration.

### 4. Accessing the Application

- **PHP Application**:  
  Once the containers are running, access the PHP application by navigating to:
  
  ```
  http://localhost:8080
  ```

- **Oracle Database**:  
  The Oracle Database service is exposed on port `1521` and can be connected to using your preferred Oracle client with these parameters:
  - **Hostname**: `localhost`
  - **Port**: `1521`
  - **SID**: `ORCLCDB` (as set in the `docker-compose.yml`)
  - **User & Password**: Configured via the SQL initialization script (`init-app-user.sql`)

## Oracle Docker Configuration Reference

Our Oracle Database setup is based on the approach outlined by Raina (2023), which explains how to pull and run the Oracle Database image from Oracle's container registry using Docker. While the original guide uses docker run commands, we extended it using Docker Compose to orchestrate both the Oracle and PHP containers, adding environment variables, and service dependencies for a complete development environment.


### Key Configuration Points:

- **Oracle Container Environment**:  
  Environment variables such as `ORACLE_PWD` and `ORACLE_SID` are set in `docker-compose.yml` to initialize the container with the proper configurations.

- **Volume Bindings**:  
  The Oracle container uses volume bindings to persist database files (`./oradata`) and to load the initialization SQL script (`init-app-user.sql`).

- **Networking and Port Mappings**:  
  Port `1521` is mapped for Oracle, while the PHP application container maps port `80` to host port `8080`.

- **PHP Container Setup**:  
  The PHP container Dockerfile downloads and installs the Oracle Instant Client libraries, unzips the downloaded packages, and installs the OCI8 extension via PECL. It also sets the necessary environment variables for library paths so that the PHP application can communicate with the Oracle Database.

## Troubleshooting

- **Container Startup Issues**:  
  Ensure there is no port conflict (1521 or 8080) on your host machine.  
- **Database Initialization Errors**:  
  Check the logs of the Oracle container for any issues during startup using:
  ```bash
  docker logs orclcdb
  ```

### References:
Raina, A. S. (2023, January 26). *How to run Oracle Database in a Docker container using Docker Compose*. DEV Community. https://dev.to/docker/how-to-run-oracle-database-in-a-docker-container-using-docker-compose-1c9b
