#!/bin/bash

if [ -z "$(which php)" ]; then
  echo 'error: php required but not found, exiting'
  exit 1
fi

if [ -z "$(which git)" ]; then
  echo 'error: git required but not found, exiting'
  exit 2
fi

dir="$HOME/.ssh/sync"
mkdir -p "$dir"
cd "$dir"

git clone https://github.com/leongrdic/ssh-keys-sync.git
mv ssh-keys-sync/* .
rm -rf ssh-keys-sync

if [ -f "$dir/config.json" ]; then
  echo -n "config file config.json already exists, do you want to run the setup again (y/n)? "
  read prompt
  if [ "$prompt" != "Y" ] && [ "$prompt" != "y" ]; then
    echo 'exitting'
    exit;
  fi
fi

php setup.php

echo 'setup done, running the first update to confirm the config is valid'

php update.php
if [ "$?" == 0 ]; then
  echo 'success!'
else
  echo 'updating failed, please check the config; exiting'
  exit 3
fi

echo -n 'how often (in minutes) do you want to sync the keys (1-60): '
read minutes

echo 'installing new cron job'

cronjob="*/$minutes * * * * $(which php) $dir/update.php -c $dir/config.json"

crontab -l > tmp-crontab
echo "$cronjob" >> tmp-crontab
crontab tmp-crontab
rm tmp-crontab

echo "done"
