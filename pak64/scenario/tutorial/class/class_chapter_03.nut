//
/*
 *  class chapter_03
 *
 *
 *  Can NOT be used in network game !
 */

//Step 5 =====================================================================================
ch3_cov_lim1 <- {a = 4, b = 6}

//Step 7 =====================================================================================
ch3_cov_lim2 <- {a = 5, b = 7}

//Step 11 =====================================================================================
ch3_cov_lim3 <- {a = 6, b = 10}


//----------------Para las señales de paso------------------------
persistent.sigcoord <- [{x=null, y=null}]
sigcoord  <- [{x=null, y=null}]
persistent.signal <- [0,0,0,0,0,0,0,0,0,0,0,0,0,0]
gsignal <- [0,0,0,0,0,0,0,0,0,0,0,0,0,0]
persistent.point <- [0,0,0,0,0,0,0,0,0,0,0]
point <- [0,0,0,0,0,0,0,0,0]

signal <-	[	{coor=coord3d(94,197,6), ribi=8}, {coor=coord3d(112,198,2), ribi=2}, 
				{coor=coord3d(121,199,0), ribi=1}, {coor=coord3d(120,263,3), ribi=4},
				{coor=coord3d(121,271,3), ribi=1}, {coor=coord3d(120,324,5), ribi=4},
				{coor=coord3d(121,331,5), ribi=1}, {coor=coord3d(120,377,9), ribi=4}, 
			]
//----------------------------------------------------------------

wayend <- 0
coorbord <- 0
reached <- 0

cursor_count <- 0

class tutorial.chapter_03 extends basic_chapter
{
	chapter_name  = "Riding the Rails"
	chapter_coord = coord(93,153)
	startcash     = 50000000	   				// pl=0 startcash; 0=no reset
	comm_script = false

	gl_wt = wt_rail

	cy1 = {c = coord(111,184), name = ""}
	cy2 = {c = coord(52,194), name = ""}
	cy3 = {c = coord(115,268), name = ""}
	cy4 = {c = coord(124,326), name = ""}
	cy5 = {c = coord(125,378), name = ""}

	//Step 1 =====================================================================================
	//Productor
	f1name = "Nutzwald"
	f1_coord = coord(123,160)
	f1_lim = {a = coord(123,160), b = coord(125,162)}
	
	//Fabrica
	f2name = "SaegewerkMHz"
	f2_coord = coord(93,153)
	f2_lim = {a = coord(93,153), b = coord(94,154)}

	//Step 2 =====================================================================================
	//Primer tramo de rieles
	//--------------------------------------------------------------------------------------------
	st1_way_lim = {a = coord(120,163), b = coord(125,163)}		//Limites de la via para la estacion
	bord1_lim = {a = coord(106,154), b = coord(120,167)}		//Marca area con "X"
	label1_lim = coord(120,163)									//Indica el final de un tramo
	c_way1 = {a = coord3d(125,163,0), b = coord3d(107,158,0)}	//Inicio y Fin de la via (fullway)

	//Estaciones del Productor
	st1_list = [coord(125,163), coord(124,163), coord(123,163)]
	//--------------------------------------------------------------------------------------------
	
	//Para el puente
	//------------------------------------------------------------------------------------------
	c_bway_lim1 = {b = coord(107,158), a = coord(102,158)}
	c_brge1 = {b = coord(106,158), a = coord(103,158)}
	brge1_z = -1
	//-------------------------------------------------------------------------------------------	

	//Segundo tramo de rieles
	//--------------------------------------------------------------------------------------------
	st2_way_lim = {a = coord(96,151), b = coord(96,155)}		//Limites de la via para la estacion
	bord2_lim = {a = coord(95,155), b = coord(103,160)}			//Marca area con "X"
	label2_lim = coord(96,155)									//Indica el final de un tramo
	c_way3 = {a = coord3d(106,158,-1), b = coord3d(96,151,1)}	//Inicio y Fin de la via (fullway)

	//Estaciones de la Fabrica
	st2_list = [coord(96,151), coord(96,152), coord(96,153)]
	//--------------------------------------------------------------------------------------------

	//Step 4 =====================================================================================
	//Limites del deposito y rieles
	//--------------------------------------------------------------------------------------------
	c_dep1 = coord(121,164)
	c_dep1_lim = {b = coord(121,164), a = coord(121,163)}
	//--------------------------------------------------------------------------------------------

	//Step 5 =====================================================================================
	loc1_name_obj = "1Diesellokomotive"
	loc1_tile = 3
	loc1_load = 100
	loc1_wait = 0
	f1_reached = 60
	f1_good = good_alias.wood

	//Step 6 =====================================================================================
	c_lock = [coord(60,10), coord(77,25)]  //Futuros transformadores

	//Primer tramo de rieles
	//--------------------------------------------------------------------------------------------
	st3_way_lim = {a = coord(94,155), b = coord(94,160)}		//Limites de la via para la estacion
	bord3_lim = {a = coord(91,160), b = coord(96,174)}			//Marca area con "X"
	label3_lim = coord(94,160)									//Indica el final de un tramo
	c_way4 = {a = coord3d(94,155,2), b = coord3d(96,172,3)}		//Inicio y Fin de la via (fullway)

	//Estaciones de la Fabrica
	st3_list = [coord(94,155), coord(94,156), coord(94,157)]
	//--------------------------------------------------------------------------------------------	

	//Para el tunel
	//------------------------------------------------------------------------------------------
	c_tway_lim2 = {a = coord(96,172), b = coord(104,172)}
	c_tunn1 = {a = coord3d(96,172,3), b = coord3d(104,172,3)}	//Inicio y Fin de la via (fullway)
	brge2_z = 1
	//-------------------------------------------------------------------------------------------

	//Segundo tramo de rieles
	//--------------------------------------------------------------------------------------------
	st4_way_lim = {a = coord(109,186), b = coord(109,189)}		//Limites de la via para la estacion
	bord4_lim = {a = coord(102,171), b = coord(110,186)}		//Marca area con "X"
	label4_lim = coord(109,186)									//Indica el final de un tramo
	c_way5 = {a = coord3d(104,172,3), b = coord3d(109,189,2)}	//Inicio y Fin de la via (fullway)

	//Estaciones del Consumidor
	st4_list = [coord(109,189), coord(109,188), coord(109,187)]
	//--------------------------------------------------------------------------------------------

	//Step 7 =====================================================================================
	//Limites del deposito y rieles
	//--------------------------------------------------------------------------------------------
	c_dep2 = coord(93,158)
	c_dep2_lim = {b = coord(94,158), a = coord(93,158)}
	//--------------------------------------------------------------------------------------------

	loc2_name_obj = "1Diesellokomotive"
	loc2_tile = 3
	loc2_load = 100
	loc2_wait = 0

	//Consumidor Final
	f3name = "Materialswholesale"
	f3_coord = coord(110,190)
	f3_reached = 30
	f3_good = good_alias.plan

	//Step 8 =====================================================================================
	//Tramo de via para el tunel
	c_way6_lim = {a = coord(93,198), b = coord(114,198)}
	c_way6 = {a = coord3d(114,198,0), b = coord3d(93,198,5)}		//Inicio y Fin de la via (fullway)
	//------------------------------------------------------------------------------------------

	//Para el puente
	//------------------------------------------------------------------------------------------
	c_bway_lim3 = {a = coord(90,198), b =  coord(94,198)}
	c_brge3 = {a = coord(93,198), b = coord(91,198)}
	brge3_z = 5
	//-------------------------------------------------------------------------------------------

	//Para la entrada del tunel
	//------------------------------------------------------------------------------------------
	start_tunn = coord(90,198)
	end_tunn = coord(89,198)
	c_tun_lock = coord(88,198)
	//------------------------------------------------------------------------------------------

	//Subterraneo
	//------------------------------------------------------------------------------------------
	c_tunn2_lim = {b = coord(91,194), a = coord(63,202)}
	c_tunn2 = {a = coord3d(90,198,6), b = coord3d(63,198,8)}		//Inicio y Fin de la via (fullway)

	c_lock_tunn = [coord(65,199),coord(65,197)]

	layer_lvl = 6 
	start_lvl_z = 6
	end_lvl_z = 8
	c_tun_list = [coord3d(65,198,6), coord3d(64,198,7), coord3d(63,198,8)]
	//------------------------------------------------------------------------------------------

	//Step 9 =====================================================================================
	c_way_list1 = 	[	{a = coord3d(120,199,0), b = coord3d(120,264,3) }, {a = coord3d(121,264,3), b = coord3d(121,199,0) }, 
						{a = coord3d(120,271,3), b = coord3d(120,324,5) }, {a = coord3d(121,324,5), b = coord3d(121,271,3) },
						{a = coord3d(120,331,5), b = coord3d(120,377,9) }, {a = coord3d(121,377,9), b = coord3d(121,331,5) }
					]

	c_way_lim1 =	[	{a = coord(120,199), b = coord(120,264) }, {b = coord(121,264), a = coord(121,199) }, 
						{a = coord(120,271), b = coord(120,324) }, {b = coord(121,324), a = coord(121,271) },
						{a = coord(120,331), b = coord(120,377) }, {b = coord(121,377), a = coord(121,331) }
					]
	
	signr = signal.len()

	//Step 10 =====================================================================================
	dir_list = [0,5,2,6,5,2,2,2]
	c_cate_list1 =	[	{a = coord3d(55,198,11), b = coord3d(120,198,0) }, {a = coord3d(120,198,0), b = coord3d(120,383,9) }, 
						{a = coord3d(121,383,9), b = coord3d(121,197,0)}, {a = coord3d(120,197,0), b = coord3d(55,197,11)}
					]
	c_cate_lim1 =	[	{a = coord3d(55,198,11), b = coord3d(120,198,0) }, {a = coord3d(120,198,0), b = coord3d(120,383,9) }, 
						{b = coord3d(121,383,9), a = coord3d(121,197,0)}, {b = coord3d(120,197,0), a = coord3d(55,197,11)}
					]

	//Limites del deposito y rieles
	//--------------------------------------------------------------------------------------------
	c_dep3 = coord(108,196)
	c_dep3_lim = {a = coord(108,196), b = coord(108,197)}
	//--------------------------------------------------------------------------------------------

	//Step 11 =====================================================================================
	loc3_name_obj = "NS1000"
	loc3_tile = 4
	loc3_load = 100
	loc3_wait = 25369

    line1_name = "Test 4"
	st_lim_a =	[	{a = coord(55,197), b = coord(58,197)}, {a = coord(116,198), b = coord(119,198)},
					{a = coord(120,266), b= coord(120,269)}, {a = coord(120,326), b= coord(120,329)},
					{a = coord(120,380), b= coord(120,383)}, {a = coord(121,326), b= coord(121,329)}, 
					{a = coord(121,266), b= coord(121,269)}, {a = coord(116,197), b = coord(119,197)}
				]
	sch_list = [coord(55,198), coord(116,198), coord(120,266), coord(120,326), coord(120,380), coord(120,326), coord(120,266), coord(116,198)]
	d3_cnr = 3
	tmp_d3_cnr = 0

	//Script
	//----------------------------------------------------------------------------------
	sc_way_name = "concrete_sleeper_track"
	sc_tunn_name = "RailTunnel"
	sc_bridge_name = "ClassicRail"
	sc_station_name = "FreightTrainStop"
	sc_dep_name = "TrainDepot"
	sc_veh1_name = "Holzwagen"
	sc_wag1_nr = 5
	sc_veh2_name = "Holzwagen"
	sc_wag2_nr = 5
	sc_wag3_name = "Passagierwagen"
	sc_wag3_nr = 7
	sc_sign_name = "Signals"
	sc_caten_name = "SlowOverheadpower"
	//------------------------------------------------------------------------------------

	function start_chapter(){  //Inicia solo una vez por capitulo
		rules.clear()
		set_all_rules(0)

		cy1.name = get_city_name(cy1.c)
		cy2.name = get_city_name(cy2.c)
		cy3.name = get_city_name(cy3.c)
		cy4.name = get_city_name(cy4.c)
		cy5.name = get_city_name(cy5.c)

		local pl = 0
		if(this.step == 11){
            local c_dep = this.my_tile(c_dep3)
			local c_list = sch_list
			start_sch_tmpsw(pl,c_dep, c_list)
		}
	}

