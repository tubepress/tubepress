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
# Ubuntu-specific mods, mainly just getting rid of the annoying motd and login message.
#
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