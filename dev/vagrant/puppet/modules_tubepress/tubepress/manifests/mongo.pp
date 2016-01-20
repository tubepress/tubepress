class tubepress::mongo {

  class { '::mongodb::server' :

    noauth => true
  }

  package { 'ruby-json' :

    ensure => 'installed',
  }
}