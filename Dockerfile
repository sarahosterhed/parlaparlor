FROM wordpress:latest

# COPY busts Railway's build cache. The wrapper removes conflicting MPMs
# at runtime right before Apache starts, which is more reliable than
# build-time fixes that get swallowed by layer caching.
COPY apache2-foreground-wrapper.sh /usr/local/bin/apache2-foreground-wrapper.sh
RUN chmod +x /usr/local/bin/apache2-foreground-wrapper.sh \
    && mv /usr/local/bin/apache2-foreground /usr/local/bin/apache2-foreground.orig \
    && mv /usr/local/bin/apache2-foreground-wrapper.sh /usr/local/bin/apache2-foreground
