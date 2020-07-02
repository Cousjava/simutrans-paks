// POV-Ray 3.7  include Datei "basis.inc" fuer PAK128.german  
// author: Daniel Martin Juli 2019
//Aktualisiert am: 08.09.2019 - DM
// homepage:
// email: daniel_martin@gmx.at
//----------------------------------------------------------------------------------------
#ifndef( basis_INC_Temp)
#declare basis_INC_Temp = version;
#version 3.7;
#include "metals.inc"
#include "shapes.inc"
#declare STex = texture_map{[0 T_Wood12 scale 0.9]
													[0.5 T_Wood12 scale 0.9]
													[0.5 T_Wood22 scale 0.5]
													[0.95 T_Wood22 scale 0.5]
													[0.95 T_Copper_1A]
													[1 T_Brass_1A]
													}
#declare TTrans = texture {pigment{color Clear}}
#declare BT = texture {pigment {color rgb 0.2}}
#declare BalkonPfosten = texture_map{[0.0 T_Wood12][0.6 T_Wood26][0.7 T_Wood12][0.7 TTrans][0.9 BT][1.0 BT]}
#declare Blaetter_Herbst1 = 
 texture{ pigment{ color rgbf< .4,.11,.11,.3>*.8}   
          normal { bumps 0.15 scale 0.05 }
          finish {ambient 0.1 diffuse 0.9}
        }
#declare Blaetter_Herbst2 = 
 texture{ pigment{  color rgbf< .77,.38,.06, 0.4>*.85}   
          normal { bumps 0.15 scale 0.05 }
          finish {diffuse 0.8}
        }
//=======================================================
#declare bodenplatte = box { <-20,-0.1,-134>,<100,0,-15>}
//=====================================================
#macro Rohbau (laenge,hoehe,breite)
difference {
box {<0,0,0>,<laenge,hoehe,breite>}       
box {<0.5,0.5,0.5>,<laenge - 0.5,hoehe + 1,breite - 0.5> 
//pigment{checker color rgb 1*0.2 color rgb 1*0.8}
}
}
#end
//Fenster Aussparung
#declare Fenster_AS_5x5 = box {<0,0,0>,<5,5,1>}
#declare Fenster_AS_5x8 = box {<0,0,0>,<5,8.5,1>}
#declare Fenster_AS_6x6 = box {<0,0,0>,<6.5,6.5,1>}
#declare Fenster_AS_5x6 = box {<0,0,0>,<5,6,1>}
#declare Fenster_AS_6x5 = box {<0,0,0>,<6,5,1>}
#declare Fenster_AS_7x6 = box {<0,0,0>,<7,6,1>}
//====================================================
//Fenster
#declare Fenster4x5 = union {
box {<0.2,0.2,0.05>,<3.8,4.8,0.1> texture {NBoldglass}}
                difference {
                box {<0,0,0>,<4,5,0.2>}
                box {<0.2,0.2,-0.1>,<3.8,4.8,0.25>}
                }
                }
#declare Fenster5x5 = union {
box {<0.2,0.2,0.05>,<4.8,4.8,0.1> texture {Glass}}
                difference {
                box {<0,0,0>,<5,5,0.2>}
                box {<0.2,0.2,-0.1>,<4.8,4.8,0.25>}
                }
                box {<2.3,0,0>,<2.7,6,0.2>}
                box {<0,2.8,0>,<5,2.2,0.2>}
                }
#declare Fenster6x6_L = union {
box {<0.2,0.2,0.05>,<5.8,5.8,0.1> texture {NBglass}}
                difference {
                box {<0,0,0>,<6.5,6.5,0.2>}
                box {<0.2,0.2,-0.1>,<5.8,5.8,0.25>}
                }
                box {<0,3.2,0>,<6.5,3.3,0.2>}
                box {<3.2,0,0>,<3.3,6.5,0.2>}
