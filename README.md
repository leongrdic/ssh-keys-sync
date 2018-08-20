
# ssh-keys-sync

This script connects to the API of your choice (e.g. Github, Gitea, Gogs, etc.), pulls your public ssh keys and puts them in your ssh `authorized_keys` file. It also detects when a key has been removed and removes it from the local file.
It's intended to be simple, fast and easy to set up. The setup usually takes less than 2 minutes!

## Installation

### Requirements

The main requirement is `php-cli`, version 5.6 or above. This means the script could work on different platforms like Windows, but it's primarily written for Linux based distros.
The installer provided in the next section also requires `git` and `crontab`, but you don't have to use it to set up the sync script

### Automated installer

The easiest way to install the sync script is to run this command while logged in as a user whose keys you want to sync (works only on linux based systems):
```
curl -o- https://raw.githubusercontent.com/leongrdic/ssh-keys-sync/master/install.sh | bash
```

The installer will then guide you through the setup of this script and will automatically configure a cronjob for your user account. 

### The 'manual' way

1. clone the repo in a directory you wish
2. run `php setup.php` in the directory of the repo and follow the config generation settings
3. run `php update.php` to do the first key synchronization
4. set up a cronjob that will periodically run the command from step 3 to sync the keys with the server

## Custom API

This sync script supports any API that is similar to the one provided by Github. The installer supports configuring the script for Github and Gitea/Gogs but you can also use it with different providers.
Take a look at the `config.sample.json` file where you can find the format of the config file that the sync script is expecting.
The script is expecting the response from the API to be a JSON formatted array containing a list of keys with `id` and `key` properties (take a look at Github's API to check the format)

## Disclaimer

I suggest setting the sync period to about 15 to 30 minutes which seems like a nice middle (it's not too often nor too rare.
The script is theoretically 100% secure considering if you're using an https API and don't have any untrusted certificates installed on your system.
I am not responsible for any problems this script could potentially cause, I've tested it and am using it personally so it's not my problem if you get locked out of your account.
