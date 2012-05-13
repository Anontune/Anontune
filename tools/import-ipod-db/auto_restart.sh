!/bin/bash

while [ 1 ]

do

	APPCHK=$(ps aux | grep -c "[p]ython .*insert_data[.]py.*")

	if [ $APPCHK == 1 ]; then
		sleep 3.0
		continue
		exit

	else
                #(killall python)
		(nohup python /home/anontune/insert_data.py &> /dev/null &)

	fi

	sleep 3.0

done