box{<0,0,0>,<3.25,6.5,-0.1> rotate y*-2 translate <-3.25,0,-0.07> pigment{color PaleGreen*0.8}}
box{<3.25,0,0>,<6.5,6.5,-0.1> rotate y*2 translate <3.25,0,0> pigment{color PaleGreen*0.8}}
                texture{pigment{color rgb 1} normal{bozo 2 scale 0.075} finish{specular 0.1 roughness 0.05}}
                
                }
#declare Fenster6x6_L2 = union {
box {<0.2,0.2,0.05>,<6.3,6.3,0.1> texture {NBglass}}
                difference {
                box {<0,0,0>,<6.5,6.5,0.2>}
                box {<0.2,0.2,-0.1>,<6.3,6.3,0.25>}
                }
                box {<0,3.2,0>,<6.55,3.3,0.2>}
                box {<3.2,0,0>,<3.3,6.5,0.2>}
box{<0,0,0>,<3.25,6.5,-0.1> rotate y*-2 translate <-3.25,0,-0.07> pigment{color rgb <0.29,0.33,0.13>*0.5}}
box{<3.25,0,0>,<6.5,6.5,-0.1> rotate y*2 translate <3.25,0,0> pigment{color rgb <0.29,0.33,0.13>*0.5}}
                texture{pigment{color PaleGreen*0.3} normal{bozo 2 scale 0.075} finish{specular 0.1 roughness 0.05}}
                }
#declare Fenster6x6_RW = union{
difference{
box {<0,0,0>,<6.5,6.5,0.3>}
box {<0.2,0.2,-0.1>,<6.3,6.3,0.4>}
}
box {<3.1,0,0>,<3.4,6.5,0.3>}
box {<0.05,0.05,0.1>,<6.45,6.45,.15> texture{NBoldglass}}
//Zierstreifen
union{
box {<-.6,-.6,0>,<-0.05,6.5,0.1>}
box {<6.55,-.6,0>,<7.1,6.5,0.1>}
box {<-.6,-.6,0>,<7.1,-.05,0.1>}
box {<-.6,6.55,0>,<7.1,7.1,0.1>}
 translate <0,0,-0.2>
texture{pigment{color rgb <.98,.92,.84>}finish{phong 0.9}}}
//Blech
box {<0,0,0>,<6.5,0.08,-0.4> texture{T_Copper_2A}}
texture{pigment{color rgb 1*0.9}finish{specular 0.3 roughness 0.08}}
}
#declare Fenster5x6_RW = union{
difference{
box {<0,0,0>,<5,6,0.3>}
box {<0.2,0.2,-0.1>,<4.8,5.8,0.4>}
}
box {<2.35,0,0>,<2.65,6,0.3>}
box {<0.05,0.05,0.1>,<4.95,5.95,.15> texture{NBoldglass}}
//Zierstreifen
union{
box {<-.6,-.6,0>,<-0.05,6,0.1>}
box {<5.05,-.6,0>,<5.6,6,0.1>}
box {<-.6,-.6,0>,<5.6,-.05,0.1>}
box {<-.6,6.05,0>,<5.6,6.6,0.1>}
 translate <0,0,-0.2>
texture{pigment{color rgb <.98,.92,.84>}finish{phong 0.9}}}
//Blech
box {<0,0,0>,<5,0.08,-0.4> texture{T_Copper_2A}}
texture{pigment{color rgb 1*0.9}finish{specular 0.3 roughness 0.08}}
}
#declare Fenster5x6_L = union {
box {<0.05,0.05,0.1>,<4.95,5.95,.15> texture{NBoldglass}}
                difference {
                box {<0,0,0>,<5,6,0.3>}
				box {<0.2,0.2,-0.1>,<4.8,5.8,0.4>}
                }
                box {<2.35,0,0>,<2.65,6,0.2>}
box{<0,0,0>,<2.5,6,-0.1> rotate y*-2 translate <-2.5,0,-0.3> texture{pigment{color PaleGreen*0.8}normal{wood 1 scale 0.5}}}
box{<2.5,0,0>,<5,6,-0.1> rotate y*2 translate <2.5,0,0> texture{pigment{color PaleGreen*0.8}normal{wood 1 scale 0.5}}}
                }
