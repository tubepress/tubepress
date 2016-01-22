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
# Installs and configures xhgui
#
class tubepress::webapp::xhgui {

  mongodb::db { 'xhprof':
    user     => 'xhprof',
    password => 'xhprof',
    require  => [ Class['tubepress::mongo'] ]
  }

  file { [ '/var/www/xhgui', '/var/www/.composer' ]:

    ensure => 'directory',
    owner  => 'www-data',
    group  => 'www-data',
    mode   => 0755,
  }

  vcsrepo { '/var/www/xhgui':
    ensure   => present,
    provider => git,
    source   => 'https://github.com/perftools/xhgui.git',
    require  => [ Package['git'], File['/var/www/xhgui', '/var/www/.composer'] ],
    user     => 'www-data',
  }

  exec { 'install-xhgui' :

    command => 'php ./install.php',
    cwd     => '/var/www/xhgui',
    require => [
      Vcsrepo['/var/www/xhgui'],
      Class['::php::cli'],
      Package['php5-mcrypt']
    ],
    user    => 'www-data',
    creates => '/var/www/xhgui/vendor',
    environment => [ "HOME=/var/www" ],
  }

  apache::vhost { 'profiler.tubepress-test.com':
    docroot => '/var/www/xhgui/webroot',
    require => [
      Exec['install-xhgui'],
    ],
    proxy_pass_match => [
      {
        'path' => '^/(.*\.php(/.*)?)$',
        'url'  => 'fcgi://127.0.0.1:9000/var/www/xhgui/webroot/$1'
      }
    ],
    directories => [
      {
        'path'         => '/var/www/xhgui/webroot',
        directoryindex => '/index.php index.php',
        allow_override => 'All',
      }
    ],
    notify => Service['apache2'],
  }

  file { '/var/www/xhgui/config/config.php' :

    ensure  => 'file',
    owner   => 'www-data',
    group   => 'www-data',
    mode    => 0644,
    source  => 'puppet:///modules/tubepress/xhgui/config.php',
    require => Vcsrepo['/var/www/xhgui'],
  }

  php::fpm::config { 'auto_prepend_file=/var/www/xhgui/external/header.php':
    section => 'PHP',
  }

  file { '/tmp/xhgui_mongo_init.js' :

    ensure  => 'file',
    source  => 'puppet:///modules/tubepress/xhgui/mongo_init.txt',
    notify  => Exec['init-xhgui-mongo'],
    require => Mongodb::Db['xhprof']
  }

  exec { 'init-xhgui-mongo' :

    refreshonly => true,
    command     => 'mongo < /tmp/xhgui_mongo_init.js',
  }
}