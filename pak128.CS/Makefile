# Makefile based on the pak64 Makefile. Thank you prissi et al!
# Makefile for pak128-britain standard and experimental
# 2010-06 sdog
# 2010-06 neroden
# 2011-01 sdog (adopting for pak128)
# 2013-09 kierongreen
# 2013-11 Fabio Gonella
# 2014-07 Miziiik (editting for pak128.CS)
#
# Just run
# make clean all archives
# to get fresh and ready to deploy .tbz2 and .zip archives
#
# version in pak128.CS svn by sdog, downloaded from:
# https://github.com/sdog/simutrans-pak128/raw/master/Makefile
#
CONFIG ?= config.default
-include $(CONFIG)

MAKEOBJ ?= makeobj
PAKNAME ?= pak128.CS
DESTDIR ?= .
PAKDIR  ?= $(DESTDIR)/$(PAKNAME)
DESTFILE ?= $(PAKNAME)

EXTENDED := $(shell makeobj capabilities 2>&1 > /dev/null |grep Extended)

NAMEPREFIX ?=
NAMESUFFIX ?=

CPFILES := pak128.CS.prototype

DIRS64 :=
DIRS64 += base/misc_GUI_64

DIRS128 :=
DIRS128 += factories 
DIRS128 += factories/goods
DIRS128 += factories/powerplants
DIRS128 += cityhouses/com
DIRS128 += cityhouses/ind
DIRS128 += cityhouses/res
DIRS128 += special_buildings/city
DIRS128 += special_buildings/landscape
DIRS128 += special_buildings/monuments
DIRS128 += special_buildings/townhalls
DIRS128 += citycars
DIRS128 += citycars/Avia
DIRS128 += citycars/GAZ
DIRS128 += citycars/Ford
DIRS128 += citycars/Iveco
DIRS128 += citycars/Liaz
DIRS128 += citycars/Mercedes
DIRS128 += citycars/Renault
DIRS128 += citycars/ruzne
DIRS128 += citycars/Robur
DIRS128 += citycars/Skoda
DIRS128 += citycars/TATRA
DIRS128 += citycars/VAZ
DIRS128 += landscape/grounds
DIRS128 += landscape/trees
DIRS128 += landscape/groundobj_static
DIRS128 += base/pedestrians
DIRS128 += base/smokes

