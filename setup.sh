#!/bin/sh
set -e

composer create-project drupal/recommended-project mysite
cd mysite
composer require drupal/gin drupal/gin_login

