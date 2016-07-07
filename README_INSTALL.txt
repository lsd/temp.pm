Temp.PM - Temporary Private Message
-----------------------------------------

Contact: temp.pm (at) riseup (dot) net / temp.pm (at) protonmail (dot) com
Web: https://temp.pm
License: Do what you want but keep it free and open source and include original authors info/contact details!

Requirements:
- HTTP server with HTTPS/SSL (preferably Apache2, with mod_rewrite + mod_headers)
- PHP5 (with OpenSSL enabled)
- PHP5-mcrypt + mcrypt
- crontab
- shred (coreutils)
- wget

-----------------------------------------
	
Install:

1. Edit index.php, stats_delete.php, stats_created.php, load.php, htaccess_example.txt (rename this to .htaccess) and temp.pm_crontab.sh to suit your needs.

Note and stat directories SHOULD NOT BE INSIDE any normal www document root dir!
Apache site configuration example is in apache_example.txt

2. Copy all necessary content to your www document dir

3. Create these message/stats directories (change the paths accordingly to the ones you used earlier, for example):
/path/for/messages/txt
/path/for/messages/txt/stats
/path/for/messages/txt/1
/path/for/messages/txt/12h
/path/for/messages/txt/15m
/path/for/messages/txt/1h
/path/for/messages/txt/3
/path/for/messages/txt/30
/path/for/messages/txt/30m
/path/for/messages/txt/45m
/path/for/messages/txt/60
/path/for/messages/txt/6h
/path/for/messages/txt/7
/path/for/messages/txt/stats
/path/for/messages/txt/stats/1
/path/for/messages/txt/stats/30
/path/for/messages/txt/stats/7
/path/for/messages/txt/stats/random

4. Create these files (change the paths accordingly to the ones you used earlier, for example):
/path/for/messages/txt/stats/1.txt
/path/for/messages/txt/stats/30.txt
/path/for/messages/txt/stats/7.txt
/path/for/messages/txt/stats/hits.txt
/path/for/messages/txt/stats/waiting.txt

5. Edit these lines according to the paths you used earlier and add to root user's crontab:
	
@reboot swapoff -a >/dev/null 2>&1 ;
* * * * * /path/for/temp_pm_crontab.sh
*/15 * * * * wget -q -O /dev/null https://YOUR_DOMAIN_HERE/stats_delete.php?d=1 >/dev/null 2>&1 ;
*/15 * * * * wget -q -O /dev/null https://YOUR_DOMAIN_HERE/stats_delete.php?d=7 >/dev/null 2>&1 ;
*/15 * * * * wget -q -O /dev/null https://YOUR_DOMAIN_HERE/stats_delete.php?d=30 >/dev/null 2>&1 ;
*/15 * * * * wget -q -O /dev/null https://YOUR_DOMAIN_HERE/stats_delete.php?d=hits >/dev/null 2>&1 ;
*/15 * * * * wget -q -O /dev/null https://YOUR_DOMAIN_HERE/stats_delete.php?d=random >/dev/null 2>&1 ;
*/15 * * * * wget -q -O /dev/null https://YOUR_DOMAIN_HERE/stats_created.php >/dev/null 2>&1 ;

6. Make sure that everything works (all directories you used exist, permission are correct etc.)

7. Please disable all HTTP server/Apache logs (just comment them out in your configs), disable swapping (from fstab) and don’t keep backups of the message data! It’s also recommended to store the message data inside a VeraCrypt partition/container for added security.

8. IF YOU USE THIS SCRIPT ONLINE, PLEASE KEEP IT UPDATED!

Latest source code package can be found at https://temp.pm/?about

Send us an email (address is at the top of this document) if you wish to be notified when a new version is released.

Regards,
Temp.PM staff
