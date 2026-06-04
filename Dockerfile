FROM wordpress:latest

RUN find /etc/apache2/mods-enabled/ -name 'mpm_*' -delete \
    && a2enmod mpm_prefork
