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
# Adds a few packages, removes cruft, and configures APT repos.
#
class tubepress::packages {

  package {[
    'apt',
    'bash',
    'deborphan',
    'file',
    'git',
    'grep',
    'gzip',
    'lsof',
    'tar',
    'telnet',
    'tree',
    'unzip',
    'vim',
    'zip',
  ]:
    ensure => 'installed'
  }

  package {[
    'acpi',
    'acpi-support-base',
    'acpid',
    'apparmor',
    'apport',
    'apport-symptoms',
    'aptitude',
    'aptitude-common',
    'aptitude-doc-en',
    'apt-listchanges',
    'at',
    'bc',
    'busybox',
    'byobu',
    'cloud-init',
    'cloud-initramfs-rescuevol',
    'cloud-utils',
    'console-setup',
    'console-setup-linux',
    'db5.1-util',
    'dc',
    'debconf-i18n',
    'debian-faq',
    'dictionaries-common',
    'discover',
    'discover-data',
    'doc-debian',
    'docutils-common',
    'docutils-doc',
    'ed',
    'emacsen-common',
    'exim4',
    'exim4-base',
    'exim4-config',
    'exim4-daemon-light',
    'fontconfig',
    'fonts-liberation',
    'fonts-ubuntu-font-family-console',
    'ftp',
    'fuse',
    'git-core',
    'heirloom-mailx',
    'hicolor-icon-theme',
    'host',
    'iamerican',
    'ibritish',
    'ienglish-common',
    'info',
    'installation-report',
    'ispell',
    'iw',
    'juju',
    'libavahi-common3',
    'mlocate',
    'mutt',
    'nano',
    'nfacct',
    'nfs-common',
    'popularity-contest',
    'ppp',
    'procmail',
    'reportbug',
    'rpcbind',
    'task-english',
    'tasksel',
    'tasksel-data',
    'texinfo',
    'traceroute',
    'ufw',
    'util-linux-locales',
    'w3m',
    'x11-common',
    'xauth',
    'xserver-common',
  ]:
    ensure => 'purged'
  }
}