//==========================================
#declare Fenster5x8_5 = union {
box {<0.2,0.2,0.05>,<4.8,8.3,0.1> texture {Glass}}
                difference {
                box {<0,0,0>,<5,8.5,0.2>}
                box {<0.2,0.2,-0.1>,<4.8,8.3,0.25>}
                }
                box {<0,6.2,0>,<5,6.4,0.2>}
                box {<2.4,0,0>,<2.6,6.4,0.2>}
                texture{pigment{color MediumSeaGreen} scale 0.08}
                }
//Fenster mit Fensterladen
#declare Fenster5x8_5_L = union {
box {<0.2,0.2,0.05>,<4.8,8.3,0.1> texture {Glass}}
                difference {
                box {<0,0,0>,<5,8.5,0.2>}
                box {<0.2,0.2,-0.1>,<4.8,8.3,0.25>}
                }
                box {<0,6.2,0>,<5,6.4,0.2>}
                box {<2.4,0,0>,<2.6,6.4,0.2>}
box{<0,0,0>,<2.5,8.5,-0.1> rotate y*-2 translate <-2.5,0,0>}
box{<2.5,0,0>,<5,8.5,-0.1> rotate y*2 translate <2.5,0,0>}
                texture{pigment{color MediumSeaGreen} scale 0.08}
                }
#declare Fenster7x6 = union {
box {<0.2,0.2,0.05>,<6.8,5.8,0.1> texture {Glass}}
                difference {
                box {<0,0,0>,<7,6,0.2>}
                box {<0.2,0.2,-0.1>,<6.8,5.8,0.25>}
                }
                box {<3.3,0,0>,<3.7,6,0.2>}
                box {<0,2.8,0>,<7,3.2,0.2>}
                }
#declare Fenster5x5L = union{
object{ Supertorus(
  3.4, 0.2,// R_Major, R_Minor,
  2.00, 0.55,// Maj_Control, Min_Control,
  0.001,1.50)
   rotate y*45}
box{<-.15,.1,-2.4>,<.15,.2,2.4>}
box{<-2.5,-.1,-2.4>,<2.5,-.2,2.4> texture{Glass}}
union{
box{<-2.5,-.1,-2.4>,<-5,-.2,2.4>}
box{<-2.5,-.1,-1.4>,<-5,-.05,-1.5>}
box{<-2.5,-.1,1.4>,<-5,-.05,1.5>}
 translate<0,.4,0>
pigment{ color rgb<1,0.65,0>*.4}
}
union{
box{<2.5,-.1,-2.4>,<5,-.2,2.4>}
box{<2.5,-.1,-1.4>,<5,-.05,-1.5>}
box{<2.5,-.1,1.4>,<5,-.05,1.5>}
 translate<0,.3,0>
pigment{ color rgb<1,0.65,0>*.4}
}
texture{pigment{ color rgb<1,0.65,0>*.9} normal{bozo 1 scale 0.3} finish { phong 1}}
}
//Fenster mit Sims
#declare Fenster5x8_5Z = union {
box {<0.2,0.2,0.05>,<4.8,8.3,0.1> texture {Glass}}
                difference {
                box {<0,0,0>,<5,8.5,0.2> }
                box {<0.2,0.2,-0.1>,<4.8,8.3,0.25>}
                texture{T_Wood12 scale 0.08}
                }
                box {<0,6.2,0>,<5,6.4,0.2> texture{T_Wood12 scale 0.08}}
                box {<2.4,0,0>,<2.6,6.4,0.2> texture{T_Wood12 scale 0.08}}
                box {<-0.4,-0.15,-0.4>,<5.4,0,0> texture{T_Brass_1B}}
                box {<-0.3,-0.3,-0.3>,<5.3,-0.15,0> pigment{color NewTan*0.6}}
                union{
difference{
union{
cylinder{<-0.4,0,0>,<5.4,0,0>,0.2 translate<0,8.9,0>}
cylinder{<-0.4,0,0>,<5.4,0,0>,0.2 translate<0,8.5,0>}
cylinder{<0,0,-0.15>,<0,0,0>,0.4 translate<-0.4,4,0>}
cylinder{<0,0,-0.15>,<0,0,0>,0.4 translate<5.4,4,0>}
box{<-0.4,11.8,-0.2>,<5.4,12,0>}//sims oben
box{<-0.4,12,-0.35>,<5.4,12.3,0>}//sims oben 3
box{<-0.4,12.3,-0.4>,<5.4,12.35,0> texture{T_Brass_1B}}//sims blech
box{<-0.4,10,-0.1>,<5.4,12,0>}
box{<-0.4,4,-0.1>,<5.4,8.9,0> }
cylinder{<0,0,-0.1>,<0,0,0>,0.4 translate<0,10,0>}
cylinder{<0,0,-0.1>,<0,0,0>,0.4 translate<5,10,0>}
}
box{<0,0,-0.2>,<5,8.5,0.2>}
cylinder{<0,0,-0.2>,<0,0,0.1>,1 translate<1.25,10,0> scale <2,1,1>}
}
cone{<0,10.2,0>,0.75,<0,8.9,0>,0.45 scale <1.3,1,0.5> translate<2.5,0,-0.1>}
cone{<0,10.6,0>,0.7,<0,8.9,0>,0.4 translate<2.5,0,0>}
pigment{color NewTan*0.7}}
                }
