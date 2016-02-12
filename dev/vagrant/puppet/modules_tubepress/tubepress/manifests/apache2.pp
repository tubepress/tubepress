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
# Installs Apache and a few basic mods
#
class tubepress::apache2 {

  class {[
    '::apache',
    '::apache::mod::proxy',
    '::apache::mod::rewrite',
  ]:
  }

  ::apache::mod { 'proxy_fcgi' : }

  file { '/var/www/tubepress' :

    ensure  => 'directory',
    owner   => 'www-data',
    group   => 'www-data',
    mode    => 0755,
    require => Class['::apache'],
  }
}