DIRS128VEHICLES :=
DIRS128VEHICLES += ng-cargo
DIRS128VEHICLES += ng-engines
DIRS128VEHICLES += ng-psg+mail
DIRS128VEHICLES += rail-cargo
DIRS128VEHICLES += rail-cargo/tank-cars
DIRS128VEHICLES += rail-cargo/tank-cars/Uac
DIRS128VEHICLES += rail-cargo/tank-cars/Uacs
DIRS128VEHICLES += rail-cargo/tank-cars/Zacns
DIRS128VEHICLES += rail-cargo/tank-cars/Zagkks
DIRS128VEHICLES += rail-cargo/tank-cars/Zans
DIRS128VEHICLES += rail-cargo/chladici
DIRS128VEHICLES += rail-cargo/kryte/Gags
DIRS128VEHICLES += rail-cargo/kryte/Gbgs
DIRS128VEHICLES += rail-cargo/kryte/Gbkkqss
DIRS128VEHICLES += rail-cargo/kryte/Gbs
DIRS128VEHICLES += rail-cargo/kryte/Glm
DIRS128VEHICLES += rail-cargo/kryte/Habbillnss
DIRS128VEHICLES += rail-cargo/kryte/Habiikks
DIRS128VEHICLES += rail-cargo/kryte/Hccrrss
DIRS128VEHICLES += rail-cargo/kryte/Hadgs
DIRS128VEHICLES += rail-cargo/kryte/Taes
DIRS128VEHICLES += rail-cargo/nizkostenne
DIRS128VEHICLES += rail-cargo/plosinove/Kils
DIRS128VEHICLES += rail-cargo/plosinove/Ks
DIRS128VEHICLES += rail-cargo/plosinove/Laaers
DIRS128VEHICLES += rail-cargo/plosinove/Laaps
DIRS128VEHICLES += rail-cargo/plosinove/Lgs
DIRS128VEHICLES += rail-cargo/plosinove/Rils
DIRS128VEHICLES += rail-cargo/plosinove/Sdgmrs
DIRS128VEHICLES += rail-cargo/plosinove/Sggmrss
DIRS128VEHICLES += rail-cargo/plosinove/Sggnss
DIRS128VEHICLES += rail-cargo/plosinove/Sggrss
DIRS128VEHICLES += rail-cargo/plosinove/Sgnss
DIRS128VEHICLES += rail-cargo/plosinove/Sgs
DIRS128VEHICLES += rail-cargo/specialni
DIRS128VEHICLES += rail-cargo/vysypne
DIRS128VEHICLES += rail-cargo/vysypne/Faccs
DIRS128VEHICLES += rail-cargo/vysypne/Falls
DIRS128VEHICLES += rail-cargo/vysypne/Sas
DIRS128VEHICLES += rail-cargo/vysypne/Tanoos
DIRS128VEHICLES += rail-cargo/vysypne/Tdgns
DIRS128VEHICLES += rail-cargo/vysypne/Tdns
DIRS128VEHICLES += rail-cargo/vysypne/Tdgs
DIRS128VEHICLES += rail-cargo/vysypne/Tds
DIRS128VEHICLES += rail-cargo/vysypne/Wap
DIRS128VEHICLES += rail-engines-diesel
DIRS128VEHICLES += rail-engines-electric/battery
DIRS128VEHICLES += rail-engines-electric/AC
DIRS128VEHICLES += rail-engines-electric/DC
DIRS128VEHICLES += rail-engines-electric/multi-system
DIRS128VEHICLES += rail-engines-steam
DIRS128VEHICLES += rail-psg+mail
DIRS128VEHICLES += rail-psg+mail/440_640_650
DIRS128VEHICLES += rail-psg+mail/451_452
DIRS128VEHICLES += rail-psg+mail/460_560
DIRS128VEHICLES += rail-psg+mail/471_671
DIRS128VEHICLES += rail-psg+mail/680
DIRS128VEHICLES += rail-psg+mail/810
DIRS128VEHICLES += rail-psg+mail/814
DIRS128VEHICLES += rail-psg+mail/820
DIRS128VEHICLES += rail-psg+mail/830
DIRS128VEHICLES += rail-psg+mail/840_841
DIRS128VEHICLES += rail-psg+mail/842_843
DIRS128VEHICLES += rail-psg+mail/850_851
DIRS128VEHICLES += rail-psg+mail/860
DIRS128VEHICLES += road-cargo
DIRS128VEHICLES += road-cargo/LaK
DIRS128VEHICLES += road-cargo/PRAGA
DIRS128VEHICLES += road-psg+mail
DIRS128VEHICLES += road-psg+mail/IRISBUS_IVECO
DIRS128VEHICLES += road-psg+mail/KAROSA
DIRS128VEHICLES += road-psg+mail/LaK
DIRS128VEHICLES += road-psg+mail/MERCEDES-BENZ
DIRS128VEHICLES += road-psg+mail/MAN
DIRS128VEHICLES += road-psg+mail/PRAGA
DIRS128VEHICLES += road-psg+mail/SKODA
DIRS128VEHICLES += road-psg+mail/SOR
DIRS128VEHICLES += road-trolleybus
DIRS128VEHICLES += trams

DIRS128 += $(addprefix vehicles/,$(DIRS128VEHICLES))

INFRASTRUCTURE128 :=
INFRASTRUCTURE128 += airport_buildings_towers
INFRASTRUCTURE128 += airport_depots
INFRASTRUCTURE128 += airport_ways_items
INFRASTRUCTURE128 += catenary_all
INFRASTRUCTURE128 += road_rail_crossings
INFRASTRUCTURE128 += road_ng_crossings
INFRASTRUCTURE128 += road_water_crossings
INFRASTRUCTURE128 += rail_water_crossings
INFRASTRUCTURE128 += ng_water_crossings
INFRASTRUCTURE128 += depots_rail_road_tram_ng
INFRASTRUCTURE128 += headquarters
INFRASTRUCTURE128 += ng_bridges
INFRASTRUCTURE128 += ng_signals
INFRASTRUCTURE128 += ng_stations
INFRASTRUCTURE128 += ng_tracks
INFRASTRUCTURE128 += ng_tunnels
INFRASTRUCTURE128 += rail_bridges
INFRASTRUCTURE128 += rail_signals
INFRASTRUCTURE128 += rail_stations
INFRASTRUCTURE128 += rail_tracks
INFRASTRUCTURE128 += rail_tunnels
INFRASTRUCTURE128 += road_bridges
INFRASTRUCTURE128 += road_signs
INFRASTRUCTURE128 += road_stops
INFRASTRUCTURE128 += road_tunnels
INFRASTRUCTURE128 += road_lights
INFRASTRUCTURE128 += roads
INFRASTRUCTURE128 += station_buildings
INFRASTRUCTURE128 += tram_signals
INFRASTRUCTURE128 += tram_stops
INFRASTRUCTURE128 += tram_tracks
INFRASTRUCTURE128 += water_all

