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

  package { [
    'php-pear',
    'libyaml-dev',
  ]:

    ensure => 'present',
  } ~>

  class { '::php::dev' :

  } ~>

  class {[
    '::php::fpm',
    '::php::cli',
  ]:
    settings => [
      'set Date/date.timezone America/Los_Angeles'
    ]
  } ~>

  class {[
    '::php::extension::gd',
    '::php::extension::mcrypt',
    '::php::extension::mongo',
    '::php::extension::opcache',
    '::php::extension::xhprof',
    '::php::extension::yaml'
  ]:

  } ~>

  file { [
    '/etc/php5/cli/conf.d/20-yaml.ini',
    '/etc/php5/fpm/conf.d/20-yaml.ini',
    ]:

    ensure => 'link',
    target => '/etc/php5/mods-available/yaml.ini',
    notify => Service['php5-fpm'],
  } ~>

  file { [
    '/etc/php5/cli/conf.d/20-xhprof.ini',
    '/etc/php5/fpm/conf.d/20-xhprof.ini',
  ]:
    ensure => 'link',
    target => '/etc/php5/mods-available/xhprof.ini',
    notify => Service['php5-fpm'],
  } ~>

  file { [
    '/etc/php5/cli/conf.d/20-mongo.ini',
    '/etc/php5/fpm/conf.d/20-mongo.ini',
  ]:
    ensure => 'link',
    target => '/etc/php5/mods-available/mongo.ini',
    notify => Service['php5-fpm'],
  } ~>

  class { '::php::extension::xdebug':
    settings => [
      'set ".anon/zend_extension" "xdebug.so"',
      'set ".anon/xdebug.remote_connect_back" "1"',
      'set ".anon/xdebug.remote_enable" "1"',
    ]
  } ~>

  php::fpm::pool { 'www' :


  }
}