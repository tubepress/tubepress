class tubepress {

  Exec { path => [ '/bin/', '/sbin/' , '/usr/bin/', '/usr/sbin/' ] }

  include tubepress::apache2
  include tubepress::mongo
  include tubepress::packages
  include tubepress::php
  include tubepress::time
  include tubepress::ubuntu
  include tubepress::xhgui
}