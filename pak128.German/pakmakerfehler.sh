#!/bin/bash
echo -e "PAKMAKER FEHLER\n">"./PAKMAKERFEHLER.txt"
cd LOG
grep -R -i "Warning\|Error">>"../PAKMAKERFEHLER.txt"
while true
do
	read -n 1 -p "q zum beenden druecken." q
	case "$q" in
		q)	break;;
		*)	;;
	esac
done