//Haustuerloch
#declare Haust_AS_7k5_11 = box{<0,0,0>,<7.5,11,1>}
#declare Haust_AS_7_11 = box{<0,0,0>,<7,11,1>}
#declare Haust_AS_6_10 = box{<0,0,0>,<6,10,1>}
//=====Türen=====================================================================
#declare Haustuer_7k5_11 = union{
difference {
box{<0,0,0>,<7.5,11,0.2>}
box{<0.9,0.9,-0.1>,<6.6,10.1,0.3>}
}
box{<1.85,0,0>,<2.15,11,0.2>}
box{<3.05,0,0>,<3.35,11,0.2>}
box{<4.25,0,0>,<4.55,11,0.2>}
box{<5.45,0,0>,<5.75,11,0.2>}
box{<0.3,0.9,0.05>,<7.2,10.1,0.15> texture{NBwinebottle}}
sphere{<0,0,0>,0.2 translate <0.4,5,0> texture{T_Chrome_1A}}
}
#declare Haustuer_7_11 = union{
 difference{
 box{<0,0,0>,<7,11,0.2>}
 intersection{
 sphere{<0,0,0>,1 translate <3,8,0.1>}
 sphere{<1.1,0,0>,1 translate <3,8,0.1>}
 }
 texture{pigment{image_map{tga "parkett_braun_alt.tga" map_type 0 interpolate 3}} scale 2.5}}
 box{<-.2,0,-.1>,<.1,11,0>}
 box{<6.9,0,-.1>,<7.2,11,0>}
 box{<-.2,10.9,-.1>,<7.2,11.2,0>}
 sphere {<1,5,0>,0.3 texture{T_Brass_1A}}
 texture{T_Wood22 scale 0.5}}
 #declare Haustuer_6_10 = union{
 difference{
 box{<0,0,0>,<6,10,0.2>}
 intersection{
 sphere{<0,0,0>,1 translate <3,8,0.1>}
 sphere{<1.1,0,0>,1 translate <3,8,0.1>}
 }
 texture{pigment{image_map{tga "parkett_braun_alt.tga" map_type 0 interpolate 3}} scale 2.5}}
 box{<-.2,0,-.1>,<.1,10,0>}
 box{<5.9,0,-.1>,<6.2,10,0>}
 box{<-.2,10.9,-.1>,<6.2,10.2,0>}
 sphere {<1,5,0>,0.3 texture{T_Brass_1A}}
 texture{T_Wood22 scale 0.5}}
