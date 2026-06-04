FROM wordpress:latest

RUN echo "=== MPM files before fix ===" \
    && find /etc/apache2/mods-enabled/ -name 'mpm_*' | sort \
    && echo "=== Running fix ===" \
    && find /etc/apache2/mods-enabled/ -name 'mpm_*' -delete \
    && echo "=== MPM files after delete ===" \
    && (find /etc/apache2/mods-enabled/ -name 'mpm_*' | sort || echo "none") \
    && a2enmod mpm_prefork \
    && echo "=== MPM files after a2enmod ===" \
    && find /etc/apache2/mods-enabled/ -name 'mpm_*' | sort