	function set_goal_text(text){

		switch (this.step) {
			case 1:
				if (pot0==0){
					text = ttextfile("chapter_03/01_1-2.txt")
					text.tx = ttext("<em>[1/2]</em>")
				}
				else {
					text = ttextfile("chapter_03/01_2-2.txt")
					text.tx = ttext("<em>[2/2]</em>")
				}
				break
			case 2:
				local c1 = coord(c_way1.a.x, c_way1.a.y)
				local c2 = coord(c_way1.b.x, c_way1.b.y)
				local c3 = coord(c_way3.a.x, c_way3.a.y)
				local c4 = coord(c_way3.b.x, c_way3.b.y)

				if (pot0==0){
					text = ttextfile("chapter_03/02_1-3.txt")
					text.tx = ttext("<em>[1/3]</em>")
				}
				else if (pot1==0){
					text = ttextfile("chapter_03/02_2-3.txt")
					text.tx = ttext("<em>[2/3]</em>")
				}
				else if (pot2==0){
					text = ttextfile("chapter_03/02_3-3.txt")
					text.tx = ttext("<em>[3/3]</em>")
				}
				text.br = c_brge1.a.href("("+c_brge1.a.tostring()+")")	
				text.w1 = c1.href("("+c1.tostring()+")")
				text.w2 = c2.href("("+c2.tostring()+")")
				text.w3 = c3.href("("+c3.tostring()+")")
				text.w4 = c4.href("("+c4.tostring()+")")

				if (coorbord==0)
					text.cbor = "<em>" + translate("Ok") + "</em>"
				else
					text.cbor = coord(coorbord.x, coorbord.y).href("("+coorbord.tostring()+")")

				break
			case 3:
				if (pot0==0){
					text = ttextfile("chapter_03/03_1-2.txt")
					text.tx = ttext("<em>[1/2]</em>")
				}
				else if (pot0==1&&pot1==0){
					text = ttextfile("chapter_03/03_2-2.txt")
					text.tx = ttext("<em>[2/2]</em>")
				}
				text.tile = loc1_tile
				break
			case 4:
				if (pot0==0){
					text = ttextfile("chapter_03/04_1-3.txt")
					text.tx=ttext("<em>[1/3]</em>")
				}
				else if (pot0==1&&pot1==0){
					text = ttextfile("chapter_03/04_2-3.txt")
					text.tx=ttext("<em>[2/3]</em>")
				}
				else if (pot1==1){
					text = ttextfile("chapter_03/04_3-3.txt")
					text.tx=ttext("<em>[3/3]</em>")
				}
				text.w1 = c_dep1_lim.a.href("("+c_dep1_lim.a.tostring()+")")
				text.w2 = c_dep1_lim.b.href("("+c_dep1_lim.b.tostring()+")")
				text.dep = c_dep1.href("("+c_dep1.tostring()+")")
				break
			case 5:
				text.reached = reached
				text.t_reach = f1_reached
				text.loc1 = translate(loc1_name_obj)
				text.wag = sc_wag1_nr
				text.tile = loc1_tile
				text.load = loc1_load
				text.wait = get_wait_time_text(loc1_wait)
				break
			case 6:
				local c1 = coord(c_way4.a.x, c_way4.a.y)
				local c2 = coord(c_way4.b.x, c_way4.b.y)
				local c3 = coord(c_way5.a.x, c_way5.a.y)
				local c4 = coord(c_way5.b.x, c_way5.b.y)
				if (pot0==0){
					text = ttextfile("chapter_03/06_1-5.txt")
					text.tx=ttext("<em>[1/5]</em>")
				}
				else if (pot1==0){
					text = ttextfile("chapter_03/06_2-5.txt")
					text.tx=ttext("<em>[2/5]</em>")
				}
				else if (pot2==0){
					text = ttextfile("chapter_03/06_3-5.txt")
					text.tx=ttext("<em>[3/5]</em>")
				}
				else if (glsw[0]==0){
					text = ttextfile("chapter_03/06_4-5.txt")
					text.tx=ttext("<em>[4/5]</em>")
				}
				else if (glsw[1]==0){
					text = ttextfile("chapter_03/06_5-5.txt")
					text.tx = ttext("<em>[5/5]</em>")
				}
				text.tu = c_tway_lim2.a.href("("+c_tway_lim2.a.tostring()+")")
				text.w1 = c1.href("("+c1.tostring()+")")
				text.w2 = c2.href("("+c2.tostring()+")")
				text.w3 = c3.href("("+c3.tostring()+")")
				text.w4 = c4.href("("+c4.tostring()+")")
				text.tile = loc2_tile
				break
			case 7:
				text.reached = reached
				text.t_reach = f3_reached
				text.loc2 = translate(loc2_name_obj)
				text.wag = sc_wag2_nr
				text.tile = loc2_tile
				text.load = loc2_load
				text.wait = get_wait_time_text(loc2_wait)
				text.w1 = c_dep2_lim.a.href("("+c_dep2_lim.a.tostring()+")")
				text.w2 = c_dep2_lim.b.href("("+c_dep2_lim.b.tostring()+")")
				break
			case 8:	

				if(pot0==0){
					text = ttextfile("chapter_03/08_1-5.txt")
					text.tx = ttext("<em>[1/5]</em>")
					text.w1 = c_way6_lim.b.href("("+c_way6_lim.b.tostring()+")")
					text.w2 = c_way6_lim.a.href("("+c_way6_lim.a.tostring()+")")
				}
				else if(pot1==0){
					text = ttextfile("chapter_03/08_2-5.txt")
					text.tx = ttext("<em>[2/5]</em>")
					text.br = c_brge3.a.href("("+c_brge3.a.tostring()+")")	
				}
				else if (pot2==0){
					text = ttextfile("chapter_03/08_3-5.txt")
					text.tx = ttext("<em>[3/5]</em>")
					text.t1 = "<a href=\"("+ start_tunn.x+","+ start_tunn.y+")\">("+ start_tunn.tostring()+")</a>"
				}
				else if(pot3==0){
					text = ttextfile("chapter_03/08_4-5.txt")
					text.tx = ttext("<em>[4/5]</em>")
					text.lev = layer_lvl
					text.t1 = "<a href=\"("+ start_tunn.x+","+ start_tunn.y+")\">("+ start_tunn.tostring()+")</a>"
					text.t2 = "<a href=\"("+ c_tun_list[0].x+","+ c_tun_list[0].y+")\">("+ c_tun_list[0].tostring()+")</a>"
					
				}
				else if(pot4==0){

					text = ttextfile("chapter_03/08_5-5.txt")
					text.tx = ttext("<em>[5/5]</em>")
					local tx_list = ""
					for(local j=0;(j+start_lvl_z)<=end_lvl_z;j++){
						local c = coord(c_tun_list[j].x, c_tun_list[j].y)
						local c_z = c_tun_list[j].z
						if (glsw[j]==0){
							local link = "<a href=\"("+c.x+","+c.y+")\">("+c.tostring()+","+c_z+")</a>"
							local layer = translate("Layer level")+" = <st>"+(layer_lvl+j)+"</st>"
							tx_list += ttext("--> <st>" + format("[%d]</st> %s %s<br>", j+1, link, layer))
							text.lev = layer_lvl+j
							text.tunn = link
							break
						}
						else {
							local tx_ok = translate("OK")
							local tx_coord = "("+c.tostring()+","+c_z+")"
							local layer = translate("Layer level")+" = "+(layer_lvl+j)+""
							tx_list += ttext("<em>"+format("<em>[%d]</em> %s", j+1, tx_coord+" "+layer+" <em>"+tx_ok+"</em><br>"))
						}
					}
					text.mx_lvl = end_lvl_z
					text.list = tx_list
					
				}
				break

			case 9:

				local way_list = ""
				if (pot0==0){
					text = ttextfile("chapter_03/09_1-2.txt")
					text.tx = ttext("<em>[1/2]</em>")
					local w_nr = 0
					for(local j=0;j<c_way_lim1.len();j++){
						local c_a = coord(c_way_list1[j].a.x, c_way_list1[j].a.y)//c_way_lim1[j].a
						local c_b = coord(c_way_list1[j].b.x, c_way_list1[j].b.y)//c_way_lim1[j].b
						if (glsw[j]==0){
							local link1 = "<a href=\"("+c_a.x+","+c_a.y+")\">("+c_a.tostring()+")</a>"
							local link2 = " --> <a href=\"("+c_b.x+","+c_b.y+")\">("+c_b.tostring()+")</a><br>"
							way_list += ttext("<st>" + format("--> [%d]</st> %s", j+1, link1 + link2))
							w_nr = j
							break
						}
						else {
							local tx_ok = translate("OK")
							local tx_coord = "("+c_a.tostring()+") --> ("+c_b.tostring()+")"
							way_list += ttext("<em>" + format("<em>[%d]</em> %s", j+1, tx_coord+" <em>"+tx_ok+"</em><br>"))
						}
					}
					text.list = way_list
					text.w2 = c_way_lim1[w_nr].a.href("("+c_way_lim1[w_nr].a.tostring()+")")
				 	text.w1 = c_way_lim1[w_nr].b.href("("+c_way_lim1[w_nr].b.tostring()+")")
				}

				else if (pot1==0){
					text = ttextfile("chapter_03/09_2-2.txt")
					text.tx = ttext("<em>[2/2]</em>")
					local sigtxt = ""
					for(local j=0;j<signr;j++){
						local c = signal[j].coor
						if (glsw[j]==0){
							local link = "<a href=\"("+c.x+","+c.y+")\">("+c.tostring()+")</a><br>"
							sigtxt += ttext("<st>" + format(translate("Signal Nr.%d") + "</st> %s", j+1, link))
						}

						else{
							local tx_ok = translate("OK")
							local tx_coord = "("+c.tostring()+")"
							sigtxt += ttext("<em>" + format(translate("Signal Nr.%d") + "</em> %s", j+1, tx_coord+"<em> "+tx_ok+"</em><br>"))
						}
					}
					text.sig = sigtxt
					break
				}
				break
			
			case 10:
                if (pot0==0){
						if (glsw[1]==0){
						text = ttextfile("chapter_03/10_1-4.txt")
						text.tx = ttext("<em>[1/4]</em>")
					}
						else {
						text = ttextfile("chapter_03/10_2-4.txt")
						text.tx = ttext("<em>[2/4]</em>")
					}
				}
				else if (pot1==0){
					text = ttextfile("chapter_03/10_3-4.txt")
					text.tx = ttext("<em>[3/4]</em>")
				}	
				else if (pot2==0){
					text = ttextfile("chapter_03/10_4-4.txt")
					text.tx = ttext("<em>[4/4]</em>")
				}			

				text.dep = c_dep3.href("("+c_dep3.tostring()+")")

				break

			case 11:

				local tx_list = ""
				local nr = sch_list.len()
				local list = sch_list
				for (local j=0;j<nr;j++){
					local c = coord(list[j].x, list[j].y)
					local tile = my_tile(c)
					local st_halt = tile.get_halt()

					if(tmpsw[j]==0 ){
						tx_list += format("<st>%s %d:</st> %s<br>", translate("Stop"), j+1, c.href(st_halt.get_name()+" ("+c.tostring()+")"))
					}
					else{						
						tx_list += format("<em>%s %d:</em> %s <em>%s</em><br>", translate("Stop"), j+1, st_halt.get_name(), translate("OK"))
					}
				}
				local c = coord(list[0].x, list[0].y)
				text.stnam = "1) "+my_tile(c).get_halt().get_name()+" ("+c.tostring()+")"
				text.list = tx_list
				text.dep = c_dep3.href("("+c_dep3.tostring()+")")
				text.loc3 = translate(loc3_name_obj)
				text.load = loc3_load
				text.wait = get_wait_time_text(loc3_wait)
				text.cnr = d3_cnr
				text.tile = loc3_tile
				text.wag = sc_wag3_nr

				break
		}
		text.f1 = f1_coord.href(translate(f1name)+" ("+f1_coord.tostring()+")")
		text.f2 = f2_coord.href(translate(f2name)+" ("+f2_coord.tostring()+")")
		text.f3 = f3_coord.href(translate(f3name)+" ("+f3_coord.tostring()+")")
		text.cfar=f1_coord.href(translate(f1name)+" ("+f1_coord.tostring()+")")
		text.cmi=f2_coord.href(translate(f2name)+" ("+f2_coord.tostring()+")")
		text.cba=f3_coord.href(translate(f3name)+" ("+f3_coord.tostring()+")")
		text.cdep=c_dep1.href("("+c_dep1.tostring()+")")
		text.way1=c_dep2.href("("+c_dep2.tostring()+")")

		text.cy1=cy1.name
		text.cy2=cy2.name
		text.cy3=cy3.name
		text.cy4=cy4.name
		text.cy5=cy5.name

		text.co1=cy1.c.href("("+cy1.c.tostring()+")")
		text.co2=cy2.c.href("("+cy2.c.tostring()+")")
		text.co3=cy3.c.href("("+cy3.c.tostring()+")")
		text.co4=cy4.c.href("("+cy4.c.tostring()+")")
		text.co5=cy5.c.href("("+cy5.c.tostring()+")")
		text.cbor = ""
		if (coorbord==0)
			text.cbor = "<em>" + translate("Ok") + "</em>"
		else
			text.cbor = coord(coorbord.x, coorbord.y).href("("+coorbord.tostring()+")")

		text.tool1 = tool_alias.inspe
		text.tool2 = tool_alias.rail
		text.tool3 = tool_alias.land

		text.good1 = translate(f1_good)
		text.good2 = translate(f3_good)
		return text

	}
	
