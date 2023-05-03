#!/bin/bash
ENV_NAME=".env"
if [[ ! -f "$ENV_NAME" ]]; then
  cp .env.example $ENV_NAME
  sed -r -i '' -e "s|UID\=|UID\=$(id -u)|g" $ENV_NAME
  sed -r -i '' -e "s|GID\=|GID\=$(id -g)|g" $ENV_NAME
fi
