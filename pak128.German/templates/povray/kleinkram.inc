// POV-Ray 3.7  include Datei "basis.inc" fuer PAK128.german  
// author: Daniel Martin Juli 2019
//Aktualisiert am: 08.09.2019 - DM
// homepage:
// email: daniel_martin@gmx.at
//----------------------------------------------------------------------------------------
#ifndef( kleinkram_INC_Temp)
#declare kleinkram_INC_Temp = version;
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
//======Ein grober Tisch by Friedrich Lohmüller

#macro Table(WideX,WideY,WideZ,TT,TFR)                        //------------------------------------- table --------<<<
#local D = 0.0001;
#local R = TFR;   // table foot half diameter          // TT = Dicke des senkrechten Kerns
#local FR = 0.02;  // frame radius
#local TableWood = texture{pigment{color rgb <0.85,0.6,0.4>*1.1 }
                           finish {ambient 0.15 diffuse 0.80 phong 1 reflection 0.05} }

#local TableFoot = box {<-R,0,-R>,< R,WideY-D,R>}


union{
union{
box {<-WideX/2+FR,-TT   ,-WideZ/2+FR>,< WideX/2-FR,0     ,WideZ/2-FR> translate<0,WideY,0>}
box {<-WideX/2   ,-TT+FR,-WideZ/2+FR>,< WideX/2   ,0-FR  ,WideZ/2-FR> translate<0,WideY,0>}
box {<-WideX/2+FR,-TT+FR,-WideZ/2   >,< WideX/2-FR,0-FR  ,WideZ/2   > translate<0,WideY,0>}
// edges vertical
cylinder {<-WideX/2+FR,WideY-TT+FR,-WideZ/2+FR>,<-WideX/2+FR,WideY-FR,-WideZ/2+FR>,FR }
cylinder {<-WideX/2+FR,WideY-TT+FR, WideZ/2-FR>,<-WideX/2+FR,WideY-FR, WideZ/2-FR>,FR }
cylinder {< WideX/2-FR,WideY-TT+FR,-WideZ/2+FR>,< WideX/2-FR,WideY-FR,-WideZ/2+FR>,FR }
cylinder {< WideX/2-FR,WideY-TT+FR, WideZ/2-FR>,< WideX/2-FR,WideY-FR, WideZ/2-FR>,FR }
// edges horizontal
cylinder {<-WideX/2+FR,WideY-TT+FR,-WideZ/2+FR>,< WideX/2-FR,WideY-TT+FR,-WideZ/2+FR>,FR }
cylinder {<-WideX/2+FR,WideY-TT+FR, WideZ/2-FR>,< WideX/2-FR,WideY-TT+FR, WideZ/2-FR>,FR }
cylinder {<-WideX/2+FR,WideY-TT+FR,-WideZ/2+FR>,<-WideX/2+FR,WideY-TT+FR, WideZ/2-FR>,FR }
cylinder {< WideX/2-FR,WideY-TT+FR,-WideZ/2+FR>,< WideX/2-FR,WideY-TT+FR, WideZ/2-FR>,FR }
cylinder {<-WideX/2+FR,WideY-FR,-WideZ/2+FR>,< WideX/2-FR,WideY-FR,-WideZ/2+FR>,FR }
cylinder {<-WideX/2+FR,WideY-FR, WideZ/2-FR>,< WideX/2-FR,WideY-FR, WideZ/2-FR>,FR }
cylinder {<-WideX/2+FR,WideY-FR,-WideZ/2+FR>,<-WideX/2+FR,WideY-FR, WideZ/2-FR>,FR }
cylinder {< WideX/2-FR,WideY-FR,-WideZ/2+FR>,< WideX/2-FR,WideY-FR, WideZ/2-FR>,FR }
sphere   {<-WideX/2+FR,WideY-TT+FR,-WideZ/2+FR>,FR }
sphere   {<-WideX/2+FR,WideY-TT+FR, WideZ/2-FR>,FR }
sphere   {< WideX/2-FR,WideY-TT+FR,-WideZ/2+FR>,FR }
sphere   {< WideX/2-FR,WideY-TT+FR, WideZ/2-FR>,FR }
sphere   {<-WideX/2+FR,WideY-FR,-WideZ/2+FR>,FR }
sphere   {<-WideX/2+FR,WideY-FR, WideZ/2-FR>,FR }
sphere   {< WideX/2-FR,WideY-FR,-WideZ/2+FR>,FR }
sphere   {< WideX/2-FR,WideY-FR, WideZ/2-FR>,FR }
     }

object{TableFoot  translate< WideX/2-2*R,0, WideZ/2-2*R>}
object{TableFoot  translate< WideX/2-2*R,0,-WideZ/2+2*R>}
object{TableFoot  translate<-WideX/2+2*R,0,-WideZ/2+2*R>}
object{TableFoot  translate<-WideX/2+2*R,0, WideZ/2-2*R>}
}
#end
#declare Waeschestange = union{
 cylinder{<0,0,0>,<0,7,0>,0.2} 
 cylinder{<0,0,0>,<0,7,0>,0.2 translate <11,0,0>}
 cylinder{<0,0,0>,<11,0,0>,0.05 translate<0,6.5,0> pigment{ color rgb 1}}
 texture{pigment{color rgb 0.2*0.5}}
 }
 #declare Regenwasserfass = union{
 cylinder{<0,0,0>,<0,6,0>,2}
 torus{2,.1 translate<0,6,0>}
 torus{2,.1 translate<0,3,0>}
 torus{2,.1 translate<0,0.1,0>}
 texture{pigment{color rgb .33*0.8}
normal{dents 1}
finish{specular 0.5}}}

