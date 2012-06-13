# Just run
#   make clean all archives
# to get fresh and ready to deploy .tbz2 and .zip archives

MAKEOBJ ?= ./makeobj

DESTDIR  ?= simutrans
PAKDIR   ?= $(DESTDIR)/pak
ADDONDIR ?= $(DESTDIR)/addons/pak
DESTFILE ?= simupak64

OUTSIDE :=
OUTSIDE += ground

DIRS64 :=
DIRS64 += city_extra
DIRS64 += com
DIRS64 += cur
DIRS64 += factory
DIRS64 += factory_waste
DIRS64 += ind
DIRS64 += monument
DIRS64 += other
DIRS64 += player
DIRS64 += res
DIRS64 += trees
DIRS64 += vehicle/road
DIRS64 += vehicle/track
DIRS64 += vehicle/water
DIRS64 += vehicle/air
DIRS64 += vehicle/monorail
DIRS64 += vehicle/trams
DIRS64 += way

DIRS128 :=
DIRS128 += big-logo

ADDON_DIRS64 :=
#ADDON_DIRS64 += factory_alcohol
#ADDON_DIRS64 += factory_computer
ADDON_DIRS64 += factory_food


DIRS := $(OUTSIDE) $(DIRS64) $(DIRS128)
ADDON_DIRS := $(ADDON_DIRS64)


.PHONY: $(DIRS) $(ADDON_DIRS64) copy tar zip

all: copy $(DIRS) zip

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
	@cp -p compat/compat.tab $(PAKDIR)
	@cp -p sound/* $(PAKDIR)/sound
	@cp -p config/* $(PAKDIR)/config
	@cp -p scenario/* $(PAKDIR)/scenario
	@cp -p pak.text/* $(PAKDIR)/text

$(DIRS64):
	@echo "===> PAK64 $@"
	@mkdir -p $(PAKDIR)
	@$(MAKEOBJ) quiet PAK $(PAKDIR)/ $@/ > /dev/null

$(DIRS128):
	@echo "===> PAK128 $@"
	@mkdir -p $(PAKDIR)
	@$(MAKEOBJ) quiet PAK128 $(PAKDIR)/ $@/ > /dev/null

$(OUTSIDE):
	@echo "===> OUTSIDE with REVISION and grounds"
	@mkdir -p $(PAKDIR)
	@echo -e -n "Obj=ground\nName=Outside\ncopyright=pak64 112.0 r" >$@/outside.dat
	@svnversion >>$@/outside.dat
	@echo -e "Image[0][0]=outside.0.0\n-" >>$@/outside.dat
	@$(MAKEOBJ) PAK64 $(PAKDIR)/ $@/ > /dev/null
	@rm $@/outside.dat

clean:
	@echo "===> CLEAN"
	@rm -fr $(PAKDIR) $(DESTFILE).tbz2 $(DESTFILE).zip

addons: 	clean copy_addons $(ADDON_DIRS64)

copy_addons:
	@echo "===> COPY"
	@mkdir  -p $(ADDONDIR)

$(ADDON_DIRS64):
	@echo "===> PAK64 $@"
	@mkdir -p $(ADDONDIR)
	@$(MAKEOBJ) quiet PAK $(ADDONDIR)/ $@/ > /dev/null
