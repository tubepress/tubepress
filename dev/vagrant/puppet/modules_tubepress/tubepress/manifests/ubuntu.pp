class tubepress::ubuntu {

  ini_setting { 'suppress-upgrade-notice' :

    ensure  => 'present',
    path    => '/etc/update-manager/release-upgrades',
    section => 'DEFAULT',
    setting => 'Prompt',
    value   => 'never'
  }

  file { '/var/lib/update-notifier/release-upgrade-available' :

    ensure => 'absent',
  }

  file { '/etc/motd' :

    ensure  => 'file',
    content => ''
  }
}