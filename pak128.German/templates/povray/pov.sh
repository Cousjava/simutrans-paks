#!/bin/bash
case $1 in

k800)  povray my[800] +Ikonstruktion.pov
;;
k1024)  povray my[1024] +Ikonstruktion.pov
;;
my768)  povray my[768] +IRES_ALP_1801_MHorange_02_sommer.pov
;;
my128*)  povray my[128] +IRES_ALP_1801_MHorange_02_sommer.pov
;;
my_template)povray my[768] +Iiso_template.pov
;;
esac
