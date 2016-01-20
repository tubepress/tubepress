class tubepress::time {

  include '::ntp'

  class { 'timezone':
    timezone => 'America/Los_Angeles',
  }
}