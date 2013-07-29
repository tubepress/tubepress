#!/bin/bash

ROOT="$HOME/Dropbox/git/code/public/tubepress"

cd "$ROOT/src/main/assembly"
ant quick
cd "$ROOT/../../private/tubepress-pro/src/main/assembly"
ant quick
rsync -rlpDv --delete stage/tubepress_pro_3_1_0/ ~/Dropbox/git/sites/private/ttg.lan/web/wordpress/wp-content/plugins/tubepress/
