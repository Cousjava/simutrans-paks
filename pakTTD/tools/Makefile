# Project: grf2dat
# Makefile created by Dev-C++ 4.9.9.2

CPP  = g++.exe -D__DEBUG__
CC   = gcc.exe -D__DEBUG__
WINDRES = windres.exe
RES  = 
OBJ  = grf2dat.o rdwr_png.o rdwr_pcx.o $(RES)
LINKOBJ  = grf2dat.o rdwr_png.o rdwr_pcx.o $(RES)
LIBS =  -lpng -lz -lstdc++
INCS =  
CXXINCS = 
BIN  = grf2dat.exe
CXXFLAGS = $(CXXINCS)   -g3
CFLAGS = $(INCS)   -g3
RM = rm -f

.PHONY: all all-before all-after clean clean-custom

all: all-before grf2dat.exe all-after


clean: clean-custom
	${RM} $(OBJ) $(BIN)

$(BIN): $(OBJ)
	$(CC) $(LINKOBJ) -o "grf2dat.exe" $(LIBS)

grf2dat.o: grf2dat.c
	$(CC) -c grf2dat.c -o grf2dat.o $(CFLAGS)

rdwr_png.o: rdwr_png.c
	$(CC) -c rdwr_png.c -o rdwr_png.o $(CFLAGS)

rdwr_pcx.o: rdwr_pcx.c
	$(CC) -c rdwr_pcx.c -o rdwr_pcx.o $(CFLAGS)
