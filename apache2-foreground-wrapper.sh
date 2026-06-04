#!/bin/sh
set -e
# Remove conflicting MPMs at runtime, right before Apache starts.
find /etc/apache2/mods-enabled/ -name 'mpm_event*' -delete 2>/dev/null || true
find /etc/apache2/mods-enabled/ -name 'mpm_worker*' -delete 2>/dev/null || true
exec /usr/local/bin/apache2-foreground.orig "$@"