	function is_chapter_completed(pl) {
		local percentage=0
		persistent.sigcoord = sigcoord
		persistent.point = point
		save_pot()
		save_glsw()

		switch (this.step) {
			case 1:
				local next_mark = false
				if(pot0==0){
					try {
						next_mark = delay_mark_tile(f2_lim.a, f2_lim.b, 0)
					}
					catch(ev) {
						return 0
					}
				}
				else if (pot0==1 && pot1==0){
					try {
						next_mark = delay_mark_tile(f2_lim.a, f2_lim.b, 0)
					}
					catch(ev) {
						return 0
					}
					if(next_mark)
						pot1=1
				}
				else if (pot1==1 && pot2==0){
					try {
						next_mark = delay_mark_tile(f1_lim.a, f1_lim.b, 0)
					}
					catch(ev) {
						return 0
					}
				}
				else if (pot2==1 && pot3==0){
					try {
						next_mark = delay_mark_tile(f1_lim.a, f1_lim.b, 0)
					}
					catch(ev) {
						return 0
					}
					if(next_mark)
						pot3=1
				}
				else if (pot3==1 && pot4==0){
					this.next_step()
				}
				return 5
				break;
			case 2:
				//Primer tramo de rieles
				if (pot0==0){
					local limi = label1_lim
					local tile1 = my_tile(st1_list[0])
					if (!tile1.find_object(mo_way)){
						label_x.create(st1_list[0], player_x(0), translate("Build Rails form here"))
					}
					else
						tile1.remove_object(player_x(0), mo_label)

					local tile2 = my_tile(limi)
					if (!tile2.find_object(mo_way)){
						label_x.create(limi, player_x(0), translate("Build Rails form here"))

						//elimina el cuadro label
						local opt = 0
						local del = true
						local text = "X"
						label_bord(bord1_lim.a, bord1_lim.b, opt, del, text)
					}

					if (coorbord != 0){
						if (tile2.find_object(mo_label) && coorbord.x<=limi.x){
							if (!tile_x(wayend.x, wayend.y, wayend.z).find_object(mo_way))
								label_x.create(wayend, player_x(0), translate("Build Rails form here"))
							//Creea un cuadro label
							local opt = 0
							local del = false
							local text = "X"
							label_bord(bord1_lim.a, bord1_lim.b, opt, del, text)
							
							tile2.remove_object(player_x(0), mo_label)

						}
					}
					
					local opt = 0
					local coora = coord3d(c_way1.a.x, c_way1.a.y, c_way1.a.z)
					local coorb = coord3d(c_way1.b.x, c_way1.b.y, c_way1.b.z)
					local dir = 6
					local obj = false
					local wt = wt_rail

					wayend = coorb
					local fullway = get_fullway(coora, coorb, dir, obj)
					if (fullway==0){
						tile_x(coora.x, coora.y, coora.z).find_object(mo_way).unmark()
						tile_x(wayend.x, wayend.y, coorb.z).remove_object(player_x(0), mo_label)
						tile1.remove_object(player_x(0), mo_label)

						//elimina el cuadro label
						local opt = 0
						local del = true
						local text = "X"
						label_bord(bord1_lim.a, bord1_lim.b, opt, del, text)

						pot0=1
						wayend=0
						coorbord = 0
					}
					else 
						coorbord = fullway
				}
				//Para el puente
				else if (pot0==1&&pot1==0){
					local tile = my_tile(c_brge1.b)
					if ((!tile.find_object(mo_bridge))){
						label_x.create(c_brge1.b, player_x(0), translate("Build a Bridge here!."))
						coorbord = 	coord3d(tile.x, tile.y, tile.z)
					}
					else {
						tile.remove_object(player_x(0), mo_label)

						if (my_tile(c_brge1.b).find_object(mo_bridge)){
							coorbord = 0
							pot1=1
						}				
					}
				}
				//Segundo tramo de rieles
				else if (pot1==1 && pot2==0){
					local limi = label2_lim
					local tile1 = my_tile(limi)
					if ((coorbord!=0) && (coorbord.y > limi.y)){
						label_x.create(limi, player_x(0), translate("Build Rails form here"))
						//Creea un cuadro label
						local opt = 0
						local del = false
						local text = "X"
						label_bord(bord2_lim.a, bord2_lim.b, opt, del, text)
					}
					else if (coorbord!=0){
						tile1.remove_object(player_x(0), mo_label)
						//elimina el cuadro label
						local opt = 0
						local del = true
						local text = "X"
						label_bord(bord2_lim.a, bord2_lim.b, opt, del, text)
						if (!tile1.find_object(mo_label))
							label_x.create(st2_list[0], player_x(0), translate("Build Rails form here"))
					}
						
					local opt = 0
					local coora = coord3d(c_way3.a.x, c_way3.a.y, c_way3.a.z)
					local coorb = coord3d(c_way3.b.x, c_way3.b.y, c_way3.b.z)
					local dir = 6
					local obj = false
					wayend = coorb

					local fullway = get_fullway(coora, coorb, dir, obj)
					if (fullway==0){

						//elimina el cuadro label
						local opt = 0
						local del = true
						local text = "X"
						label_bord(bord2_lim.a, bord2_lim.b, opt, del, text)

						tile_x(coorb.x, coorb.y, coorb.z).remove_object(player_x(0), mo_label)
						tile1.remove_object(player_x(0), mo_label)
						coorbord = 0					
						this.next_step()	
					}
					else 
						coorbord = fullway
				}
				return 10
				break;
			case 3:
				glresult = null
				
				local passa = good_alias.passa
				local mail = good_alias.mail

				if (pot1==0){
					//Estaciones de la Fabrica
					local pl_nr = 1 
					local c_list = st2_list
					local st_nr = c_list.len() //Numero de estaciones
					local good = good_alias.goods
					local result = is_stations_building(pl_nr, c_list, st_nr, good)

					if(result){
						pot0=1
					}
				}
				
				if (pot0==1 && pot1==0){
					
					//Estaciones de la Fabrica
					local pl_nr = 1 
					local c_list = st1_list
					local st_nr = c_list.len() //Numero de estaciones
					local good = good_alias.goods
					local result = is_stations_building(pl_nr, c_list, st_nr, good)

					if (result){
						this.next_step()
					}
				}			
				return 15
				break
			case 4:
				local tile = my_tile(c_dep1)
				if(pot0==0){
					local c_list = [c_dep1_lim.a, c_dep1_lim.b]
					local siz = c_list.len()
					
					local next_mark = true

					try {
						 next_mark = delay_mark_tile_list(c_list,siz)
					}
					catch(ev) {
						return 0
					}				

					if(!tile.find_object(mo_way)){
						label_x.create(c_dep1, player_x(0), translate("Build Rails form here"))
					}
					else{
						local stop_mark = true
						try {
							 next_mark = delay_mark_tile_list(c_list,siz, stop_mark)
						}
						catch(ev) {
							return 0
						}
						tile.remove_object(player_x(0), mo_label)
						pot0=1	
					}		
				}

				else if(pot0==1 && pot1==0){
					if(!tile.find_object(mo_depot_rail)){
						label_x.create(c_dep1, player_x(0), translate("Build Train Depot here!."))
					}
					else{
						tile.remove_object(player_x(0), mo_label)
						pot1=1
					}				
				}

				else if(pot2==1){
					this.next_step()						
				}	
				return 16
				break
			case 5:
				if (!cov_sw)
					return 0

				local wt = wt_rail

				if (current_cov == ch3_cov_lim1.b){
					reached = get_reached_target(f2_coord, f1_good)
					if (reached>= f1_reached){
						pot1=1
					}	
				}

				if (pot1==1 && pot0==0){
					//Marca tiles para evitar construccion de objetos
					local c_list = c_lock
					local siz = c_lock.len()
					local del = false
					local pl_nr = 1
					local text = "X"
                    lock_tile_list(c_list, siz, del, pl_nr, text)

					this.next_step()
					reset_stop_flag()
					reached = 0
				}
				return 30
				break
			case 6:
				//Primer tramo de rieles
				if (pot0==0){

					local limi = label3_lim
					local tile1 = my_tile(st3_list[0])
					if (!tile1.find_object(mo_way)){
						label_x.create(st3_list[0], player_x(0), translate("Build Rails form here"))
					}
					else
						tile1.remove_object(player_x(0), mo_label)

					local tile2 = my_tile(limi)
					if (!tile2.find_object(mo_way)){
						label_x.create(limi, player_x(0), translate("Build Rails form here"))

						//elimina el cuadro label
						local opt = 0
						local del = true
						local text = "X"
						label_bord(bord3_lim.a, bord3_lim.b, opt, del, text)
					}

					if (coorbord != 0){
						//gui.add_message(""+coorbord.tostring()+"")
						if (tile2.find_object(mo_label) && coorbord.y>=limi.y){

							tile2.remove_object(player_x(0), mo_label)
							if (!tile_x(wayend.x, wayend.y, wayend.z).find_object(mo_way))
								label_x.create(wayend, player_x(0), translate("Build Rails form here"))
							//Creea un cuadro label
							local opt = 0
							local del = false
							local text = "X"
							label_bord(bord3_lim.a, bord3_lim.b, opt, del, text)
							
						}
					}

					local opt = 0
					local coora = coord3d(c_way4.a.x, c_way4.a.y, c_way4.a.z)
					local coorb = coord3d(c_way4.b.x, c_way4.b.y, c_way4.b.z)
					local obj = false
					local dir = 3

					wayend = coorb

					local fullway = get_fullway(coora, coorb, dir, obj)
					if (fullway==0){
						tile_x(coora.x, coora.y, coora.z).find_object(mo_way).unmark()
						tile_x(wayend.x, wayend.y, coorb.z).remove_object(player_x(0), mo_label)

						//elimina el cuadro label
						local opt = 0
						local del = true
						local text = "X"
						label_bord(bord3_lim.a, bord3_lim.b, opt, del, text)

						pot0 = 1
						wayend = 0
						coorbord = 0
					}
					else 
						coorbord = fullway
				}
				//Para el tunel
				else if (pot0==1 && pot1==0){
					local tile = my_tile(c_tunn1.a)
					if ((!tile.find_object(mo_tunnel))){
						label_x.create(c_tunn1.a, player_x(0), translate("Place a Tunnel here!."))
						coorbord = 	coord3d(tile.x, tile.y, tile.z)
					}
					else {
						tile.remove_object(player_x(0), mo_label)

						if (my_tile(c_tunn1.b).find_object(mo_tunnel)){
						}				
					}
					local opt = 0
					local coora = coord3d(c_tunn1.a.x, c_tunn1.a.y, c_tunn1.a.z)
					local coorb = coord3d(c_tunn1.b.x, c_tunn1.b.y, c_tunn1.b.z)
					local obj = false
					local dir = 5
					wayend = coorb				
					local fullway = get_fullway(coora, coorb, dir, obj)
					if (fullway==0){
						pot1=1	
						wayend = 0
						coorbord = 0		
					}
					else 
						coorbord = fullway

				}
				//Segundo tramo de rieles
				else if (pot1==1 && pot2==0){
					local limi = label4_lim
					local tile1 = my_tile(limi)
					local tile2 = my_tile(st4_list[0])
					if ((coorbord!=0) && (coorbord.y < limi.y)){
						label_x.create(limi, player_x(0), translate("Build Rails form here"))
						//Creea un cuadro label
						local opt = 0
						local del = false
						local text = "X"
						label_bord(bord4_lim.a, bord4_lim.b, opt, del, text)
					}
					else if (coorbord!=0){
						tile1.remove_object(player_x(0), mo_label)
						//elimina el cuadro label
						local opt = 0
						local del = true
						local text = "X"
						label_bord(bord4_lim.a, bord4_lim.b, opt, del, text)

						if (!tile2.find_object(mo_way))
							label_x.create(st4_list[0], player_x(0), translate("Build Rails form here"))
					}
					local opt = 0
					local coora = coord3d(c_way5.a.x, c_way5.a.y, c_way5.a.z)
					local coorb = coord3d(c_way5.b.x, c_way5.b.y, c_way5.b.z)
					local obj = false
					local dir = 5
					wayend = coorb				
					local fullway = get_fullway(coora, coorb, dir, obj)
					if (fullway==0){
						tile1.remove_object(player_x(0), mo_label)
						tile2.remove_object(player_x(0), mo_label)
						//elimina el cuadro label
						local opt = 0
						local del = true
						local text = "X"
						label_bord(bord4_lim.a, bord4_lim.b, opt, del, text)

						pot2=1		
						wayend = 0
						coorbord = 0		
					}
					else 
						coorbord = fullway
				}	
				
				//Text label para las estaciones			
				else if (pot2==1 && pot3==0){
					glresult = null
					local passa = good_alias.passa
					local mail = good_alias.mail

					//Estaciones de la Fabrica
					local pl_nr = 1 
					local c_list = st4_list
					local st_nr = c_list.len() //Numero de estaciones
					local good = good_alias.goods
					local result = is_stations_building(pl_nr, c_list, st_nr, good)

					if(result){
						pot3 = 1
					}
				}
				else if (pot3==1 && pot4==0){
					glresult = null
					local passa = good_alias.passa
					local mail = good_alias.mail

					//Estaciones de la Fabrica
					local pl_nr = 1 
					local c_list = st3_list
					local st_nr = c_list.len() //Numero de estaciones
					local good = good_alias.goods
					local result = is_stations_building(pl_nr, c_list, st_nr, good)

					if(result){
						pot4 = 1
					}
				}
				else if (pot4==1 && pot5==0){
					//Elimina las Marcas de tiles
					local c_list = c_lock
					local siz = c_lock.len()
					local del = true
					local pl_nr = 1
					local text = "X"
                    lock_tile_list(c_list, siz, del, pl_nr, text)

					this.next_step()
				}
				return 35
				break
			case 7:
				if (!cov_sw)
					return 0

				//Marca las vias del tren
				local opt = 2
				local wt = gl_wt
				local tile = my_tile(c_dep2)
				if(pot0==0){
					if(!tile.find_object(mo_way)){
						label_x.create(c_dep2, player_x(0), translate("Build Rails form here"))
					}
					else{
						tile.remove_object(player_x(0), mo_label)
						pot0=1	
					}			
				}

				else if(pot0==1 && pot1==0){
					if(!tile.find_object(mo_depot_rail)){
						label_x.create(c_dep2, player_x(0), translate("Build Train Depot here!."))
					}
					else{
						tile.remove_object(player_x(0), mo_label)
						pot1=1	
					}				
				}

				else if(current_cov == ch3_cov_lim2.b){
					reached = get_reached_target(f3_coord, f3_good)
					if (reached>=f3_reached){
						pot3=1
					}
				}

				if (pot3==1 && pot4==0){	
					this.next_step()
					reset_stop_flag()
					reached = 0
				}
					
				return 40
				break
			case 8:
				//Para el tramo de via
				if (pot0==0){
					local coora = coord3d(c_way6.a.x, c_way6.a.y, c_way6.a.z)
					local coorb = coord3d(c_way6.b.x, c_way6.b.y, c_way6.b.z)
					local obj = false
					local tunnel = false
					local dir = get_dir_start(coora)
					local fullway = get_fullway(coora, coorb, dir, obj, tunnel)
					coorbord = fullway
					if (fullway==0){
						pot0=1
						return 45
					}
				}
				//Para el puente
				else if (pot0==1 && pot1==0){
					local tile = my_tile(c_brge3.a)
					if ((!tile.find_object(mo_bridge))){
						label_x.create(c_brge3.a, player_x(0), translate("Build a Bridge here!."))
						coorbord = 	coord3d(tile.x, tile.y, tile.z)
					}
					else {
						tile.remove_object(player_x(0), mo_label)

						if (my_tile(c_brge3.b).find_object(mo_bridge)){
							coorbord = 0
							pot1=1
						}				
					}
				}
				//Para la entrada del tunel
				else if (pot1==1 && pot2==0){
					local t_tunn = my_tile(start_tunn)

					if (!t_tunn.find_object(mo_tunnel))
						label_x.create(start_tunn, player_x(0), translate("Place a Tunnel here!."))
					else{
						pot2=1
						t_tunn.remove_object(player_x(0), mo_label)						
					}							
				}
				//Para conectar las dos entradas del tunel
				else if (pot2==1 && pot3==0){

					local coora = coord3d(c_tunn2.a.x, c_tunn2.a.y, c_tunn2.a.z)
					local coorb = coord3d(c_tun_list[0].x, c_tun_list[0].y, start_lvl_z)
					local obj = false
					local tunnel = true
					local dir = get_dir_start(coora)
					local fullway = get_fullway(coora, coorb, dir, obj, tunnel)
					coorbord = fullway

					if (fullway==0){
						coorbord = 0
						pot3=1
						return 45
					}

				}
			
				else if (pot3==1 && pot4==0){
					local coora = coord3d(c_tunn2.a.x, c_tunn2.a.y, c_tunn2.a.z)
					local coorb = coord3d(c_tunn2.b.x, c_tunn2.b.y, c_tunn2.b.z)
					local obj = false
					local tunnel = true
					local dir = get_dir_start(coora)
					local fullway = get_fullway(coora, coorb, dir, obj, tunnel)
					coorbord = fullway
					if (fullway==0){
						coorbord = 0
						pot4=1
						return 45
					}

					if (coorbord!=0){
						if(coorbord.z<end_lvl_z){
							local slope = tile_x(coorbord.x, coorbord.y, coorbord.z).get_slope()
							for(local j=0;(j+start_lvl_z)<end_lvl_z;j++){
								if ((coorbord.x==c_tun_list[j].x && coorbord.y==c_tun_list[j].y) && slope==28){ //Incremento para subir en pendientes
									glsw[j]=1
								}
							}
						}
					}

				}
				else if (pot4==1){
					this.next_step()
				}	
				return 45
				break

			case 9:
				if (pot0==0){
		            for(local j=0;j<c_way_list1.len();j++){
						if(glsw[j] == 0){
							local tile_a = my_tile(c_way_lim1[j].a)
							local tile_b = my_tile(c_way_lim1[j].b)

							if (!tile_a.find_object(mo_label))
								label_x.create(c_way_lim1[j].a, player_x(1), translate("Build Rails form here"))

							if (!tile_b.find_object(mo_label))
								label_x.create(c_way_lim1[j].b, player_x(1), translate("Build Rails form here"))
							
							local coora = coord3d(c_way_list1[j].a.x, c_way_list1[j].a.y, c_way_list1[j].a.z)
							local coorb = coord3d(c_way_list1[j].b.x, c_way_list1[j].b.y, c_way_list1[j].b.z)
							local obj = false
							local dir = get_dir_start(coora)
							local fullway = get_fullway(coora, coorb, dir, obj)
							coorbord = fullway
							if (fullway==0){
								tile_a.remove_object(player_x(1), mo_label)
								tile_b.remove_object(player_x(1), mo_label)
								glsw[j]=1
								if(j == c_way_lim1.len()-1){
									pot0 = 1
									reset_glsw()
									coorbord = 0
									break
								}
							}
							break
						}
		            }
				}
				else if (pot0==1 && pot1==0){
					local sign_nr=0
					for(local j=0;j<signr;j++){
						local tile = tile_x(signal[j].coor.x, signal[j].coor.y, signal[j].coor.z)
						if (!tile.find_object(mo_signal) && !tile.find_object(mo_roadsign)){
							label_x.create(signal[j].coor, player_x(1), translate("Place Singnal here!."))
							tile.find_object(mo_way).mark()
						}
						else{
							local ribi = way_x(signal[j].coor.x, signal[j].coor.y, signal[j].coor.z).get_dirs_masked()
							tile.remove_object(player_x(1), mo_label)			
							if (ribi==signal[j].ribi){
								tile.find_object(mo_way).unmark()
								sign_nr++
								glsw[j]=1
								if (sign_nr==signr){
									this.next_step()
								}
							}
						}
					}
				}
				return 50
				break

			case 10:
				if (!cov_sw)
					return 0

				if (pot0==0){
		            for(local j=0;j<c_cate_list1.len();j++){
						if(glsw[j] == 0){		
							local coora = coord3d(c_cate_list1[j].a.x, c_cate_list1[j].a.y, c_cate_list1[j].a.z)
							local coorb = coord3d(c_cate_list1[j].b.x, c_cate_list1[j].b.y, c_cate_list1[j].b.z)
							local elect = mo_wayobj
							local dir = dir_list[j]
							local tunn = true
							
							local fullway = get_fullway(coora, coorb, dir, elect, tunn)
							coorbord = fullway
							if (fullway==0){
								glsw[j]=1
								if(j == c_cate_list1.len()-1){
									pot0 = 1
									reset_glsw()
									break
								}
							}
							break				
						}
		            }
				}
				if (pot0==1 && pot1==0){
					local way = my_tile(c_dep3).find_object(mo_way)
					if (way.is_electrified()){
						way.unmark()
						pot1= 1
					}
					else
						way.mark()
				}
				if (pot1==1 && pot2==0){
					local tile = my_tile(c_dep3)
					if (!tile.find_object(mo_depot_rail))
						label_x.create(c_dep3, player_x(0), translate("Build Train Depot here!."))
					else{
						tile.remove_object(player_x(0), mo_label)
						pot2=1
					}
				}
				if (pot2==1 && pot3==0){
					this.next_step()
				}
				return 97
				break
			
			case 11:
            	local c_dep = this.my_tile(c_dep3)
                set_convoy_schedule(pl,c_dep, gl_wt, line1_name)

                					
				if (current_cov == ch3_cov_lim3.b){
					this.next_step()
					reset_stop_flag()
					return 90
				}
				return 90
				break

			case 12:
					//gui.add_message("12!!!!!"+step+"")
				this.step=1
				persistent.step=1
				persistent.status.step = 1
				reset_stop_flag()
				persistent.point = null
				return 100
				break
		}
		return percentage
	}
		
