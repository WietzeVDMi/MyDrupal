# MyDrupal

This repository provides instructions for setting up a Drupal 11 environment with the Gin backend theme and Gin Login.

## Requirements

- Docker and Docker Compose
- Composer
- Drush (optional for enabling modules from the command line)

## Quick setup

1. Create a new Drupal 11 project:

   ```bash
   composer create-project drupal/recommended-project mysite
   cd mysite
   ```

2. Add the Gin theme and Gin Login module:

   ```bash
   composer require drupal/gin drupal/gin_login
   ```

3. Start the site with Docker Compose by using the provided `docker-compose.yml`:

   ```bash
   docker compose up -d
   ```

4. After the site installs, enable Gin and Gin Login:

   ```bash
   vendor/bin/drush en gin gin_login -y
   vendor/bin/drush config-set system.theme admin gin -y
   ```

Your Drupal 11 site will be available on <http://localhost:8080> with the Gin admin theme and login page.


