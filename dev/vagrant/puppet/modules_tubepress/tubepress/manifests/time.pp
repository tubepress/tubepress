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
# Installs NTP and assigns the timezone of the server to Los Angeles
#
class tubepress::time {

  include '::ntp'

  class { 'timezone':
    timezone => 'America/Los_Angeles',
  }
}