	function is_work_allowed_here(pl, tool_id, pos) {
		//glpos = coord3d(pos.x, pos.y, pos.z)
		local t = tile_x(pos.x, pos.y, pos.z)
		local ribi = 0
		local wt = 0
		local slope = t.get_slope()
		local way = t.find_object(mo_way)
		local bridge = t.find_object(mo_bridge)
		local label = t.find_object(mo_label)
		local building = t.find_object(mo_building)
		local sign = t.find_object(mo_signal)
		local roadsign = t.find_object(mo_roadsign)
		if (way){
			wt = way.get_waytype()
			if (tool_id!=tool_build_bridge)
				ribi = way.get_dirs()
			if (!t.has_way(wt_rail))
				ribi = 0
		}

		local result = translate("Action not allowed")		// null is equivalent to 'allowed'

		switch (this.step) {
			case 1:
				if ((pos.x>=f2_lim.a.x)&&(pos.y>=f2_lim.a.y)&&(pos.x<=f2_lim.b.x)&&(pos.y<=f2_lim.b.y)){
					if (tool_id == 4096){
						if (pot0==0){
							pot0=1
							return null
						}			
					}
					else
						translate("You must use the inspection tool")+" ("+pos.tostring()+")."	
				}
				if ((pos.x>=f1_lim.a.x)&&(pos.y>=f1_lim.a.y)&&(pos.x<=f1_lim.b.x)&&(pos.y<=f1_lim.b.y)){
					if (tool_id == 4096){
						if (pot1==1){
							pot2=1
							return null
						}			
					}
					else
						translate("You must use the inspection tool")+" ("+pos.tostring()+")."	
				}	
				break;
			//Conectando los rieles con la segunda fabrica
			case 2:
				if (tool_id == 4096) return result = null
				
				//Primer tramo de rieles
				if (pot0==0){
					if (pos.x>=st1_way_lim.a.x && pos.y>=st1_way_lim.a.y && pos.x<=st1_way_lim.b.x && pos.y<=st1_way_lim.b.y){
						if(tool_id==tool_build_way || tool_id==4113 || tool_id==tool_remover)
							return null						
					}
					if (pos.x>=bord1_lim.a.x && pos.y>=bord1_lim.a.y && pos.x<=bord1_lim.b.x && pos.y<=bord1_lim.b.y){
						if (coorbord!=0){
							if (!way && label && label.get_text()=="X"){
								return translate("Indicates the limits for using construction tools")+" ( "+pos.tostring()+")."	
							}
							return all_control(result, gl_wt, way, ribi, tool_id, pos, coorbord)
						}
					}
					else if(tool_id==tool_build_way && coorbord!=0)
							return translate("Connect the Track here")+" ("+coorbord.tostring()+")."
				}
				//Construye un puente
				if (pot0==1 && pot1==0){
					if (pos.x>=c_bway_lim1.a.x && pos.y>=c_bway_lim1.a.y && pos.x<=c_bway_lim1.b.x && pos.y<=c_bway_lim1.b.y){
						if(tool_id==tool_build_way)
							return null
						if(tool_id==tool_build_bridge){
							if(pos.z==brge1_z)
								return null
							else
								return translate("You must build the bridge here")+" ("+c_brge1.b.tostring()+")."
						}	
					}
				}
				//Segundo tramo de rieles
				if (pot1==1&&pot2==0){
					if (pos.x>=st2_way_lim.a.x && pos.y>=st2_way_lim.a.y && pos.x<=st2_way_lim.b.x && pos.y<=st2_way_lim.b.y){
						return all_control(result, gl_wt, way, ribi, tool_id, pos, coorbord)	
					}
					if (pos.x>=bord2_lim.a.x && pos.y>=bord2_lim.a.y && pos.x<=bord2_lim.b.x && pos.y<=bord2_lim.b.y){
						if (coorbord!=0){
							if (!way && label && label.get_text()=="X"){
								return translate("Indicates the limits for using construction tools")+" ("+pos.tostring()+")."	
							}
							return all_control(result, gl_wt, way, ribi, tool_id, pos, coorbord)
						}
					}
					else if(tool_id==tool_build_way && coorbord!=0)
						return translate("Connect the Track here")+" ("+coorbord.tostring()+")."
				}
				break;

			case 3:
				if (pot0==0){
					//Estaciones de la Fabrica
					local good = good_alias.goods
					local c_list = st2_list
					local siz = c_list.len()
					return get_stations(pos, tool_id, result, good, c_list, siz)
				}
				
				else if (pot0==1 && pot1==0){
					//Estaciones del Productor
					local good = good_alias.goods
					local c_list = st1_list
					local siz = c_list.len()
					return get_stations(pos, tool_id, result, good, c_list, siz)
				}		
				break
			case 4:
				if(pot0==0){
					if (pos.x>=c_dep1_lim.a.x && pos.y>=c_dep1_lim.a.y && pos.x<=c_dep1_lim.b.x && pos.y<=c_dep1_lim.b.y){	
						if (tool_id==tool_build_way)
							return null
					}
					else return translate("You must build track in")+" ("+c_dep1.tostring()+")."
				}
				else if(pot0==1 && pot1==0){
					if ((pos.x==c_dep1.x)&&(pos.y==c_dep1.y)){
						if (tool_id==tool_build_depot)
							return null						
					}
					else return translate("You must build the train depot in")+" ("+c_dep1.tostring()+")."	
				}
				else if (pot1==1 && pot2==0){
					if ((pos.x==c_dep1.x)&&(pos.y==c_dep1.y)){
						if (tool_id==4096){
							pot2=1
							return null						
						}
						else return translate("You must use the inspection tool")+" ("+c_dep1.tostring()+")."
					}
				}
				break
			case 5:
				//Enrutar vehiculos (estacion nr1)
				if (building && pos.x>=st1_way_lim.a.x && pos.y>=st1_way_lim.a.y && pos.x<=st1_way_lim.b.x && pos.y<=st1_way_lim.b.y){
					if (tool_id==4108){
						if (stop_flag[0]==0){
							stop_flag[0] = 1
							return null
						}
						else
							return translate("Select the other station")+" ("+coord(st2_list[0].x, st2_list[0].y).tostring()+".)"
					}		
				}
				else if (tool_id==4108){
					if (stop_flag[0]==0)
						return format(translate("Select station No.%d"),1)+" ("+coord(st1_list[0].x, st1_list[0].y).tostring()+".)"
				} 
				//Enrutar vehiculos (estacion nr2)
				if (building && pos.x>=st2_way_lim.a.x && pos.y>=st2_way_lim.a.y && pos.x<=st2_way_lim.b.x && pos.y<=st2_way_lim.b.y){
					if (tool_id==4108){
						if (stop_flag[0]==1 && stop_flag[1]==0){
							stop_flag[1] = 1
							return null
						}
						if (stop_flag[0]==0)
							return translate("Select the other station first")+" ("+coord(st1_list[0].x, st1_list[0].y).tostring()+".)"
						else if (stop_flag[0]==1 && stop_flag[1]==1)
							return translate("The route is complete, now you may dispatch the vehicle from the depot")+" ("+c_dep1.tostring()+".)"	
					}	
				}
				else if (tool_id==4108){
					if (stop_flag[0]==0)
						return translate("Select the other station first")+" ("+coord(st1_list[0].x, st1_list[0].y).tostring()+".)"

					else if (stop_flag[0]==1 && stop_flag[1]==0)
						return format(translate("Select station No.%d"),2)+" ("+coord(st2_list[0].x, st2_list[0].y).tostring()+".)"

					else if (stop_flag[0]==1 && stop_flag[1]==1)
						return translate("The route is complete, now you may dispatch the vehicle from the depot")+" ("+c_dep1.tostring()+".)"
				}
				break

			//Conectando los rieles con el consumidor final
			case 6:				
				//Primer tramo de rieles
				if (pot0==0){
					if (pos.x>=st3_way_lim.a.x && pos.y>=st3_way_lim.a.y && pos.x<=st3_way_lim.b.x && pos.y<=st3_way_lim.b.y){			
						if(tool_id==tool_build_way || tool_id==4113 || tool_id==tool_remover)
							return null						
					}
					if (pos.x>=bord3_lim.a.x && pos.y>=bord3_lim.a.y && pos.x<=bord3_lim.b.x && pos.y<=bord3_lim.b.y){
						if (coorbord!=0){
							if (label && label.get_text()=="X"){
									return translate("Indicates the limits for using construction tools")+" ("+pos.tostring()+")."	
							}
							return all_control(result, gl_wt, way, ribi, tool_id, pos, coorbord)
							 
						}
					}
					else if(tool_id==tool_build_way && coorbord!=0)
						return translate("Connect the Track here")+" ("+coorbord.tostring()+")."
				}
				//Construye un tunel
				else if (pot0==1 && pot1==0){
					if (pos.x>=c_tway_lim2.a.x && pos.y>=c_tway_lim2.a.y && pos.x<=c_tway_lim2.b.x && pos.y<=c_tway_lim2.b.y){
						if(tool_id==tool_build_way || tool_id==tool_build_tunnel){
							return null
						}	
					}
				}

				//Segundo tramo de rieles
				if (pot1==1&&pot2==0){
					if (pos.x>=st4_way_lim.a.x && pos.y>=st4_way_lim.a.y && pos.x<=st4_way_lim.b.x && pos.y<=st4_way_lim.b.y){
						
							return all_control(result, gl_wt, way, ribi, tool_id, pos, coorbord)
						
					}
					if (pos.x>=bord4_lim.a.x && pos.y>=bord4_lim.a.y && pos.x<=bord4_lim.b.x && pos.y<=bord4_lim.b.y){
						if (coorbord!=0){
							if (!way && label && label.get_text()=="X"){
								return translate("Indicates the limits for using construction tools")+" ("+pos.tostring()+")."	
							}
							return all_control(result, gl_wt, way, ribi, tool_id, pos, coorbord)
						}
					}

					else if(tool_id==tool_build_way && coorbord!=0)
						return translate("Connect the Track here")+" ("+coorbord.tostring()+")."					
				}
				//Estaciones de la Fabrica
				else if (pot2==1 && pot3==0){
					local good = good_alias.goods
					local c_list = st4_list
					local siz = c_list.len()
					return get_stations(pos, tool_id, result, good, c_list, siz)
				}
				//Estaciones del Productor	
				else if (pot3==1 && pot4==0){
					local good = good_alias.goods
					local c_list = st3_list
					local siz = c_list.len()
					return get_stations(pos, tool_id, result, good, c_list, siz)
				}
				break
			case 7:
				if (tool_id==4096)
					break

				//Construye rieles y deposito
				if (pos.x>=c_dep2_lim.a.x && pos.y>=c_dep2_lim.a.y && pos.x<=c_dep2_lim.b.x && pos.y<=c_dep2_lim.b.y){
					if (pot0==0){
						if(tool_id==tool_build_way)
							return null
						else
							return translate("You must build track in")+" ("+c_dep2.tostring()+")."
					}
					else if (pot0==1 && pot1==0)
						if(tool_id==tool_build_depot)
							return null
						else
							return result = translate("You must build the train depot in")+" ("+c_dep2.tostring()+")."
				}
				else if (pot0==0)
					return translate("You must build track in")+" ("+c_dep2.tostring()+")."
				else if (pot0==1 && pot1==0)
					return result = translate("You must build the train depot in")+" ("+c_dep2.tostring()+")."

				//Enrutar vehiculos (estacion nr1)
				if (pot1==1 && pot2==0){
					if (building && pos.x>=st3_way_lim.a.x && pos.y>=st3_way_lim.a.y && pos.x<=st3_way_lim.b.x && pos.y<=st3_way_lim.b.y){
						if (tool_id==4108 && building){
							if (stop_flag[0]==0){
								stop_flag[0] = 1
								return null
							}
							else
								return translate("Select the other station")+" ("+coord(st4_list[0].x, st4_list[0].y).tostring()+".)"
						}		
					}
					else if (tool_id==4108){
						if (stop_flag[0]==0)
							return format(translate("Select station No.%d"),1)+" ("+coord(st3_list[0].x, st3_list[0].y).tostring()+".)"
					} 
					//Enrutar vehiculos (estacion nr2)
					if (building && pos.x>=st4_way_lim.a.x && pos.y>=st4_way_lim.a.y && pos.x<=st4_way_lim.b.x && pos.y<=st4_way_lim.b.y){
						if (tool_id==4108 && building){
							if (stop_flag[0]==1 && stop_flag[1]==0){
								stop_flag[1] = 1
								return null
							}
							if (stop_flag[0]==0)
								return translate("Select the other station first")+" ("+coord(st3_list[0].x, st3_list[0].y).tostring()+".)"
							else if (stop_flag[0]==1 && stop_flag[1]==1)
								return translate("The route is complete, now you may dispatch the vehicle from the depot")+" ("+c_dep1.tostring()+".)"	
						}	
					}
					else if (tool_id==4108){
						if (stop_flag[0]==0)
							return translate("Select the other station first")+" ("+coord(st3_list[0].x, st3_list[0].y).tostring()+".)"

						else if (stop_flag[0]==1 && stop_flag[1]==0)
							return format(translate("Select station No.%d"),2)+" ("+coord(st4_list[0].x, st4_list[0].y).tostring()+".)"

						else if (stop_flag[0]==1 && stop_flag[1]==1)
							return translate("The route is complete, now you may dispatch the vehicle from the depot")+" ("+c_dep1.tostring()+".)"
					}
				}
				if (pot2==1 && pot3==0){
					return translate("The route is complete, now you may dispatch the vehicle from the depot")+" ("+c_dep1.tostring()+".)"
				}
				break

			case 8:
				//Construye tramo de via para el tunel
				if (pot0==0){
					if (pos.x>=c_way6_lim.a.x && pos.y<=c_way6_lim.a.y && pos.x<=c_way6_lim.b.x && pos.y>=c_way6_lim.b.y){
						if (tool_id==tool_build_way || tool_id == tool_build_bridge || tool_id == tool_build_tunnel){
							return all_control(result, gl_wt, way, ribi, tool_id, pos, coorbord)
						}
					}
				}
				//Construye un puente
				else if (pot0==1 && pot1==0){
					if (pos.x>=c_bway_lim3.a.x && pos.y>=c_bway_lim3.a.y && pos.x<=c_bway_lim3.b.x && pos.y<=c_bway_lim3.b.y){
						if(tool_id==tool_build_way)
							return null
						if(tool_id==tool_build_bridge){
							if(pos.z==brge3_z)
								return null
							else
								return translate("You must build the bridge here")+" ("+c_brge3.a.tostring()+")."
						}	
					}
				}
				//Construye Entrada del tunel
				else if (pot1==1 && pot2==0){
					if (tool_id==tool_build_tunnel){
						if (pos.x==c_tun_lock.x && pos.y==c_tun_lock.y)
							return translate("Press [Ctrl] to build a tunnel entrance here")+" ("+start_tunn.tostring()+".)"

						if (pos.x == start_tunn.x && pos.y == start_tunn.y)
							return null	

						if (pos.x == end_tunn.x && pos.y == end_tunn.y)
							return null				
					}
				}
				//Conecta los dos extremos del tunel
				else if (pot2==1 && pot3==0){
					if (tool_id==tool_remover){
						if (pos.x>=c_tunn2_lim.a.x && pos.y<=c_tunn2_lim.a.y && pos.x<=c_tunn2_lim.b.x && pos.y>=c_tunn2_lim.b.y){
							if(slope==0)return null
						}
					}
					if (tool_id==tool_build_tunnel || tool_id==tool_build_way || tool_id== 4099){
						if((pos.x>=c_tun_list[0].x)&&(pos.x>=c_tunn2_lim.a.x && pos.y<=c_tunn2_lim.a.y && pos.x<=c_tunn2_lim.b.x && pos.y>=c_tunn2_lim.b.y)){
							if((pos.x == c_lock_tunn[0].x && pos.y == c_lock_tunn[0].y)||( pos.x == c_lock_tunn[1].x && pos.y == c_lock_tunn[1].y))
								return coorbord!=0? translate("Build a tunnel here")+" ("+coorbord.tostring()+")." : result
							return all_control(result, gl_wt, way, ribi, tool_id, pos, coorbord!=0?coorbord:pos)
						}
					}
				}
				//Tunel con pendientes
				else if (pot3==1 && pot4==0){
					local max = 1
					local count_tunn = count_tunnel(pos, max)
					if (tool_id==tool_remover){
						if (pos.x>=c_tunn2_lim.a.x && pos.y<=c_tunn2_lim.a.y && pos.x<=c_tunn2_lim.b.x && pos.y>=c_tunn2_lim.b.y){
							if(!count_tunn && slope==0 && way && way.is_marked())
								return null
							if(count_tunn && pos.z!=end_lvl_z) return translate("You must use the tool to raise the ground here")+" ("+coorbord.tostring()+".)" 
						}
					}
					if (tool_id==4100){
						if (pos.x>=c_tunn2_lim.a.x && pos.y<=c_tunn2_lim.a.y && pos.x<=c_tunn2_lim.b.x && pos.y>=c_tunn2_lim.b.y){
							if (pos.z == start_lvl_z && slope!=28){
								return null
							}
							if (count_tunn || slope != 0){
								if (pos.z == (end_lvl_z))
									return translate("The tunnel is already at the correct level")+" (-"+end_lvl_z+")."
								if (slope==0)
									return null
								else if (slope_rotate(slope))
									return translate("The slope is ready.")
								else if (coorbord!=0){
									local slopebord = tile_x(coorbord.x, coorbord.y, coorbord.z).get_slope()
										if (!label && slopebord == 28)
											return translate("The slope is ready.")
										if (slopebord==0)
											return translate("Raise ground here")+" ("+coorbord.tostring()+".)"
										else if (slope_rotate(slopebord))
											return translate("First you must build a tunnel section.")
										else
											return null
								}
							}
							else if (slope==0){
								if (pos.z == (end_lvl_z))
									return translate("The tunnel is already at the correct level")+" (-"+end_lvl_z+")."
								return translate("The tunnel is not correct, use the [Remove] tool here")+" ("+coorbord.tostring()+".)" 
							}
						}
						else return coorbord!=0 && slope==0? translate("Modify the terrain here")+" ("+coorbord.tostring()+")." : result
					}
					if (tool_id==tool_build_tunnel || tool_id==tool_build_way || tool_id== 4099){
						if (pos.x>=c_tunn2_lim.a.x && pos.y<=c_tunn2_lim.a.y && pos.x<=c_tunn2_lim.b.x && pos.y>=c_tunn2_lim.b.y){
							if (way && slope != 28 && slope!= 0) return translate("You must upper the ground first")+" ("+coorbord.tostring()+".)"
							if (coorbord!=0){
								local slopebord = tile_x(coorbord.x, coorbord.y, coorbord.z).get_slope()
								if((count_tunn && slopebord==28)||(pos.z== start_lvl_z && slope==28)) return all_control(result, gl_wt, way, ribi, tool_id, pos, coorbord)
								if (pos.z < end_lvl_z){
									if (slopebord==28){
										//gui.add_message(""+count_tunn+" "+pos.tostring())
										local is_mark = way? way.is_marked():false 
										local cursor = t.find_object(mo_pointer)
										local max = 2
										local lock = cursor_tile_count(cursor, is_mark, max, pos)
										if(is_mark || label || lock) return all_control(result, gl_wt, way, ribi, tool_id, pos, coorbord)
										if (!count_tunn)
											return translate("The tunnel is not correct, use the [Remove] tool here")+" ("+coorbord.tostring()+".)"

										return translate("First you must Upper the layer level.")
									}
									else if (slopebord==0){
										if (!count_tunn) return translate("The tunnel is not correct, use the [Remove] tool here")+" ("+coorbord.tostring()+".)" 
										return translate("You must upper the ground first")+" ("+coorbord.tostring()+".)"
									}
								}
								else if (pos.z == end_lvl_z)
									return all_control(result, gl_wt, way, ribi, tool_id, pos, coorbord)
							}
							return translate("First you must Upper the layer level.")
						}
						else if(pos.z == end_lvl_z) return coorbord!=0? translate("Connect the Track here")+" ("+coorbord.tostring()+").": result
						else return coorbord!=0? translate("Build a tunnel here")+" ("+coorbord.tostring()+")." : result		
					}
				}

				break
			case 9:
				if (pot0==0){
					result = coorbord != 0? translate("Connect the Track here")+" ("+coorbord.tostring()+").":result
		            for(local j=0;j<c_way_lim1.len();j++){
						if(glsw[j] == 0){
							if(pos.x>=c_way_lim1[j].a.x && pos.y>=c_way_lim1[j].a.y && pos.x<=c_way_lim1[j].b.x && pos.y<=c_way_lim1[j].b.y){
												   
								if(tool_id == tool_build_way){
								   return null
								}
							   }
						   else if (j== c_way_lim1.len()-1){
								result = translate("You are outside the allowed limits!")+" ("+pos.tostring()+")."
						   	}
							break
						}
					}
					return result
				}
				if (pot0==1 && pot1==0){
					//Elimina las señales
					if (tool_id==tool_remover){
						if (sign || roadsign){
							for(local j=0;j<signr;j++){
								if (pos.x==signal[j].coor.x && pos.y==signal[j].coor.y){
									backward_glsw(j)
									return null
								}
							}
						}
						else
							return translate("Only delete signals.")							
					}
					//Construye señales de paso					
					if (tool_id==4116){
						if (!sign){
							for(local j=0;j<signr;j++){
								local tile = tile_x(signal[j].coor.x, signal[j].coor.y, signal[j].coor.z)
								local r
								if (tile.find_object(mo_signal)){
									r = get_signa(signal[j].coor,j,signal[j].ribi)
									if (r==null)
										return translate("The signal does not point in the correct direction")+" ("+signal[j].coor.tostring()+")."
								}
								else
									result = translate("Place a block signal here")+" ("+signal[j].coor.tostring()+")."

								if (tile.find_object(mo_roadsign))
									return translate("It must be a block signal!")+" ("+signal[j].coor.tostring()+")."
							}	
						}
						for(local j=0;j<signr;j++){
							local tile = tile_x(signal[j].coor.x, signal[j].coor.y, signal[j].coor.z)
							if (tile.find_object(mo_roadsign))
								return translate("It must be a block signal!")+" ("+signal[j].coor.tostring()+")."
							if ((pos.x==signal[j].coor.x)&&(pos.y==signal[j].coor.y)){
								if (label)point[0]++
								return get_signa(pos,j,signal[j].ribi)
							}
						}
						return result
					}
				}
				break
			case 10:
                //return square_x(pos.x, pos.y).get_climate()

				if (pot0==0){
		            for(local j=0;j<c_cate_lim1.len();j++){
		               if(pos.x>=c_cate_lim1[j].a.x && pos.y>=c_cate_lim1[j].a.y && pos.x<=c_cate_lim1[j].b.x && pos.y<=c_cate_lim1[j].b.y){
		                                   
		                    if(tool_id == 4114){
								return null
		                    }
		               }
		               else if (j== c_cate_lim1.len()-1){
		                    result =  coorbord!=0 ? translate("Connect the Track here")+" ("+coorbord.tostring()+").": result
		               }
		            }
					if ((tool_id == 4114)&&(pos.x==c_dep3.x)&&(pos.y==c_dep3.y)) return null
				}
				else if (pot0==1 && pot1==0){
					if (pos.x>=c_dep3_lim.a.x && pos.y>=c_dep3_lim.a.y && pos.x<=c_dep3_lim.b.x && pos.y<=c_dep3_lim.b.y){
						if (tool_id==4114){
							return null
						}					
							
					}
		            result = translate("Connect the Track here")+" ("+c_dep3.tostring()+")."
				}
				else if (pot1==1 && pot2==0){
					if ((pos.x==c_dep3.x)&&(pos.y==c_dep3.y)){
						if (tool_id==tool_build_depot){
							return null
						}					
					}
					result = translate("You must build the train depot in")+" ("+c_dep3.tostring()+")."
				}
				break

			case 11:

				if (tool_id==4108){
					//gui.add_message(""+st_lim_a.len()+"")
					for(local j=0;j<st_lim_a.len();j++){				
						result = format(translate("Select station No.%d"),j+1)+" ("+st_lim_a[j].a.tostring()+".)"
						if(tmpsw[j]==0){
		                    if((pos.x>=st_lim_a[j].a.x)&&(pos.y>=st_lim_a[j].a.y)&&(pos.x<=st_lim_a[j].b.x)&&(pos.y<=st_lim_a[j].b.y)){
								local c_list = sch_list //Lista de todas las estaciones
								local c_dep = c_dep3 //Coordeadas del deposito 
								local siz = c_list.len()//Numero de paradas 
								result = translate("The route is complete, now you may dispatch the vehicle from the depot")+" ("+c_dep.tostring()+")."
								return is_stop_allowed_ex(result, siz, c_list, pos, gl_wt)					
							}
							else
								return result
						}
						if ((j+1) == st_lim_a.len())
							return translate("The route is complete, now you may dispatch the vehicle from the depot")+" ("+c_dep3.tostring()+")."
					}
					return result
				}
				
				break
		}
		if (tool_id == 4096){
			if (label && label.get_text()=="X")
				return translate("Indicates the limits for using construction tools")+" ("+pos.tostring()+")."		
			else if (label)	
				return translate("Text label")+" ("+pos.tostring()+")."
			result = null	// Always allow query tool
		}
		if (label && label.get_text()=="X")
			return translate("Indicates the limits for using construction tools")+" ("+pos.tostring()+")."	

		return result	
	}
	
