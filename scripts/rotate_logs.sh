NOWDATE=`date +"20%y-%m-%d-%H%M%S"`

cd /home/www-data/soactive-wms/modules/netro_email/log

cp logs.txt log_$NOWDATE.txt
echo $NOWDATE > logs.txt

cp newsletter.txt newsletter_$NOWDATE.txt
echo $NOWDATE > newsletter.txt
