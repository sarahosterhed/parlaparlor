FROM wordpress:latest

RUN a2dismod mpm_event || true && a2enmod mpm_prefork
