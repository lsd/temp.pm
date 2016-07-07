# Securely remove expired Temp.PM messages
find /path/to/your/notes/15m/* -mmin 15 -exec shred -f --remove -n 7 {} \;
find /path/to/your/notes/30m/* -mmin 30 -exec shred -f --remove -n 7 {} \;
find /path/to/your/notes/1h/* -mmin 60 -exec shred -f --remove -n 7 {} \;
find /path/to/your/notes/6h/* -mmin 360 -exec shred -f --remove -n 7 {} \;
find /path/to/your/notes/12h/* -mmin 720 -exec shred -f --remove -n 7 {} \;
find /path/to/your/notes/1/* -mtime 1 -exec shred -f --remove -n 7 {} \;
find /path/to/your/notes/3/* -mtime 3 -exec shred -f --remove -n 7 {} \;
find /path/to/your/notes/7/* -mtime 7 -exec shred -f --remove -n 7 {} \;
find /path/to/your/notes/30/* -mtime 30 -exec shred -f --remove -n 7 {} \;
find /path/to/your/notes/60/* -mtime 60 -exec shred -f --remove -n 7 {} \;