# Just run
#   make clean all archives
# to get fresh and ready to deploy .tbz2 and .zip archives

MAKEOBJ ?= ./makeobj

DESTDIR  ?= simutrans
PAKDIR   ?= $(DESTDIR)/pak
DESTFILE ?= simupak64

DIRS64 :=
DIRS64 += city_extra
DIRS64 += com
DIRS64 += cur
DIRS64 += factory
#DIRS64 += factory_alcohol
#DIRS64 += factory_computer
#DIRS64 += factory_food
#DIRS64 += factory_waste
DIRS64 += ground
#DIRS64 += ground/oldgrounds
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
#DIRS64 += way/Hebewerk

DIRS128 :=
DIRS128 += big-logo


DIRS := $(DIRS64) $(DIRS128)


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
	@mkdir -p $(PAKDIR)/sound $(PAKDIR)/text $(PAKDIR)/config
	@cp -p compat/compat.tab $(PAKDIR)
	@cp -p sound/* $(PAKDIR)/sound
	@cp -p config/* $(PAKDIR)/config
	@cp -p pak.text/* $(PAKDIR)/text

$(DIRS64):
	@echo "===> PAK64 $@"
	@mkdir -p $(PAKDIR)
	@$(MAKEOBJ) quiet PAK $(PAKDIR)/ $@/ > /dev/null

$(DIRS128):
	@echo "===> PAK128 $@"
	@mkdir -p $(PAKDIR)
	@$(MAKEOBJ) quiet PAK128 $(PAKDIR)/ $@/ > /dev/null

clean:
	@echo "===> CLEAN"
	@rm -fr $(PAKDIR) $(DESTFILE).tbz2 $(DESTFILE).zip
