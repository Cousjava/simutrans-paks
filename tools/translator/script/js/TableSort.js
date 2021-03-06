// TableSort 9.2
// Jürgen Berkemeier, 7. 7. 2016
// www.j-berkemeier.de

( function() {	

	"use strict";

	var JB_sortbutStyle = document.createElement('style'); // Stylesheet für Button im TH
	JB_sortbutStyle.innerText = 'button.sortbut { width:100%; height:100%; border: none; background-color: transparent; font: inherit; color: inherit; text-align: inherit; padding: 0; cursor: pointer; } button.sortbut::-moz-focus-inner { margin: -1px; border-width: 1px; padding: 0; }';
	document.head.appendChild(JB_sortbutStyle);

		var JB_Table = function(tab) {
	
		var up = String.fromCharCode(9650);
		var down = String.fromCharCode(9660);
		// var up = String.fromCharCode(8593);
		// var down = String.fromCharCode(8595);
		// var up = String.fromCharCode(11014);
		// var down = String.fromCharCode(11015);
		var no = String.fromCharCode(160,160,160); // Idee: 9674 ???
		var dieses = this;
		var defsort = 0;
		var startsort_u = -1,startsort_d = -1;
		var first = true;
		var ssort;
		var tbdy = tab.getElementsByTagName("tbody")[0];
		var tz = tbdy.rows;
		var nzeilen = tz.length;
		if (nzeilen==0) return;
		var nspalten = tz[0].cells.length;
		var Titel = tab.getElementsByTagName("thead")[0].getElementsByTagName("tr")[0].getElementsByTagName("th");
		var Arr = new Array(nzeilen);
		var ct = 0;
		var sdir = new Array(nspalten);
		var stype = new Array(nspalten); 
		var sortable = new Array(nspalten); 
		for(var i=0;i<nspalten;i++) { 
			stype[i] = "n";
			sdir[i] = "u";
			sortable[i] = false;
		}
	
		var initTableHead = function(t,nr) {
			var b = document.createElement("button");
			b.type = "button";
			b.className = "sortbut"
			b.innerHTML = t.innerHTML;
			t.innerHTML = "";
			if(window.addEventListener) b.addEventListener("click",function() { dieses.sort(nr); },false);
			//b.title = 'Die Tabelle nach "'+b.textContent+'" sortieren.';
			t.appendChild(b);
			sortsymbol.init(t,no);
			if(t.className.indexOf("vorsortiert-")>-1) {
				sortsymbol.set(t,down);
				ssort = nr;
			}
			else if(t.className.indexOf("vorsortiert")>-1) {
				sortsymbol.set(t,up);
				ssort = nr;
			}
			if(t.className.indexOf("sortiere-")>-1) startsort_d=nr;
			else if(t.className.indexOf("sortiere")>-1) startsort_u=nr;
			sortable[nr] = true;
		} // initTableHead
    
		var sortsymbol = {
			init: function(t,s) {
				var tt = t.querySelector("button");
				var sp = tt.getElementsByTagName("span");
				for(var i=0;i<sp.length;i++) {
					if(!sp[i].hasChildNodes()) {
						t.sym = sp[i].appendChild(document.createTextNode(s));
						break;
					}
				}
				if(typeof(t.sym)=="undefined") t.sym = tt.appendChild(document.createTextNode(s));
			},
			set: function(t,s) {
				t.sym.data = s;
			},
			get: function(t) {
				return t.sym.data;
			}
		} // sortsymbol

		var VglFkt_s = function(a,b) {
			var as = a[ssort], bs = b[ssort];
			var ret=(as>bs)?1:(as<bs)?-1:0;
			if(!ret && ssort!=defsort) {
				if (stype[defsort]=="s") { as = a[defsort]; bs = b[defsort]; ret = (as>bs)?1:(as<bs)?-1:0; }
				else ret = parseFloat(a[defsort])-parseFloat(b[defsort])
			}
			return ret;
		} // VglFkt_s

		var VglFkt_n = function(a,b) {
			var ret = parseFloat(a[ssort])-parseFloat(b[ssort]);
			if(!ret && ssort!=defsort) {
				if (stype[defsort]=="s") { var as = a[defsort],bs = b[defsort]; ret = (as>bs)?1:(as<bs)?-1:0; }
				else ret = parseFloat(a[defsort])-parseFloat(b[defsort]);
			}
			return ret;
		} // VglFkt_n

		var convert = function(val,s) {
			var dmy;
			var trmdat = function() {
				if(dmy[0]<10) dmy[0] = "0" + dmy[0];
				if(dmy[1]<10) dmy[1] = "0" + dmy[1];
				if(dmy[2]<10) dmy[2] = "200" + dmy[2];
				else if(dmy[2]<20) dmy[2] = "20" + dmy[2];
				else if(dmy[2]<99) dmy[2] = "19" + dmy[2];
				else if(dmy[2]>9999) dmy[2] = "9999";
			}
			if(val.length==0) val = "0";
			if(!isNaN(val) && val.search(/[0-9]/)!=-1) return val;
			var n = val.replace(",",".");
			if(!isNaN(n) && n.search(/[0-9]/)!=-1) return n;
			n = n.replace(/\s|&nbsp;|&#160;|\u00A0/g,"");
			if(!isNaN(n) && n.search(/[0-9]/)!=-1) return n;
			if(!val.search(/^\s*\d+\s*\.\s*\d+\s*\.\s*\d+\s+\d+:\d\d\:\d\d\s*$/)) {
				var dp = val.search(":");
				dmy = val.substring(0,dp-2).split(".");
				dmy[3] = val.substring(dp-2,dp);
				dmy[4] = val.substring(dp+1,dp+3);
				dmy[5] = val.substring(dp+4,dp+6);
				for(var i=0;i<6;i++) dmy[i] = parseInt(dmy[i],10);
				trmdat();
				for(var i=3;i<6;i++) if(dmy[i]<10) dmy[i] = "0" + dmy[i];
				return (""+dmy[2]+dmy[1]+dmy[0]+"."+dmy[3]+dmy[4]+dmy[5]).replace(/ /g,"");
			}
			if(!val.search(/^\s*\d+\s*\.\s*\d+\s*\.\s*\d+\s+\d+:\d\d\s*$/)) {
				var dp = val.search(":");
				dmy = val.substring(0,dp-2).split(".");
				dmy[3] = val.substring(dp-2,dp);
				dmy[4] = val.substring(dp+1,dp+3);
				for(var i=0;i<5;i++) dmy[i] = parseInt(dmy[i],10);
				trmdat();
				for(var i=3;i<5;i++) if(dmy[i]<10) dmy[i] = "0"+dmy[i];
				return (""+dmy[2]+dmy[1]+dmy[0]+"."+dmy[3]+dmy[4]).replace(/ /g,"");
			}
			if(!val.search(/^\s*\d+:\d\d\:\d\d\s*$/)) {
				dmy = val.split(":");
				for(var i=0;i<3;i++) dmy[i] = parseInt(dmy[i],10);
				for(var i=0;i<3;i++) if(dmy[i]<10) dmy[i] = "0"+dmy[i];
				return (""+dmy[0]+dmy[1]+"."+dmy[2]).replace(/ /g,"");
			}
			if(!val.search(/^\s*\d+:\d\d\s*$/)) {
				dmy = val.split(":");
				for(var i=0;i<2;i++) dmy[i] = parseInt(dmy[i],10);
				for(var i=0;i<2;i++) if(dmy[i]<10) dmy[i] = "0"+dmy[i];
				return (""+dmy[0]+dmy[1]).replace(/ /g,"");
			}
			if(!val.search(/^\s*\d+\s*\.\s*\d+\s*\.\s*\d+/)) {
				dmy = val.split(".");
				for(var i=0;i<3;i++) dmy[i] = parseInt(dmy[i],10);
				trmdat();
				return (""+dmy[2]+dmy[1]+dmy[0]).replace(/ /g,"");
			}
			stype[s] = "s";
//			return val.toLowerCase().replace(/\u00e4/g,"ae").replace(/\u00f6/g,"oe").replace(/\u00fc/g,"ue").replace(/\u00df/g,"ss");
			return val.toLowerCase().replace(/\u00e4/g,"a ").replace(/\u00f6/g,"o ").replace(/\u00fc/g,"u ").replace(/\u00df/g,"ss");
		} // convert

		this.sort = function(sp) {
			if(sp<0 || sp>=nspalten) return;
			if(!sortable[sp]) return;
			if(typeof(JB_presort)=="function") JB_presort(tab,tbdy,tz,nzeilen,nspalten,ssort);
			if (first) {
				for(var z=0;z<nzeilen;z++) {
					var zelle = tz[z].getElementsByTagName("td"); // cells;
					Arr[z] = new Array(nspalten+1);
//					Arr[z] = new Array(nspalten*2);
					Arr[z][nspalten] = tz[z];
					for(var s=0;s<nspalten;s++) {
						if (zelle[s].getAttribute("data-sort_key")) 
							var zi = convert(zelle[s].getAttribute("data-sort_key"),s);
						else if (zelle[s].getAttribute("sort_key")) 
							var zi = convert(zelle[s].getAttribute("sort_key"),s);
						else 
							var zi = convert(zelle[s].textContent,s);
						Arr[z][s] = zi ;
//						Arr[z][s+nspalten] = zelle[s].innerHTML;
						/* zelle[s].innerHTML += "<br>"+zi; // zum Debuggen   */
					}
				}
				first = false;
			}
			if(sp==ssort) {
				Arr.reverse() ;
				if ( sortsymbol.get(Titel[ssort])==down )
					sortsymbol.set(Titel[ssort],up);
				else
					sortsymbol.set(Titel[ssort],down);
			}
			else {
				if ( ssort>=0 && ssort<nspalten ) sortsymbol.set(Titel[ssort],no);
					ssort = sp;
				if(stype[ssort]=="s") Arr.sort(VglFkt_s);
				else                  Arr.sort(VglFkt_n);
				if(sdir[ssort]=="u") {
					sortsymbol.set(Titel[ssort],up);
				}
				else {
					Arr.reverse() ;
					sortsymbol.set(Titel[ssort],down);
				}
			}
/*			for(var z=0;z<nzeilen;z++) {
				var zelle = tz[z].getElementsByTagName("td"); // cells;
					for(var s=0;s<nspalten;s++) 
						zelle[s].innerHTML = Arr[z][s+nspalten];
			}*/
			for(var z=0;z<nzeilen;z++)
				tbdy.appendChild(Arr[z][nspalten]);
			if(typeof(JB_aftersort)=="function") JB_aftersort(tab,tbdy,tz,nzeilen,nspalten,ssort);
		} // sort

		//if(!tab.title.length) tab.title="Ein Klick auf die Spalten\u00fcberschrift sortiert die Tabelle."; 
		for(var i=Titel.length-1;i>-1;i--) {
			var t=Titel[i];
			if(t.className.indexOf("sortier")>-1) {
				ct++;
				initTableHead(t,i);
				defsort = i ;
				if(t.className.indexOf("sortierbar-")>-1) sdir[i] = "d";
			}
	}
		if(ct==0) {
			for(var i=0;i<Titel.length;i++) 
				initTableHead(Titel[i],i);
			defsort = 0;
		}
		if(startsort_u>=0) this.sort(startsort_u);
		if(startsort_d>=0) { this.sort(startsort_d); this.sort(startsort_d); }
		if(typeof(JB_aftersortinit)=="function") JB_aftersortinit(tab,tbdy,tz,nzeilen,nspalten,-1);

	} // JB_Table

	var JB_initTableSort = function() {
		if (!document.querySelectorAll) return;
		var JB_Tables = [];
		var Sort_Table = document.querySelectorAll("table.sortierbar, table[sortable]");
		for(var i=0;i<Sort_Table.length;i++) JB_Tables.push(new JB_Table(Sort_Table[i]));

		var pars = decodeURI(window.location.search.substring(1));
		if(pars.length) { // jbts=((0,1),(10,0),(3,3),(2,2))   tnr,snr
			pars = pars.replace(/\s/g,"");
			pars = pars.match(/jbts=\(?(\(\d+,\d+\),?){1,}\)?/gi); 
			if(pars) {
				pars = pars[0].substr(pars[0].search("=")+1); 
				pars = pars.replace(/\(\(/g,"(").replace(/\)\)/g,")").replace(/\)\(/g,")|(").replace(/\),\(/g,")|("); 
				pars = pars.split("|");
				for(var i=0;i<pars.length;i++) {
					var p = pars[i].substring(1,pars[i].length-1).split(","); 
					if(p[0]>-1&&p[0]<JB_Tables.length) JB_Tables[p[0]].sort(p[1]);
				}
			}
		} 
	} // initTableSort

	if(window.addEventListener) window.addEventListener("DOMContentLoaded",JB_initTableSort,false);

})();