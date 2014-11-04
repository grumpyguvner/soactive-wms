#change to a backup directory
cd /home/activewms
#remove any local copies of backup
rm activewms_*.tar.gz
NOWDATE=`date +"20%y-%m-%d-%H%M%S"`
#dump the database:
mysqldump -u activewms -p'St4rl!ght' activewms > activewms_$NOWDATE.sql
#backup the website files and db dump
tar -cvzf activewms_$NOWDATE.tar.gz activewms_$NOWDATE.sql public_html
#delete the database dump file:
rm activewms_$NOWDATE.sql
#move the backup to the backup directory:
cd /home/activewms/backups
cp ../activewms_$NOWDATE.tar.gz .
