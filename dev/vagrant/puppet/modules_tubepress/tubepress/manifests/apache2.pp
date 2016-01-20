class tubepress::apache2 {

  class {[
    '::apache',
    '::apache::mod::actions',
    '::apache::mod::fastcgi',
  ]:

    #libapache2-mod-fastcgi is only in the multiverse
    require => Apt::Source['precise-multiverse']
  }
}