#!/bin/bash
# Script that executes on startup of the container.
# Execute everything and dont leave a foreground process as we want to exit out as soon as we're done.

cd /root/src/scripts

# Make the multiprocess script executeable so we can use it later.
chmod +x multiprocess.sh

# Run the steps for the machine-learning generation of the swaps category.
/usr/bin/php generate-ml-commands.php # this also generates the relevant JSON files they take as input
./multiprocess.sh commands.sh
/usr/bin/php import-ml-output.php

# Run the steps for calculating swaps.
python3 update-stopwords.py
/usr/bin/php generate-food-consolidated-json-file.php "/root/data.json"
python3 generate-pickle-cache.py "/root/data.json"
/usr/bin/php generate-calculate-swaps-commands.php
./multiprocess.sh commands.sh
/usr/bin/php swap-cache-tables.php

/usr/bin/php compress-images.php