//Kamine=========================================================================
#declare Kamin_1 = difference{
union {
box {<0,0,0>,<2,7,2>}
box {<-0.2,6.9,-0.2>,<2.2,7.2,2.2> texture {T_Stone31 scale 0.5}}
texture{pigment{brick 
                White*0.5
                Scarlet*0.3
                brick_size <0.8,0.3,0.3>
                mortar 0.075}
                normal {wrinkles 0.75}
                }
}
box{<0.3,2,0.3>,<1.7,7.5,1.7> pigment {color rgb 0}}}

#declare Kamin_2 = difference{
union {
box {<0,0,0>,<4,7,2>}
box {<-0.2,6.9,-0.2>,<4.2,7.2,2.2> texture {T_Stone31 scale 0.5}}
texture{pigment{brick 
                White*0.5
                Scarlet*0.3
                brick_size <0.9,0.4,0.5>
                mortar 0.085}
                normal {wrinkles 0.75}
                }
}
box{<0.3,2,0.3>,<1.7,7.5,1.7> pigment {color rgb 0}}
box{<2.3,2,0.3>,<3.7,7.5,1.7> pigment {color rgb 0}}
}

#declare Kamin_3 = difference{
union {
box {<0,0,0>,<12,7,3>}
box {<-0.4,6.5,-0.4>,<12.4,7.2,3.4> texture {T_Stone31 scale 0.5}}
texture{pigment{brick 
                White*0.5
                Scarlet*0.3
                brick_size <2,0.75,0.65>
                mortar 0.15}
                normal {wrinkles 0.75}
                }
}
box{<0.3,2,0.3>,<3.3,7.5,2.7> pigment {color rgb 0}}
box{<3.6,2,0.3>,<6.6,7.5,2.7> pigment {color rgb 0}}
box{<6.9,2,0.3>,<11.7,7.5,2.7> pigment {color rgb 0}}
}
//========================================================
#declare schnee = height_field{ png "schneefeld.png" smooth
              texture{
                      pigment{ color rgb <1,1,1>*1.5}
                      finish {specular 0.9 roughness 0.05}
}
} 
//=========================================================

#macro Dachziegelengobiert (Laenge)
#declare WDZ_Segment = difference {cylinder{<0,0,0>,<0,Laenge,0>,0.5}cylinder{<0,-0.1,0>,<0,Laenge + 0.1,0>,0.4}box{<-0.6,-0.1,0>,<0.6,Laenge + 0.1,0.6>}}
union {
object{WDZ_Segment translate <0,0,0>}
object{WDZ_Segment rotate <0,180,0> translate <0.9,0,0>}
object{WDZ_Segment translate <1.8,0,0>}
}
#end
//Eine sehr sehr einfache Brunnenatrappe
#declare Brunnen =
difference {
union{
cylinder{<0,0,0>,<0,7,0>, 4}
cylinder{<0,7,0>,<0,7.7,0>,4.3}
cylinder{<0,7.7,0>,<0,9.7,0>,0.3 translate<-3.5,0,0> texture{T_Wood15}}
cylinder{<0,7.7,0>,<0,9.7,0>,0.3 translate<3.5,0,0> texture{T_Wood15}}
cone{<0,0,0>,4,<0,3,0>,.5 open pigment{brick color rgb 0.2 Scarlet*0.4 brick_size <0.5,1,0.5> mortar 0.05} translate<0,9.7,0>}
texture{T_Grnt19 normal{granite}} texture{pigment{brick 
                White*0.1
                Clear
                brick_size <1.5,1,.5>
                mortar 0.1
                warp{ cylindrical
        orientation <0,0,1>
        dist_exp 1
      }}
                normal {wrinkles 0.75}}
                }
cylinder{<0,0.5,0>,<0,7.8,0>,3 texture { T_Grnt16 normal { agate 0.35 scale 0.05}}}}

