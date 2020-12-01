#!/bin/bash
# Script that executes on startup of the container.
# Execute everything and dont leave a foreground process as we want to exit out as soon as we're done.

cd /root/src/scripts
php generate-commands.php
python3 update-stopwords.py
python3 generate-pickle-cache.py
chmod +x multiprocess.sh
./multiprocess.sh commands.sh
php swap-cache-tables.php
php compress-images.php
