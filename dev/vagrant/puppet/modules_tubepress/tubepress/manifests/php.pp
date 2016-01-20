#
# Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
#
#  This file is part of TubePress (http://tubepress.com)
#
#  This Source Code Form is subject to the terms of the Mozilla Public
#  License, v. 2.0. If a copy of the MPL was not distributed with this
#  file, You can obtain one at http://mozilla.org/MPL/2.0/.
#

#
# Installs and configures PHP and extensions. This class is carefully
# constructed to work 100% on the first "vagrant up". Edit at your own risk.
#
class tubepress::php {

  include php

  class {[
    '::php::fpm',
    '::php::cli',
  ]:
    settings => [
      'set Date/date.timezone America/Los_Angeles'
    ]
  } ~>

  package {[
    'php-pear',
    'php5-xdebug',
    'php-apc',
    'php5-curl',
    'php5-mcrypt',
  ] :

    ensure => 'present'
  } ~>

  package {[
    'mongo',
    'xhprof-beta',
  ]:
    provider => 'pecl',
  } ~>

  file { '/etc/php5/conf.d/xhprof.ini' :

    ensure  => 'file',
    owner   => 'root',
    group   => 'root',
    mode    => 0644,
    content => 'extension=xhprof.so',
    notify  => Service['php5-fpm'],
  } ~>

  file { '/etc/php5/conf.d/mongo.ini' :

    ensure  => 'file',
    owner   => 'root',
    group   => 'root',
    mode    => 0644,
    content => 'extension=mongo.so',
    notify  => Service['php5-fpm'],
  }

  php::fpm::pool { 'www' :

    listen       => '/var/run/php5-fpm.sock',
    listen_owner => 'www-data',
    listen_group => 'www-data',
  } ~>

  ::php::config {[
    "zend_extension=/usr/lib/php5/20090626/xdebug.so",
    "xdebug.remote_connect_back=1",
    "xdebug.xdebug.remote_enable=1"
  ]:
    file    => '/etc/php5/conf.d/xdebug.ini',
    section => '',
  }
}