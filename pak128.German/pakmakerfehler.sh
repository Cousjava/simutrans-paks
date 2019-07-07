#!/bin/bash
echo -e "PAKMAKER FEHLER\n">"./PAKMAKERFEHLER.txt"
cd LOG
grep -R -i "Warning\|Error">>"../PAKMAKERFEHLER.txt"
