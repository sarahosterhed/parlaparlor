FROM wordpress:latest

# Remove only the conflicting MPM (event/worker); mpm_prefork is already
# enabled by mod_php and must stay loaded for PHP to work.
RUN find /etc/apache2/mods-enabled/ \( -name 'mpm_event*' -o -name 'mpm_worker*' \) -delete
