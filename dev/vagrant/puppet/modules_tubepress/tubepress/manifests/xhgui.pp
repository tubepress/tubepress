class tubepress::xhgui {

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
      Class['tubepress::apache2']
    ],
    action => "php5-fcgi",
    fastcgi_server => '/usr/lib/cgi-bin/php5-fcgi',
    fastcgi_socket => '/var/run/php5-fpm.sock',
    aliases => [
      {
        path => '/usr/lib/cgi-bin/php5-fcgi',
        alias => '/cgi-bin',
      }
    ],
    directories => {
      path        => '/var/www/xhgui/webroot',
      options     => '+ExecCGI',
      addhandlers => {
        handler    => 'php5-fcgi',
        extensions => '.php',
      },
    },
  }
}