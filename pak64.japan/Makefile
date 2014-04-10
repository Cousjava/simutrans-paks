# Just run
#   make clean all archives
# to get fresh and ready to deploy .tbz2 and .zip archives

MAKEOBJ ?= ./makeobj

DESTDIR  ?= simutrans
PAKDIR   ?= $(DESTDIR)/pak.japan
DESTFILE ?= simupak64-japan

OUTSIDE :=
OUTSIDE += ground

DIRS64 :=
DIRS64 += city
DIRS64 += comindres
DIRS64 += cur
DIRS64 += cur-city
DIRS64 += factory
DIRS64 += other
DIRS64 += player
DIRS64 += trees
DIRS64 += vehicles/road
DIRS64 += vehicles/track
DIRS64 += vehicles/water
DIRS64 += vehicles/air
DIRS64 += vehicles/shinkansen
DIRS64 += vehicles/tram
DIRS64 += ways

DIRS128 :=
DIRS128 += big-logo


DIRS := $(OUTSIDE) $(DIRS64) $(DIRS128)


.PHONY: $(DIRS) copy tar zip

all: copy $(DIRS)

archives: tar zip

tar: $(DESTFILE).tbz2
zip: $(DESTFILE).zip

$(DESTFILE).tbz2: $(PAKDIR)
	@echo "===> TAR $@"
	@tar cjf $@ $(DESTDIR)

$(DESTFILE).zip: $(PAKDIR)
	@echo "===> ZIP $@"
	@zip -rq $@ $(DESTDIR)

copy:
	@echo "===> COPY"
	@mkdir -p $(PAKDIR)/sound $(PAKDIR)/text $(PAKDIR)/config $(PAKDIR)/scenario
#	@cp -p compat/compat.tab $(PAKDIR)
	@cp -p sound/* $(PAKDIR)/sound
	@cp -p config/* $(PAKDIR)/config
#	@cp -p scenario/* $(PAKDIR)/scenario
	@cp -p pak.text/* $(PAKDIR)/text

$(DIRS64):
	@echo "===> PAK64 $@"
	@mkdir -p $(PAKDIR)
	@$(MAKEOBJ) verbose PAK $(PAKDIR)/ $@/ > /dev/null

$(DIRS128):
	@echo "===> PAK128 $@"
	@mkdir -p $(PAKDIR)
	@$(MAKEOBJ) quiet PAK128 $(PAKDIR)/ $@/ > /dev/null

$(OUTSIDE):
	@echo "===> OUTSIDE with REVISION and grounds"
	@mkdir -p $(PAKDIR)
	@echo -e -n "Obj=ground\nName=Outside\ncopyright=pak64.japan 120.0 r" >$@/outside.dat
	@svnversion >>$@/outside.dat
	@echo -e "Image[0][0]=outside.0.0\n-" >>$@/outside.dat
	@$(MAKEOBJ) PAK64 $(PAKDIR)/ $@/ > /dev/null
	@rm $@/outside.dat

clean:
	@echo "===> CLEAN"
	@rm -fr $(PAKDIR) $(DESTFILE).tbz2 $(DESTFILE).zip