#declare Bank1 = union{
box{<0,0,0>,<7,.1,.3> translate <0,1,0>}
box{<0,0,0>,<7,.1,.3> translate <0,1,.4>}
box{<0,0,0>,<7,.1,.3> translate <0,1,.8>}
box{<0,0,0>,<7,.3,.1> translate <0,1.4,1>}
box{<0,0,0>,<7,.3,.1> translate <0,1.8,1>}
box{<0,0,0>,<7,.3,.1> translate <0,2.2,1>}
//Träger
    union{
    box{<0,0,0>,<.5,1.2,.1> translate <1,1.1,1.2>}
    box{<0,0,0>,<.5,1.2,.1> translate <5.5,1.1,1.2>}
    box{<.2,0,0>,<.8,1,1.1> translate <.7,0,0>}
    box{<.2,0,0>,<.8,1,1.1> translate <5.2,0,0>}
    texture{T_Grnt1}}
texture {
    pigment {
        granite
        color_map {
            [0.00 rgb <0.17, 0.11, 0.05>]
            [0.60 rgb <0.17, 0.11, 0.08>]
            [0.60 rgb <0.42, 0.27, 0.14>]
            [0.65 rgb <0.42, 0.27, 0.14>]
            [0.65 rgb <0.28, 0.18, 0.09>]
            [1.00 rgb <0.22, 0.15, 0.08>]
        }
    }}}
#declare Bank2 = union{
box{<0,0,0>,<7,.1,.3> translate <0,1,0>}
box{<0,0,0>,<7,.1,.3> translate <0,1,.4>}
box{<0,0,0>,<7,.1,.3> translate <0,1,.8>}
box{<0,0,0>,<7,.3,.1> translate <0,1.4,1>}
box{<0,0,0>,<7,.3,.1> translate <0,1.8,1>}
box{<0,0,0>,<7,.3,.1> translate <0,2.2,1>}
//Träger
    union{
    box{<0,0,0>,<.5,1.2,.1> translate <1,1.1,1.2>}
    box{<0,0,0>,<.5,1.2,.1> translate <5.5,1.1,1.2>}
    cylinder{<0,0,0>,<0,0,1>,.5 translate <.7,.5,0> texture{T_Wood1 scale .3}}
    cylinder{<0,0,0>,<0,0,1>,.5 translate <6.2,.5,0> texture{T_Wood1 scale .3}}
    //box{<.2,0,0>,<.8,1,1.1> translate <.7,0,0>}
    //box{<.2,0,0>,<.8,1,1.1> translate <5.2,0,0>}
    texture{T_Grnt1}}
texture {pigment {
        granite
        color_map {
            [0.00 rgb .9*1.2]
            [0.60 rgb .9*1.2]
            [0.60 rgb <0.42, 0.42, 0.42>*1.4]
            [0.65 rgb <0.42, 0.42, 0.42>*1.4]
            [0.65 rgb .2*2]
            [1.00 rgb <0.22, 0.25, 0.25>*2]
        }
    }}}

 #declare Kiste_1 = union{
difference{    
box{<0,0,0>,<4,4,4>}
box{<0,0,0>,<6,3,3> translate<-1,.5,.5>}
box{<0,0,0>,<3,6,3> translate<.5,-1,.5>}
box{<0,0,0>,<3,3,6> translate<.5,.5,-1>}
}
box{<0.2,0.2,0.2>,<3.8,3.8,3.8>}
}
#declare Laterne_1 =union{
cone{<0,0,0>,0.5,<0,0.2,0>,0.3}
cylinder{<0,0.2,0>,<0,3,0>,0.3}
lathe{linear_spline
        5,
        <.3,0>,
        <.5,.4>,
        <.5,.8>,
        <.3,1.2>,
        <.3,0>
        translate<0,3,0> }
cylinder{<0,4.2,0>,<0,7,0>,.3}
union{
difference{
box{<0,0,0>,<.9,1.1,.9>}
box{<.1,.1,-.1>,<.8,1,1>}
box{<-.1,.1,.1>,<1,1,.8>}
translate<-.45,7,-.45> texture{pigment{color rgb <.17,.25,.31>*0.5}finish{F_MetalA}}
}
 box{<0,0,0>,<.7,1,.7> translate<-.35,7,-.35> texture{Glass} }       
}
texture {pigment {color rgb 0.2} finish{F_MetalC}}}
//============================
#version kleinkram_INC_Temp;
#end
