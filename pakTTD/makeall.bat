mkdir pak.ttd

cd city
..\makeobj pak >err.txt
copy *.pak ..\pak.ttd\
del *.pak

cd ..\citycars
..\makeobj pak >err.txt
copy *.pak ..\pak.ttd\
del *.pak

cd ..\from-pak64
..\makeobj pak >err.txt
copy *.pak ..\pak.ttd\
del *.pak

cd ..\factory
..\makeobj pak >err.txt
copy *.pak ..\pak.ttd\
del *.pak

cd ..\ground
..\makeobj pak >err.txt
copy *.pak ..\pak.ttd\
del *.pak

cd ..\other
..\makeobj pak >err.txt
copy *.pak ..\pak.ttd\
del *.pak

cd ..\player
..\makeobj pak >err.txt
copy *.pak ..\pak.ttd\
del *.pak

cd ..\trees
..\makeobj pak >err.txt
copy *.pak ..\pak.ttd\
del *.pak

cd ..\ways
..\makeobj pak >err.txt
copy *.pak ..\pak.ttd\
del *.pak

cd ..\pak.ttd
mkdir config
copy ..\config\*.tab config\