	function is_schedule_allowed(pl, schedule) {
		local result=null	// null is equivalent to 'allowed'
		local nr =  schedule.entries.len()
		switch (this.step) {
			case 5:
				local selc = 0
				local load = loc1_load
				local time = loc1_wait
				local c_list = [st1_list[0], st2_list[0]]
				local siz = c_list.len()
				return set_schedule_list(result, pl, schedule, nr, selc, load, time, c_list, siz)
			break

			case 7:
				local selc = 0
				local load = loc2_load
				local time = loc2_wait
				local c_list = [st3_list[0], st4_list[0]]
				local siz = c_list.len()
				return set_schedule_list(result, pl, schedule, nr, selc, load, time, c_list, siz)
			break

			case 11:
				local selc = 0
				local load = loc3_load
				local time = loc3_wait
				local c_list = sch_list
				local siz = c_list.len()
				result = set_schedule_list(result, pl, schedule, nr, selc, load, time, c_list, siz)
				if(result == null){
					local line_name = line1_name
					update_convoy_schedule(pl, gl_wt, line_name, schedule)
				}
				return result
			break
		}
		return result = translate("Action not allowed")
	}

	function is_convoy_allowed(pl, convoy, depot)
	{
		local result = translate("It is not allowed to start vehicles.")
		switch (this.step) {
			case 5:
				if (comm_script){
					cov_save[current_cov]=convoy
					id_save[current_cov]=convoy.id
					gcov_nr++
					persistent.gcov_nr = gcov_nr
					return null
				}

				local wt = gl_wt
				if ((depot.x != c_dep1.x)||(depot.y != c_dep1.y))
					return 0
				local cov = 1
				local veh = 6
				local good_list = [good_desc_x(f1_good).get_catg_index()] //Wood
				local name = loc1_name_obj
				local st_tile = st1_list.len() // 3
				local is_st_tile = true
				result = is_convoy_correct(depot,cov,veh,good_list,name, st_tile, is_st_tile)

				if (result!=null){
					backward_pot(0)
					local name = translate(loc1_name_obj)
					local good = translate(f1_good)
					return train_result_message(result, name, good, veh, cov, st_tile)
				}

				if (current_cov>ch3_cov_lim1.a && current_cov<ch3_cov_lim1.b){
					local selc = 0
					local load = loc1_load
					local time = loc1_wait
					local c_list = [st1_list[0], st2_list[0]]
					local siz = c_list.len()
					return set_schedule_convoy(result, pl, cov, convoy, selc, load, time, c_list, siz)
				}
			break

			case 7:
				if (comm_script){
					cov_save[current_cov]=convoy
					id_save[current_cov]=convoy.id
					gcov_nr++
					persistent.gcov_nr = gcov_nr
					return null
				}

				local wt = gl_wt
				if ((depot.x != c_dep2.x)||(depot.y != c_dep2.y))
					return translate("You must select the deposit located in")+" ("+c_dep2.tostring()+")."	
				local cov = 1
				local veh = 6
				local good_list = [good_desc_x(f3_good).get_catg_index()]
				local name = loc2_name_obj
				local st_tile = st3_list.len() // 3
				local is_st_tile = true
				result = is_convoy_correct(depot,cov,veh,good_list,name, st_tile, is_st_tile)

				if (result!=null){
					local name = translate(loc2_name_obj)
					local good = translate(f3_good)
					return train_result_message(result, name, good, veh, cov, st_tile)
				}
				if (current_cov>ch3_cov_lim2.a && current_cov<ch3_cov_lim2.b){
					local selc = 0
					local load = loc2_load
					local time = loc2_wait
					local c_list = [st3_list[0], st4_list[0]]
					local siz = c_list.len()
					return set_schedule_convoy(result, pl, cov, convoy, selc, load, time, c_list, siz)
				}
			break

			case 11:
				if (comm_script){
					cov_save[current_cov]=convoy
					id_save[current_cov]=convoy.id
					gcov_nr++
					persistent.gcov_nr = gcov_nr
					current_cov++
					gall_cov++
					return null
				}

				local wt = gl_wt
				if ((depot.x != c_dep3.x)||(depot.y != c_dep3.y))
					return translate("You must select the deposit located in")+" ("+c_dep3.tostring()+")."	
				local cov = d3_cnr
				local veh = 8
				local good_list = [good_desc_x(good_alias.passa).get_catg_index()] //Passengers
				local name = loc3_name_obj
				local st_tile = loc3_tile
				local is_st_tile = true

				//Para arracar varios vehiculos
				local id_start = ch3_cov_lim3.a
				local id_end = ch3_cov_lim3.b
				local cir_nr = get_convoy_number_exp(sch_list[0], depot, id_start, id_end)
				local cov_list = depot.get_convoy_list()
				cov -= cir_nr
				result = is_convoy_correct(depot, cov, veh, good_list, name, st_tile, is_st_tile)

				if (result!=null){
					local name = translate(loc3_name_obj)
					local good = translate("Passengers")
					return train_result_message(result, name, good, veh, cov, st_tile)
				}

			if (current_cov>ch3_cov_lim3.a && current_cov<ch3_cov_lim3.b){
					local selc = 0
					local load = loc3_load
					local time = loc3_wait
					local c_list = sch_list
					local siz = c_list.len()
					return set_schedule_convoy(result, pl, cov, convoy, selc, load, time, c_list, siz)
				}
			break

		}
		return result = translate("It is not allowed to start vehicles.")
	}