ifdef EXTENDED
	INFRASTRUCTURE128 += signalboxes
	INFRASTRUCTURE128 += rail_signals/extended
	INFRASTRUCTURE128 += tram_signals/extended
else
	INFRASTRUCTURE128 += rail_signals/standard
	INFRASTRUCTURE128 += tram_signals/standard
endif

DIRS128 += $(addprefix infrastructure/,$(INFRASTRUCTURE128))

#these will get special treatment below
DIRGROUNDS:= base
DIRLOGO := base/misc_GUI

#other sizes for boats etc
DIRS160 := landscape/rivers

DIRS176 := vehicles/airplanes
DIRS176 += infrastructure/powerlines

DIRS250 := vehicles/ships-cargo
DIRS250 += vehicles/ships-ferries

DIRS := $(DIRS64) $(DIRS128) $(DIRS160) $(DIRS176) $(DIRS250) $(DIRLOGO) $(DIRGROUNDS)

#generating filenames
#with this function the filenames are assembled, by removing the dir, adding prefix
#and suffix
make_name = $(NAMEPREFIX)$(subst /,.,$(subst psg+mail,pax,$1))$(NAMESUFFIX).pak
#make_name = $(NAMEPREFIX)$(notdir $1)$(NAMESUFFIX)


.PHONY: $(DIRS) copy tar zip clean

all: copy $(DIRS)

archives: tar zip

tar: $(DESTFILE).tbz2
zip: $(DESTFILE).zip

$(DESTFILE).tbz2: $(PAKDIR)
	@echo "===> TAR $@"
	@tar cjf $@ $(PAKDIR)

$(DESTFILE).zip: $(PAKDIR)
	@echo "===> ZIP $@"
	@zip -rq $@ $(PAKDIR)

# Recursive copy cannot be used as it would copy .svn files too!
copy:
	@echo "===> COPY"
	@mkdir -p $(PAKDIR) $(PAKDIR)/config $(PAKDIR)/sound $(PAKDIR)/doc $(PAKDIR)/scenario $(PAKDIR)/sound $(PAKDIR)/text
	@cp -pt $(PAKDIR) $(CPFILES)/compat.tab
	@cp -pt $(PAKDIR) $(CPFILES)/demo.sve
	@cp -pt $(PAKDIR)/config $(CPFILES)/config/*
	@cp -pt $(PAKDIR)/sound $(CPFILES)/sound/*
	@cp -pt $(PAKDIR)/text $(CPFILES)/text/*

$(DIRS64):
	@echo "===> PAK64 $@"
	@mkdir -p $(PAKDIR)
	@$(MAKEOBJ) quiet PAK $(PAKDIR)/base.gui64.pak $@/ > /dev/null

$(DIRS128):
	@echo "===> PAK128 $@"
	@mkdir -p $(PAKDIR)
	@$(MAKEOBJ) PAK128 $(PAKDIR)/$(call make_name,$@) $@/ > /dev/null

$(DIRS160):
	@echo "===> PAK160 $@"
	@mkdir -p $(PAKDIR)
	@$(MAKEOBJ) quiet PAK160 $(PAKDIR)/$(call make_name,$@) $@/ > /dev/null

$(DIRS176):
	@echo "===> PAK176 $@"
	@mkdir -p $(PAKDIR)
	@$(MAKEOBJ) quiet PAK176 $(PAKDIR)/$(call make_name,$@) $@/ > /dev/null

$(DIRS250):
	@echo "===> PAK250 $@"
	@mkdir -p $(PAKDIR)
	@$(MAKEOBJ) quiet PAK250 $(PAKDIR)/$(call make_name,$@) $@/ > /dev/null

$(DIRGROUNDS):
	@echo "===> ground.Outside.pak"
	@mkdir -p $(PAKDIR)

	@$(MAKEOBJ) quiet PAK128 $(PAKDIR)/ground.Outside.pak $@/ > /dev/null

$(DIRLOGO):
	@echo "===> logo & misc gui"
	@mkdir -p $(PAKDIR)
	@$(MAKEOBJ) quiet PAK128 tmp.pak $@/ > /dev/null
	@$(MAKEOBJ) quiet EXTRACT tmp.pak > /dev/null
	@rm tmp.pak
	@mv symbol.BigLogo.pak $(PAKDIR)/
	@$(MAKEOBJ) quiet MERGE $(PAKDIR)/base.gui128.pak *.pak > /dev/null
	@rm *.pak

clean:
	@echo "===> CLEAN"
	@rm -fr $(PAKDIR) $(DESTFILE).tbz2 $(DESTFILE).zip

