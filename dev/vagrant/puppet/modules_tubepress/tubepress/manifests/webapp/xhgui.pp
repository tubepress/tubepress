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

  file { [ '/var/www/xhgui', '/var/www/.composer' ]:

    ensure => 'directory',
    owner  => 'www-data',
    group  => 'www-data',
    mode   => 0755,
  }

  vcsrepo { '/var/www/xhgui':
    ensure   => present,
    provider => git,
    source   => 'https://github.com/Tuurlijk/xhprof',
    require  => [ Package['git'], File['/var/www/xhgui', '/var/www/.composer'] ],
    user     => 'www-data',
  }

  apache::vhost { 'profiler.tubepress-test.com':
    docroot => '/var/www/xhgui/xhprof_html',
    require => [
      Vcsrepo['/var/www/xhgui'],
    ],
    proxy_pass_match => [
      {
        'path' => '^/(.*\.php(/.*)?)$',
        'url'  => 'fcgi://127.0.0.1:9000/var/www/xhgui/xhprof_html/$1'
      }
    ],
    directories => [
      {
        'path'         => '/var/www/xhgui/xhprof_html',
        directoryindex => '/index.php index.php',
        allow_override => 'All',
      }
    ],
    notify => Service['apache2'],
  }

  file { '/var/www/xhgui/xhprof_lib/config.php' :

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

  ::mysql::db { 'xhprof' :
    user => 'xhprof',
    password => 'xhprof',
    grant => ['ALL'],
    sql => '/var/www/xhgui/xhprof_lib/utils/Db/Mysqli.sql',
    require => Vcsrepo['/var/www/xhgui'],
  }
}