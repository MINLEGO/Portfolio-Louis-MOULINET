# PHP pinned to a patched release (php2shell, CVE-2026-109618 / CVE-2026-109619).
# Do not build PHP from Nixpacks: its nixpkgs snapshot ships an unpatched 8.3.15.
FROM php:8.3.36-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY . /var/www/html/
