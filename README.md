# mn-checker

1. download file checkmn.php to your arionum node directory
2. set up your array of ip addresses to check
3. set up your email address for notifications
4. add your node url to peers list https://www.ariochain.info/peers/addpeer
5. test checker `php checkmn.php`
6. set `$show = false` if you don't want to get email with full results from server everytime cron will check status
7. set up cron task to check status periodicaly