	function script_text()
	{
		if(coorbord!=0){
			local way = tile_x(coorbord.x, coorbord.y, coorbord.z).find_object(mo_way)
			if(way) way.unmark()
		}
		switch (this.step) {
			case 1:
				if(pot0==0){
					pot0=1
				}
				if (pot2==0){
					pot2=1
				}
				return null
				break;
			case 2:
				//Primer tramo de rieles
				if (pot0==0){
					//Station tramo ----------------------------------------------------------------------
					local t_start = my_tile(st1_way_lim.b)
					local t_end = my_tile(label1_lim)

					t_start.remove_object(player_x(0), mo_label)
					t_end.remove_object(player_x(0), mo_label)

					local t = command_x(tool_build_way)			
					local err = t.work(player_x(1), t_start, t_end, sc_way_name)

					//Outside tramo ----------------------------------------------------------------------
					t_start = my_tile(label1_lim)
					t_end = my_tile(coord(c_way1.b.x, c_way1.b.y))
					t = command_x(tool_build_way)			
					err = t.work(player_x(1), t_start, t_end, sc_way_name)

					//elimina el cuadro label
					local opt = 0
					local del = true
					local text = "X"
					label_bord(bord1_lim.a, bord1_lim.b, opt, del, text)
				}
				//Para el puente
				if (pot1==0){
					local t_start = my_tile(c_brge1.a)
					local t_end = my_tile(c_brge1.b)

					t_start.remove_object(player_x(0), mo_label)

					local t = command_x(tool_build_bridge)
					t.set_flags(2)		
					local err = t.work(player_x(1), t_start, t_end, sc_bridge_name)
				}
				//Segundo tramo de rieles
				if (pot4==0){
					//Outside tramo ----------------------------------------------------------------------
					local t_start = my_tile(coord(c_way3.a.x, c_way3.a.y))
					local t_end = my_tile(label2_lim)
					local t = command_x(tool_build_way)			
					local err = t.work(player_x(1), t_start, t_end, sc_way_name)

					//Station tramo ----------------------------------------------------------------------
					t_start = my_tile(label2_lim)
					t_end = my_tile(st2_way_lim.a)

					t_start.remove_object(player_x(0), mo_label)
					t_end.remove_object(player_x(0), mo_label)

					t = command_x(tool_build_way)			
					err = t.work(player_x(1), t_start, t_end, sc_way_name)

				}
				return null
				break;
			case 3:

				if (pot0==0){
					//Estaciones de la Fabrica
					for(local j=0;j<st2_list.len();j++){
						local tile = my_tile(st2_list[j])
						tile.find_object(mo_way).unmark()
						if (tile.get_halt()==null){
							if (tile.find_object(mo_label))
								tile.remove_object(player_x(1), mo_label)
							local tool = command_x(tool_build_station)			
							local err = tool.work(player_x(0), tile, sc_station_name)
						}
					}
				}
				
				if (pot1==0){
					//Estaciones del Productor
					for(local j=0;j<st1_list.len();j++){
						local tile = my_tile(st1_list[j])
						tile.find_object(mo_way).unmark()
						if (tile.get_halt()==null){
							if (tile.find_object(mo_label))
								tile.remove_object(player_x(1), mo_label)
							local tool = command_x(tool_build_station)			
							local err = tool.work(player_x(0), tile, sc_station_name)
						}
					}
				}			
				return null
				break
			case 4:

				if(pot0==0){
					local t_start = my_tile(c_dep1_lim.a)
					local t_end = my_tile(c_dep1_lim.b)
					t_start.remove_object(player_x(0), mo_label)
					local t = command_x(tool_build_way)			
					local err = t.work(player_x(1), t_start, t_end, sc_way_name)

					pot0=1	
				}

				if(pot1==0){
					local tile = my_tile(c_dep1)
					tile.remove_object(player_x(0), mo_label)
					local t = command_x(tool_build_depot)
					local err = t.work(player_x(0), tile, sc_dep_name)
					pot1=1
				}
				if(pot1==1 && pot2==0){
					pot2=1
				}

				return null
				break
			case 5:
				local wt = wt_rail
				comm_script = true
				if (current_cov>ch3_cov_lim1.a && current_cov<ch3_cov_lim1.b){
					local pl = player_x(0)
					local c_depot = my_tile(c_dep1)

					comm_destroy_convoy(pl, c_depot) // Limpia los vehiculos del deposito

					local good_nr = 0 //Passengers
					local name = loc1_name_obj
					local wag_name = sc_veh1_name
					local wag_nr = sc_wag1_nr //5
					local wag = true
					local cov_nr = 0  //Max convoys nr in depot
					if (!comm_set_convoy(cov_nr, c_depot, name)){
						return 0
					}
					for (local count = 0;count<wag_nr;count++){
						if (!comm_set_convoy(cov_nr, c_depot, wag_name, wag))
							return 0
					}
					local depot = depot_x(c_depot.x, c_depot.y, c_depot.z)
					local convoy = depot.get_convoy_list()
					local sched = schedule_x(wt, [])
					sched.entries.append(schedule_entry_x(my_tile(st1_list[0]), loc1_load, loc1_wait))
					sched.entries.append(schedule_entry_x(my_tile(st2_list[0]), 0, 0))

					comm_start_convoy(pl, wt, sched, convoy, depot)
					pot1=1
				}
				comm_script = false

				return null
				break
			case 6:
				//Primer tramo de rieles
				if (pot0==0){

					//Station tramo ----------------------------------------------------------------------
					local t_start = my_tile(st3_way_lim.a)
					local t_end = my_tile(label3_lim)

					t_start.remove_object(player_x(0), mo_label)
					t_end.remove_object(player_x(0), mo_label)

					local t = command_x(tool_build_way)			
					local err = t.work(player_x(1), t_start, t_end, sc_way_name)

					//Outside tramo ----------------------------------------------------------------------
					t_start = my_tile(label3_lim)
					t_end = my_tile(coord(c_way4.b.x, c_way4.b.y))
					t = command_x(tool_build_way)			
					err = t.work(player_x(1), t_start, t_end, sc_way_name)

					//elimina el cuadro label
					local opt = 0
					local del = true
					local text = "X"
					label_bord(bord3_lim.a, bord3_lim.b, opt, del, text)
				}
				//Para el Tunel
				if (pot1==0){
					local t_start = my_tile(c_tway_lim2.a)
					local t_end = my_tile(c_tway_lim2.b)

					t_start.remove_object(player_x(0), mo_label)

					local t = command_x(tool_build_tunnel)
					local err = t.work(player_x(1), t_start, sc_tunn_name)
				}
				//Segundo tramo de rieles
				if (pot2==0){
					//Outside tramo ----------------------------------------------------------------------
					local t_start = my_tile(coord(c_way5.a.x, c_way5.a.y))
					local t_end = my_tile(label4_lim)
					local t = command_x(tool_build_way)			
					local err = t.work(player_x(1), t_start, t_end, sc_way_name)

					//Station tramo ----------------------------------------------------------------------
					t_start = my_tile(label4_lim)
					t_end = my_tile(st4_way_lim.b)

					t_start.remove_object(player_x(0), mo_label)
					t_end.remove_object(player_x(0), mo_label)

					t = command_x(tool_build_way)			
					err = t.work(player_x(1), t_start, t_end, sc_way_name)
				}
				if (pot3==0){
					glresult = null
					local passa = good_alias.passa
					local mail = good_alias.mail
					//Estaciones de la Fabrica
					for(local j=0;j<st4_list.len();j++){
						local tile = my_tile(st4_list[j])
						tile.find_object(mo_way).unmark()
						if (tile.get_halt()==null){
							if (tile.find_object(mo_label))
								tile.remove_object(player_x(1), mo_label)
							local tool = command_x(tool_build_station)			
							local err = tool.work(player_x(0), my_tile(st4_list[j]), sc_station_name)
						}
					}
					//Estaciones del Productor
					for(local j=0;j<st3_list.len();j++){
						local tile = my_tile(st3_list[j])
						tile.find_object(mo_way).unmark()
						if (tile.get_halt()==null){
							if (tile.find_object(mo_label))
								tile.remove_object(player_x(1), mo_label)
							local tool = command_x(tool_build_station)			
							local err = tool.work(player_x(0), my_tile(st3_list[j]), sc_station_name)
						}
					}
				}
				return null
				break
			case 7:
				if (!correct_cov)
					return 0

				local opt = 2
				local wt = wt_rail

				if(pot0==0){
					local t_start = my_tile(c_dep2_lim.b)
					local t_end = my_tile(c_dep2_lim.a)
					t_end.remove_object(player_x(0), mo_label)
					local t = command_x(tool_build_way)			
					local err = t.work(player_x(1), t_start, t_end, sc_way_name)

					pot0=1	
				}

				if(pot0==1 && pot1==0){
					local t2 = command_x(tool_build_depot)
					local err2 = t2.work(player_x(0), my_tile(c_dep2), sc_dep_name)
					pot1=1
				}
				if(pot1==1 && pot2==0){
					comm_script = true
					local wt = wt_rail
					if (current_cov>ch3_cov_lim2.a && current_cov<ch3_cov_lim2.b){
						local pl = player_x(0)
						local c_depot = my_tile(c_dep2)

						comm_destroy_convoy(pl, c_depot) // Limpia los vehiculos del deposito

						local name = loc2_name_obj
						local wag_name = sc_veh2_name
						local wag_nr = 	sc_wag2_nr //5
						local wag = true
						local cov_nr = 0  //Max convoys nr in depot
						if (!comm_set_convoy(cov_nr, c_depot, name))
							return 0
						for (local count = 0;count<wag_nr;count++){
							if (!comm_set_convoy(cov_nr, c_depot, wag_name, wag))
								return 0
						}
						local depot = depot_x(c_depot.x, c_depot.y, c_depot.z)
						local convoy = depot.get_convoy_list()
						local sched = schedule_x(wt, [])
						sched.entries.append(schedule_entry_x(my_tile(st3_list[0]), loc2_load, loc2_wait))
						sched.entries.append(schedule_entry_x(my_tile(st4_list[0]), 0, 0))

						comm_start_convoy(pl, wt, sched, convoy, depot)
					}
					comm_script = false	
					pot3=1			
				}

				return null
				break

			case 8:
				if (pot0==0){
					local coora = coord3d(c_way6.a.x, c_way6.a.y, c_way6.a.z)
					local coorb = coord3d(c_way6.b.x, c_way6.b.y, c_way6.b.z)
					local t = command_x(tool_build_way)			
					local err = t.work(player_x(1), coora, coorb, sc_way_name)
					pot0=1
				}
				if (pot0==1 && pot1==0){
					local t_start = my_tile(c_brge3.a)
					local t_end = my_tile(c_brge3.b)

					t_start.remove_object(player_x(0), mo_label)

					local t = command_x(tool_build_bridge)
					t.set_flags(2)		
					local err = t.work(player_x(1), t_start, t_end, sc_bridge_name)
					pot1=1
				}
				if (pot1==1 && pot2==0){
					local t_tunn = my_tile(start_tunn)
					t_tunn.remove_object(player_x(0), mo_label)						
					local t = command_x(tool_build_tunnel)
					t.set_flags(2)
					t.work(player_x(1), t_tunn, sc_tunn_name)
					pot2=1					
				}
				if (pot2==1 && pot3==0){
					local t_tun = command_x(tool_build_tunnel)
					local c_start = c_tunn2.a 
					local c_end = coord3d(c_tun_list[0].x, c_tun_list[0].y, start_lvl_z)
					t_tun.work(player_x(1), c_start, c_end, sc_tunn_name)

					local c_list =	c_tun_list
					local t_start = my_tile(start_tunn)
					for(local j = 0; j<(c_list.len()-1);j++){
						local c = coord3d(c_list[j].x, c_list[j].y, (t_start.z+j))
						t_tun.work(player_x(1), t_start, c, sc_tunn_name)
						command_x.set_slope(player_x(1), c, slope.all_up_slope)
					}
					t_tun.work(player_x(1), c_start, c_tunn2.b, sc_tunn_name)
				}
				
				return null
				break

			case 9:
				if (pot0==0){
		            for(local j=0;j<c_way_lim1.len();j++){
						if(glsw[j] == 0){
							local tile_a = my_tile(c_way_lim1[j].a)
							local tile_b = my_tile(c_way_lim1[j].b)

							tile_a.find_object(mo_way).unmark()
							tile_b.find_object(mo_way).unmark()

							tile_a.remove_object(player_x(1), mo_label)
							tile_b.remove_object(player_x(1), mo_label)
							
							local coora = coord3d(c_way_list1[j].a.x, c_way_list1[j].a.y, c_way_list1[j].a.z)
							local coorb = coord3d(c_way_list1[j].b.x, c_way_list1[j].b.y, c_way_list1[j].b.z)
							local t = command_x(tool_build_way)
							t.set_flags(2)			
							local err = t.work(player_x(1), coora, coorb, sc_way_name)

							if(j == c_way_lim1.len()-1){
								pot0 = 1
								reset_glsw()
								break
							}							
						}
		            }
				}
				if (pot0==1 && pot1==0){
					for(local j=0;j<signr;j++){
						
						local tile = tile_x(signal[j].coor.x, signal[j].coor.y, signal[j].coor.z)
						local way = tile.find_object(mo_way)
						local rsign = tile.find_object(mo_roadsign)
						local sign = tile.find_object(mo_signal)
						if (sign) {
							tile.remove_object(player_x(1), mo_signal)
						}
						if (rsign){
							tile.remove_object(player_x(1), mo_roadsign)
						}

						local t = command_x(tool_build_roadsign)
						while(true){
							local err = t.work(player_x(1), my_tile(coord(signal[j].coor.x, signal[j].coor.y)), sc_sign_name)
							local ribi = way.get_dirs_masked()
							if (ribi == signal[j].ribi)
								break
						}
					}				
				}
				return null
				break

			case 10:
				if (!cov_sw)
					return 0
				if (coorbord != 0){
					local tile = tile_x(coorbord.x, coorbord.y, coorbord.z)
					local way = tile.find_object(mo_way)
					way.unmark()
					tile.remove_object(player_x(1), mo_label)
				}
				if (pot0==0){
		            for(local j=0;j<c_cate_list1.len();j++){
						if(glsw[j] == 0){		
							local coora = coord3d(c_cate_list1[j].a.x, c_cate_list1[j].a.y, c_cate_list1[j].a.z)
							local coorb = coord3d(c_cate_list1[j].b.x, c_cate_list1[j].b.y, c_cate_list1[j].b.z)
							local t = command_x(tool_build_wayobj)		
							local err = t.work(player_x(1), coora, coorb, sc_caten_name)

							if(j == c_cate_list1.len()-1){
								pot0 = 1
								reset_glsw()
								break
							}					
						}
		            }
				}
				if (pot0==1 && pot1==0){
					local way = my_tile(c_dep3).find_object(mo_way)
					way.unmark()
					local t = command_x(tool_build_wayobj)		
					local err = t.work(player_x(1), my_tile(c_dep3), my_tile(c_dep3), sc_caten_name)
					pot1 = 1
				}
				if (pot1==1 && pot2==0){
					local tile = my_tile(c_dep3)
					tile.remove_object(player_x(0), mo_label) //Elimina texto label
					local t = command_x(tool_build_depot)
					local err = t.work(player_x(0), tile, sc_dep_name)
					pot2=1
				}
				return null
				break
			
			case 11:

				comm_script = true
				local wt = wt_rail
				local pl = player_x(0)
				local c_depot = my_tile(c_dep3)
				comm_destroy_convoy(pl, c_depot) // Limpia los vehiculos del deposito

				local depot = depot_x(c_depot.x, c_depot.y, c_depot.z)

				//Set schedule for all convoys-------------------------------------------------------------
				local sched = schedule_x(wt, [])
				for(local j=0;j<sch_list.len();j++){
					if (j==0)
						sched.entries.append(schedule_entry_x(my_tile(sch_list[j]), loc3_load, loc3_wait))
					else
						sched.entries.append(schedule_entry_x(my_tile(sch_list[j]), 0, 0))
				}

				local cov_nr = d3_cnr
				local name = loc3_name_obj
				local wag_name = sc_wag3_name
				local wag_nr = sc_wag3_nr
				local wag = true
				if (current_cov>ch3_cov_lim3.a && current_cov<ch3_cov_lim3.b){
					for (local j = 0; j<cov_nr;j++){
						if (!comm_set_convoy(0, c_depot, name))
							return 0
						for (local count = 0;count<wag_nr;count++){
							if (!comm_set_convoy(0, c_depot, wag_name, wag))
								return 0
						}

						local convoy = depot.get_convoy_list()
						comm_start_convoy(pl, gl_wt, sched, convoy, depot)
					}			
				}
				comm_script = false

				return null
				break
		}

		return null
	}
	