#declare holzstoss_basis = union{cylinder{<0,0,0>,<0,0,1>,0.2}cylinder{<0,0,0>,<0,0,1>,0.2 translate<0.4,0,0>}cylinder{<0,0,0>,<0,0,1>,0.2 translate<0.8,0,0>}cylinder{<0,0,0>,<0,0,1>,0.2 translate<0.2,0.4,0>}cylinder{<0,0,0>,<0,0,1>,0.2 translate<0.6,0.4,0>}cylinder{<0,0,0>,<0,0,1>,0.2 translate<1,0.4,0>} texture{T_Wood1}}

 #declare Zaunpfosten_Ziegel = union{box{<0,0,0>,<1,5,1>}box{<-0.2,5,-0.2>,<1.2,5.2,1.2>} texture{pigment{brick 
																																												color rgb 0.2
																																												color Scarlet*0.4
																																												brick_size <0.5,0.2,0.5>
																																												mortar 0.08}
																																												normal{agate 1 turbulence 0.5 scale 0.2}}}
#declare Zaunlatte_rund = union{
cylinder{<0,0,0>,<0,3.8,0>,0.1}
cone{<0,3.8,0>,0.2,<0,4.5,0>,0}
texture{T_Chrome_1A}
}
#declare heckentex = texture {
pigment
 {granite
  turbulence 0.0
  color_map
   {[0.0, 0.07 color rgb 1 color rgb .9]
    [0.07, 0.2 color rgb .5 color rgb .9]
    [0.2, 0.3 color rgb .6 color Clear]
    [0.3, 0.7 color Clear color Clear]
    [0.7, 1.0 color rgb .6 color rgb .5]
   }
   }finish {crand 0.02}}
  
#declare Plumpsklo =union{
difference{
union{
      box{<0,0,0>,<0.1,11,7>}
      box{<0,0,0>,<0.1,11,7> translate x*7}
      box{<0,0,6.9>,<7.1,10,7>}
      box{<0,0,0>,<7.1,10,0.1> rotate y*8}
		}
box{<-.05,0,-.1>,<7.15,2,7.15> rotate x*8 translate<0,10,0>}
				}
box{<0,0,0>,<7.1,0.1,8> rotate x*8 translate<0,10,-0.7>}
}
#macro DachRinneWaagrecht (Laenge,Durchmesser) difference{cylinder {<0,0,0>,<Laenge,0,0>,Durchmesser}cylinder {<0,0,0>,<Laenge-0.2,0,0>,Durchmesser-0.05 translate <0.1,0.2,0>} texture {T_Brass_1A}}
#end
#declare Hasenbox1 =union{
difference{
box{<0,0,-20>,<10,4,-22>}
box{<-1,4,-19.5>,<11,8,-23> rotate x*7 translate <0,-1,0>}
 texture{gradient x texture_map{BalkonPfosten}}}
box{<-1.5,4,-19.5>,<10.5,4.4,-23> rotate x*7 translate <0,-3,0> texture{gradient x texture_map{
//[0.0 T_Stone17][0.7 T_Stone17][0.7 T_Stone8][0.9 BT][1.0 BT]}}}
[0.0 T_Wood2][0.7 T_Wood2][0.7 T_Wood15][0.9 BT][1.0 BT]}}}
}
#declare Blumenkasten_Placebo =union{difference{box{<0,0,0>,<2,1.5,8>}box{<0.1,0.1,0.1>,<1.8,1.6,7.8>}texture{T_Grnt17 scale 0.5}}
 box{<0.1,1,0.1>,<1.8,1.5,7.9> pigment{image_map{png "Misthaufentextur.png" map_type 0 interpolate 2}}}
 sphere{<0,0,0>,0.4 translate<0,1.5,0.5> texture{ pigment{  color rgbf< .11,.19,0, 0.4>*1.05}   
          normal { bumps 0.15 scale 0.05 }
          finish {phong 0.9}
        }}
 sphere{<0,0,0>,0.4 texture{ pigment{  color rgbf< .11,.19,0, 0.4>*1.05}   
          normal { bumps 0.15 scale 0.05 }
          finish {phong 0.9}
        } translate<.6,1.5,.5>}
 }
//============================
#version basis_INC_Temp;
#end
