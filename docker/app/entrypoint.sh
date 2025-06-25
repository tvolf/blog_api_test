#!/bin/bash
set -e

echo "Entrypoint start" >&2

USER_ID=${HOST_UID:-1000}
GROUP_ID=${HOST_GID:-1000}
USERNAME=laravel

if ! id -u "$USERNAME" &>/dev/null; then
    addgroup --gid "$GROUP_ID" $USERNAME
    adduser --disabled-password --gecos "" --uid "$USER_ID" --gid "$GROUP_ID" $USERNAME
fi

mkdir -p /var/log/php-fpm
touch /var/log/php-fpm/access.log


[ -d /var/log/php-fpm ] &&chown -R $USER_ID:$GROUP_ID /var/log/php-fpm
[ -d /var/www/storage ] && chown -R $USER_ID:$GROUP_ID /var/www/storage
[ -d /var/www/bootstrap/cache ] && chown -R $USER_ID:$GROUP_ID /var/www/bootstrap/cache

if [ -f /var/www/docker/app/.bash_aliases ]; then
    cp /var/www/docker/app/.bash_aliases /home/laravel/.bash_aliases
    chown $USER_ID:$GROUP_ID /home/laravel/.bash_aliases
fi

echo "Entrypoint done, exec: $@" >&2

exec "$@"

