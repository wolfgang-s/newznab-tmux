<p align="center">
    <a href="https://codeclimate.com/github/NNTmux/newznab-tmux"><img src="https://codeclimate.com/github/NNTmux/newznab-tmux/badges/gpa.svg"></a>
    <a href="https://scrutinizer-ci.com/g/NNTmux/newznab-tmux/build-status/dev"><img src="https://scrutinizer-ci.com/g/NNTmux/newznab-tmux/badges/build.png?b=dev"></a>
    <a href="https://scrutinizer-ci.com/g/NNTmux/newznab-tmux/?branch=dev"><img src="https://scrutinizer-ci.com/g/NNTmux/newznab-tmux/badges/quality-score.png?b=dev"></a>
    <a href="https://travis-ci.org/NNTmux/newznab-tmux"><img src="https://travis-ci.org/NNTmux/newznab-tmux.svg?branch=dev"></a>
    <a href="https://packagist.org/packages/nntmux/newznab-tmux"><img src="https://poser.pugx.org/nntmux/newznab-tmux/v/stable.svg" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/nntmux/newznab-tmux"><img src="https://poser.pugx.org/nntmux/newznab-tmux/license.svg" alt="License"></a>
    <a href="https://styleci.io/repos/22602573"><img src="https://styleci.io/repos/22602573/shield?branch=dev" alt="StyleCI"></a>
    <a href='https://coveralls.io/github/NNTmux/newznab-tmux?branch=0.x'><img src='https://coveralls.io/repos/github/NNTmux/newznab-tmux/badge.svg?branch=dev' alt='Coverage Status' /></a>
    <a href="https://github.com/prettier/prettier"><img src=https://img.shields.io/badge/code_style-prettier-ff69b4.svg?style=flat-square"></a>
    <a href="https://www.patreon.com/bePatron?u=6160908"><img src="https://c5.patreon.com/external/logo/become_a_patron_button.png" alt="Become a Patron!" height="35"></a>
</p>



NNTmux automatically scans usenet, similar to the way google search bots scan the internet. It does this by collecting usenet headers and temporarily storing them in a database until they can be collated into posts/releases. It provides a web-based front-end providing search, browse, and programmable (API) functionality.

This project is a fork of the open source usenet indexer newznab plus: https://github.com/anth0/nnplus and open source nZEDb usenet indexer https://github.com/nZEDb/nZEDb

NNTmux improves upon the original design, implementing several new features including:

- Optional multi-threaded processing (header retrieval, release creation, post-processing etc)
- Advanced search features (name, subject, category, post-date etc)
- Intelligent local caching of metadata
- Optional sharing of comments with other NNTmux and newznab sites
- Optional tmux (terminal session multiplexing) engine that provides thread, database and performance monitoring
- Image and video samples
- SABnzbd/NZBGet integration (web, API and pause/resume)
- CouchPotato integration (web and API)


## Prerequisites

System Administration know-how. NNTmux is not plug-n-play software. Installation and operation requires a moderate amount of administration experience. NNTmux is designed and developed with GNU/Linux operating systems. Certain features are not available on other platforms. A competent Windows administrator should be able to run NNTmux on a Windows OS.

### Hardware

	4GB RAM, 2 cores(threads) and 20GB disk space minimum.

If you wish to use more than 5 threads a quad core CPU is beneficial.

The overall speed of NNTmux is largely governed by performance of the database. As many of the database tables should be held within system RAM as possible. See Database Section below.

### Software

	PHP 7.2+ (and various modules)
	MySQL 5.6+ (Postgres is not supported)
The installation guides have more detailed software requirements.

### Database

Most (if not all) distributions ship MySQL with a default configuration that will perform well on a Raspberry Pi. If you wish to store more that 500K releases, these default settings will quickly lead to poor performance. Expect this.

As a general rule of thumb the database will need a minimum of 1-2G buffer RAM for every million releases you intend to store. That RAM should be assigned to this parameter:

- innodb_buffer_pool_size

Use [mysqltuner.pl](http://mysqltuner.pl "MySQL tuner - Use it!") for recommendations for these and other important tuner parameters. Also refer to the nZEDb project's wiki page: https://github.com/nZEDb/nZEDb/wiki/Database-tuning. This is particularly important before you start any large imports or backfills.


## Installation

 Follow NNTmux Ubuntu install guide:

 https://github.com/NNTmux/newznab-tmux/wiki/Ubuntu-Install-guide

 For composer install and getting NNTmux follow this guide:

 https://github.com/NNTmux/newznab-tmux/wiki/Installing-Composer

## Docker

 Copy .env.example -> .env, configure .env, mandatory fields:
 - DB_*
 - SPHINX_HOST=manticore
 - SPHINX_PORT=9306
 - NNTP_*
 - ADMIN_*
 - APP_TZ
 - APP_KEY=base64:wbvPP9pBOwifnwu84BeKAVzmwM4TLvcVFowLcPAi6nA= # or generate one!!!
 - REDIS_HOST=redis
 - REDIS_PASSWORD=null
 - REDIS_PORT=6379

 ´´´bash
 docker-compose run --rm nn-tmux sh /tmp/install.sh
 # answer the questions, since this will fetch predb entries this takes an hour or two, just press ctrl + c if you dont like it ;)
 docker-compose run --rm --service-ports -T nn-tmux apachectl -D FOREGROUND 
 ```
 Open http://localhost:8089
 sign in as admin (username, password from .env) and configure side settings and tmux settings

  ´´´bash
  ctrl + c
  docker-compose up nn-tmux
  # check the logs, if everything is working fine ctrl + c
  # start and run in background
  docker-compose up -d nn-tmux
  # start the automatic import
  docker-compose up -d nn-tmux-ui
  # check the monitor tmux
  docker exec -it nn-tmux-ui bash
  # attach
  root@:/var/www/NNTmux# sudo -E -u notroot tmux attach -d
  # stop
  root@:/var/www/NNTmux# sudo -E -u notroot php artisan tmux-ui:stop --kill

```
### Support

 Support is given on irc.synirc.net #tmux channel.

### Licenses

 NNTmux is GPL v3. See LICENSE.txt for the full license.

 Other licenses by various software used by NNTmux:

 Net_NNTP => W3C

 Zip file creation class => No license specified.

 simple_html_dom.php => MIT

 All external libraries will have their full licenses in their respectful folders.