	function set_all_rules(pl) {
		local forbid =	[	4129,tool_build_way,tool_build_bridge,tool_build_tunnel,tool_build_station,
							tool_remove_way,tool_build_depot,tool_build_roadsign,tool_build_wayobj
						]
		foreach(wt in all_waytypes)
			if (wt != wt_rail){
				foreach (tool_id in forbid)
					rules.forbid_way_tool(pl, tool_id, wt )
			}
		if (this.step!=2 && this.step!=8){
			local forbid = [tool_setslope]
			foreach (tool_id in forbid)
			rules.forbid_tool(pl, tool_id )
		}
			
		local forbid =	[	4134,4135,tool_lower_land,tool_raise_land,tool_restoreslope, tool_add_city,
							tool_make_stop_public,4137,tool_build_transformer,4107,4102,4127,4131
						]
		foreach (tool_id in forbid)
			rules.forbid_tool(pl, tool_id )

		switch (this.step) {
			case 1:
				local forbid=	[	4129,tool_build_way,tool_build_bridge,tool_build_tunnel,tool_build_station,
									tool_remove_way,tool_build_depot,tool_build_roadsign,tool_build_wayobj
								]
				foreach (tool_id in forbid)
					rules.forbid_way_tool(pl, tool_id, wt_rail)

				local forbid = [tool_build_station,tool_remover]
				foreach (tool_id in forbid)
					rules.forbid_tool(pl, tool_id )	
				break

			case 2:
				local forbid = [4129,tool_build_tunnel,tool_build_station,tool_build_depot,tool_build_roadsign,tool_build_wayobj]
				foreach (tool_id in forbid)
					rules.forbid_way_tool(pl, tool_id, wt_rail )

				local forbid = [tool_build_station]
				foreach (tool_id in forbid)
					rules.forbid_tool(pl, tool_id )				
				break

			case 3:
				local forbid =	[	4129,tool_build_way,tool_remove_way,tool_build_roadsign,tool_build_bridge,
									tool_build_tunnel,tool_build_depot,tool_build_roadsign,tool_build_wayobj
								]
				foreach (tool_id in forbid)
					rules.forbid_way_tool(pl, tool_id, wt_rail )
				break

			case 4:
				local forbid =	[	4129,tool_build_bridge,tool_build_tunnel,tool_build_station,
									tool_remove_way,tool_build_roadsign,tool_build_wayobj
								]
				foreach (tool_id in forbid)
					rules.forbid_way_tool(pl, tool_id, wt_rail )
				break

			case 5:
				local forbid =	[	4129,tool_build_way,tool_build_bridge,tool_build_tunnel,tool_build_depot,
									tool_build_station,tool_remove_way,tool_build_roadsign,tool_build_wayobj
								]
				foreach (tool_id in forbid)
					rules.forbid_way_tool(pl, tool_id, wt_rail )

				local forbid = [tool_build_station,tool_remover]
				foreach (tool_id in forbid)
					rules.forbid_tool(pl, tool_id )	
				break

			case 6:
				local forbid = [4129,tool_build_bridge,tool_build_depot,tool_build_roadsign,tool_build_wayobj]
				foreach (tool_id in forbid)
					rules.forbid_way_tool(pl, tool_id, wt_rail )
				break

			case 7:
				local forbid = [tool_build_bridge,tool_build_tunnel,tool_build_roadsign]
				foreach (tool_id in forbid)
					rules.forbid_way_tool(pl, tool_id, wt_rail )
				break

			case 8:
				local forbid =	[	4129,tool_build_roadsign,tool_build_station,
									tool_build_depot,tool_build_roadsign,tool_build_wayobj
								]
				foreach (tool_id in forbid)
					rules.forbid_way_tool(pl, tool_id, wt_rail )

				local forbid = [tool_build_station]
				foreach (tool_id in forbid)
					rules.forbid_tool(pl, tool_id )		
				break

			case 9:
				local forbid = [tool_build_bridge,tool_build_tunnel,tool_build_wayobj,tool_build_station]
					foreach (tool_id in forbid)
						rules.forbid_way_tool(pl, tool_id, wt_rail )
				break	
			case 10:
				local forbid =	[	tool_build_way,tool_build_roadsign,tool_build_bridge,
									tool_build_tunnel,tool_build_station,4113,4129
								]
				foreach (tool_id in forbid)
					rules.forbid_way_tool(pl, tool_id, wt_rail )

				local forbid = [tool_build_station]
				foreach (tool_id in forbid)
					rules.forbid_tool(pl, tool_id )	
				break

			case 11:
				local forbid =	[	tool_build_way,tool_build_roadsign,tool_build_bridge,tool_build_wayobj,
									tool_build_tunnel,tool_build_station,tool_remover,tool_build_depot,4113,4129
								]
				foreach (tool_id in forbid)
					rules.forbid_way_tool(pl, tool_id, wt_rail )


				foreach (tool_id in forbid)
					rules.forbid_tool(pl, tool_id )	
				break		
		}
	}
	function is_stations_building(pl, c_list, st_nr, good)
	{
		local sw = true
		for(local j=0;j<st_nr;j++){
			local tile = my_tile(c_list[j])  //tile_x(c_list[j].x, c_list[j].y, 0)
			local halt = tile.get_halt()
			local build = tile.find_object(mo_building)
			local way = tile.find_object(mo_way)
			if (halt){
				local sw = false
				local st_desc = build.get_desc()
				local st_list = building_desc_x.get_available_stations(st_desc.get_type(), st_desc.get_waytype(), good_desc_x(good))
							
				foreach(st in st_list){
					if (st.get_name() == st_desc.get_name())
						sw = true
				}
				glsw[j] = 1
				//count1=j
				tile.unmark()
				way.unmark()
				tile.remove_object(player_x(pl), mo_label)
				if (sw){

					if(j+1 == st_nr) return true
				}
			}
			else if (sw){
				glsw[j] = 0
				tile.mark()
				way.mark()
				if(j!=0)
					label_x.create(c_list[j], player_x(pl), format(translate("Build station No.%d here!."),j+1))
				sw = false
			}
			else {
				tile.unmark()
				way.unmark()
				tile.remove_object(player_x(pl), mo_label)
			}
		}
		return false
	}
    function get_stations(pos, tool_id, result, good, c_list, siz)
	{
		for(local j=0;j<siz;j++){
			local tile = my_tile(c_list[j])  //tile_x(c_list[j].x, c_list[j].y, 0)
			local halt = tile.get_halt()
			local build = tile.find_object(mo_building)
			local way = tile.find_object(mo_way)
			if(build){
				local st_desc = build.get_desc()
				local st_list = building_desc_x.get_available_stations(st_desc.get_type(), st_desc.get_waytype(), good_desc_x(good))
				local sw = false	
				foreach(st in st_list){
					if (st.get_name() == st_desc.get_name()){
						sw = true
						break
					}
				}
				if(!sw){
					if(tool_id == tool_remover){
						if((pos.x==c_list[j].x)&&(pos.y==c_list[j].y)) return null
					}
					return format(translate("Station No.%d must accept goods"), j+1)+" ("+coord(c_list[j].x, c_list[j].y).tostring()+")."
				}
			}
			if((pos.x==c_list[j].x)&&(pos.y==c_list[j].y)){
				if (tool_id == tool_build_station){
					if(build) return translate("There is already a station.")
					if(glsw[j] == 0 && way.is_marked()){
						way.unmark()
						tile.unmark()
						return null
					}
				}
			}
			else if(!build){
				if (tool_id == tool_build_station)
					return format(translate("Station No.%d here"),j+1)+" ("+coord(c_list[j].x, c_list[j].y).tostring()+")."
			}
		}
		return result
	}
	function train_result_message(nr, name, good, veh, cov, st_t)
	{
		switch (nr) {
			//case 0:
				//return format(translate("You must first buy a locomotive [%s]."),name)
			//	break

			case 0:
				return format(translate("Must choose a locomotive [%s]."),name)
				break

			case 1:
				return format(translate("The number of convoys must be [%d]."),cov)
				break

			case 2:
				return format(translate("The number of convoys must be [%d], press the [Sell] button."),cov)
				break

			case 3:
				return format(translate("All wagons must be for [%s]."),good)
				break

			case 4:
				return format(translate("The number of wagons must be [%d]."),veh-1)
				break

			case 5:
				return  format(translate("The train may not be longer than [%d] tiles."),st_t)
				break

			case 6:
				return  format(translate("The train cannot be shorter than [%d] tiles."),st_t)
				break

			default :
				return translate("The convoy is not correct.")
				break
		}
	}
	function cursor_tile_count(cursor, is_mark, max, pos){
		if(is_mark && cursor_count==0){
			cursor_count = 1
			glpos = coord3d(0,0,0)
			return true
		}
		if(pos.x != glpos.x && pos.y != glpos.y){
			if(!cursor && cursor_count>0)cursor_count++
			glpos = pos
			return true
		}

		if(cursor_count<=max){

			return true
		}
		else glpos = coord3d(0,0,0)
		cursor_count = 0
		return false
	}
}        // END of class

// END OF FILE
