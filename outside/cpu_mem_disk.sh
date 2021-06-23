#!/bin/sh

path=$(dirname "$PWD")/log/cpu_mem_disk.txt

free -m | awk 'NR==2{printf "Memory Usage: %s/%sMB (%.2f%%)\n", $3,$2,$3*100/$2 }' > $path
df -h | awk '$NF=="/"{printf "Disk Usage: %d/%dGB (%s)\n", $3,$2,$5}' >> $path
top -bn1 | grep load | awk '{printf "CPU Load: %.2f%%\n", $(NF-2)}' >> $path
