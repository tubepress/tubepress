#!/bin/bash

cd ~/Dropbox/git/code/public/tubepress/src/main/assembly
ant quick; rsync -avh --delete stage/tubepress/ ~/Desktop/tubepress-vagrant/wordpress/wp-content/plugins/tubepress/
