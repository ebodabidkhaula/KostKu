#!/bin/bash
set -e

# Render inject PORT env var secara dinamis. Default ke 80 kalau dijalankan lokal.
PORT="${PORT:-80}"

# Update Apache supaya listen di port yang diberikan Render
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
