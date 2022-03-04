// Oggetto Trans

// Inizializza l'oggetto
function Trans(ts) {
	this.ts = ts;
	this.ts_update = 0;
	this.ts_list = {"arr":0, "par":0};
	this.note_gg = null;
	
	// operazioni per visualizzazione :
	// 0 : nessun aggiornamento da fare
	// 1 : Prima volta che si mostrano i trans per questo ts
	// 2 : Si devono aggiornare i trans per questo ts
	this.refresh = {"arr":0, "par":0, "note_gg":0};
	
	this.list = {"arr":null, "par":null};
	this.viaggi = [];
	
	this.num = {"arr":0, "par":0, "note_gg":0, "viaggi":0};
	
	this.tot_trans = {"arr":0, "par":0};
	this.tot_ad = {"arr":0, "par":0};
	this.tot_bam = {"arr":0, "par":0};
}

// Analizza la risposta della chiamata AJAX e aggiorna dati e visualizzazione
Trans.prototype.update = function(data) {
	this.ts_update = data.last_update;

	// Settiamo le variabili undefined su null
	this.setUndefined(data);
	
	// Se ci sono dei tranfer eliminati da cancellare
	if(data.clx != null) {
		clx = data.clx;
		var num_clx = clx.length;
		
		for(var s = 0 ; s < num_clx ; s++) {
			var list = clx[s].list;
			var id = clx[s].id;
			
			// Se si tratta di eliminare un transfer
			if(list == "arr" || list == "par") {
				var trans_clx = this.transId(list, id);
				if(trans_clx != null) {
					this.list[list].splice(trans_clx, 1);
					this.num[list]--;
				}
			}
			// Se si tratta di eliminare una nota giornaliera
			else if(list == "nota_gg") {
				for(var i = 0 ; i < this.num["note_gg"] ; i++) {
					if(id == this.note_gg[i].id) {
						this.note_gg.splice(i, 1);
						this.num["note_gg"]--;
					}
				}
			}
		}
	}
	
	// Per gli arrivi e partenze
	var data_trans = data.trans_arr;
	
	// Trattiamo i dati in un loop per arrivi e partenze
	for(var i = 0 ; i < 2 ; i++) {
		if(data_trans != null) {
			
			// Se la lista ancora non esiste la salviamo nell'oggetto
			if(!this.list[a_p[i]]) {
				this.list[a_p[i]] = data_trans;
				
				// Si definisce l'operazione di visualizzazione per mostrare per la prima volta i trans
				this.refresh[a_p[i]] = 1;
			}
			// Apportiamo le modifiche alla lista già esistente
			else {
				var num_new_trans = data_trans.length;
				for(var j = 0 ; j < num_new_trans ; j++) {
					var new_id = data_trans[j].id;
					
					// Cerchiamo l'id del trans nella nostra lista per vedere se esiste
					var trans_old = this.transId(a_p[i], new_id);
					
					if(a_p[i] == "arr") var ora_sort = "ora_barca_"+a_p[i]+"_cal";
					else 					  var ora_sort = "ora_barca_"+a_p[i];
					
					// Se il trans esiste già nel nostro oggetto e la sua posizione nella lista non è cambiata
					if(trans_old != null && this.list[a_p[i]][trans_old][ora_sort] == data_trans[j][ora_sort]) {
						this.list[a_p[i]][trans_old] = data_trans[j];
					}
					
					// Se il trans non esiste ancora o la sua posizione è cambiata aggiungiamo il transfer alla lista nella posizione corretta
					else {
						// Se il trans già esiste ma ha cambiato posizione nella tabella lo eliminiamo per reinserirlo nella posizione corretta
						if(trans_old != null) {
							this.list[a_p[i]].splice(trans_old, 1);
							this.num[a_p[i]]--;
						}
						
						var ora_barca = data_trans[j][ora_sort];
						
						for(var z = 0 ; z < this.num[a_p[i]] ; z++) {
							// Se siamo all'inizio dell'array e il primo transfer è già all'ora uguale o inferiore
							if(z == 0 && ora_barca <= this.list[a_p[i]][z][ora_sort]) {
								this.list[a_p[i]].splice(z, 0, data_trans[j]);
								break;
							}
							
							// Se siamo alla fine dell'array o se l'orario di arrivo è superiore inseriamo la barca
							else if(z + 1 == this.num[a_p[i]] || // Fine della tabella
									((ora_barca >= this.list[a_p[i]][z][ora_sort]) && (ora_barca <= this.list[a_p[i]][z+1][ora_sort]))
																	) {
								this.list[a_p[i]].splice((z+1), 0, data_trans[j]);
								break;
							}
						}
					}
				}
				
				// Si definisce l'operazione di visualizzazione per aggiornare la visualizzazione dei trans
				this.refresh[a_p[i]] = 2;
			}
			
			// Aggiorniamo il numero di trans per il tipo scelto (arr o par)
			this.num[a_p[i]] = this.list[a_p[i]].length;
			
			// Aggiorniamo la data di ultima modifica per la lista scelta
			this.set_ts_update(a_p[i]);
		}
		
		// Modifichiamo il tipo di trans analizzati, da arrivi a partenze
		data_trans = data.trans_par;
	}
	
	// Se ci sono note giornaliere
	if(data.note_gg != null) {
		// Se la lista ancora non esiste la salviamo nell'oggetto
		if(!this.note_gg) {
			this.note_gg = data.note_gg;
			
			this.num["note_gg"] = this.note_gg.length;
		}
		// Apportiamo le modifiche alla lista già esistente
		else {
			var note_extra = data.note_gg;
			var num_note_extra = note_extra.length;
			
			for(var i = 0 ; i < num_note_extra ; i++) {
				this.note_gg.push(note_extra[i]);
			}
			this.num["note_gg"] += num_note_extra;
		}
		
		// Se le note cambiano si rivisualizzano tutte
		this.refresh["note_gg"] = 1;
	}
	
	// Se un elemento qualsiasi che potrebbe avere un qtip è cambiato li eliminiamo per evitare errori
	if(this.refresh["arr"] == 1 || this.refresh["par"] == 1 || this.refresh["note_gg"] == 1 || data.clx != null) {
		// Eliminiamo i qtip già utilizzati e creati dal plugin nel DOM
		$("body").children('div[id^="qtip"]').detach().remove();
	}

	// Aggiorniamo la visualizzazione per le liste arrivi e liste partenze
	this.refreshTrans();	
	
	// Aggiorniamo la visualizzazione
	this.refreshNote();
	
	// Ripuliamo la pagina se non ci sono transfer o note per la data scelta
	if(this.num["note_gg"] == 0) {
		$("#note_gg_box").children("button.xqtip,span").detach().remove();
		$("#note_gg_box").hide();
	}
	
	if(this.num["arr"] == 0 && this.num["par"] == 0) {
		$("#box_arr,#box_par,#box_trans_mezzo").children("table").detach().remove();
	}
	else {
		if(this.num["arr"] == 0) $("#box_arr").children("table").detach().remove();
		if(this.num["par"] == 0) $("#box_par").children("table").detach().remove();
	}
}

// Organizziamo i dati per la lista viaggi
Trans.prototype.updateViaggi = function() {
	var nuovo_viaggio = false;
	this.num["viaggi"] = 0; // Resettiamo il numero di viaggi
	this.viaggi = []; // Resettiamo i viaggi
	
	for(var i = 0 ; i < 2 ; i++) {
		var tipo = a_p[i];
		var num = this.num[tipo];
		
		for(var j = 0 ; j < num ; j++) {
			var trans_sel = this.list[tipo][j];
			
			// Se il mezzo non è uno dei fabirs andiamo al prossimo viaggio
			if(trans_sel["barca_"+tipo] != 1 && trans_sel["barca_"+tipo] != 2) continue;
			
			// Se il transfer è per un viaggio già registrato aggiorniamo il viaggio già registrato
			nuovo_viaggio = true;
			for(var z = 0 ; z < this.num["viaggi"] ; z++) {
				
				// Se l'orario è stato superato si passa direttamente alla creazione di un nuovo viaggio
				if(trans_sel["ora_barca_"+tipo] < this.viaggi[z]["ora"]) break;
				
				// Se il transfer può essere inserito in un viaggio già esistente
				if(trans_sel["ora_barca_"+tipo] == this.viaggi[z]["ora"] &&
					trans_sel["barca_"+tipo] == this.viaggi[z]["barca"] &&
					trans_sel["porto_partenza_"+tipo] == this.viaggi[z]["par"] &&
					trans_sel["porto_arrivo_"+tipo] == this.viaggi[z]["arr"]) {
						
					this.viaggi[z] = addTransInViaggio(this.viaggi[z], trans_sel, tipo);
					
					nuovo_viaggio = false;
					break;
				}
			}
			
			// Se il transfer è su un viaggio non ancora registrato registriamo il nuovo viaggio e lo inseriamo alla posizione giusta
			if(nuovo_viaggio == true) {
				var viaggio_new = {
					"barca":trans_sel["barca_"+tipo],
					"ora":trans_sel["ora_barca_"+tipo],
					"par":trans_sel["porto_partenza_"+tipo],
					"arr":trans_sel["porto_arrivo_"+tipo],
					"nomi":"",
					"pax":0,
					"comm":"",
				};
				// Inseriamo i dati specifici del trans nel viaggio appena creato
				viaggio_new = addTransInViaggio(viaggio_new, trans_sel, tipo);

				// Se non esiste ancora nessun viaggio
				if(this.num["viaggi"] == 0) {
					this.viaggi.push(viaggio_new);
				}

				// Se esistono già altri viaggi inseriamo il nuovo viaggio alla posizione giusta
				else {
					for(var z = 0 ; z < this.num["viaggi"] ; z++) {
						
						// Se siamo all'inizio dell'array e il primo viaggio è già all'ora uguale o inferiore
						if(z == 0 && viaggio_new["ora"] <= this.viaggi[z]["ora"]) {
							this.viaggi.splice(z, 0, viaggio_new);
							break;
						}
						
						// Se siamo alla fine dell'array o se l'orario di arrivo è superiore inseriamo la barca
						else if(z + 1 == this.num["viaggi"] || // Fine della tabella
								((viaggio_new["ora"] >= this.viaggi[z]["ora"]) && (viaggio_new["ora"] <= this.viaggi[z+1]["ora"]))
																) {
							this.viaggi.splice((z+1), 0, viaggio_new);
							break;
						}
						
					}
				}

				this.num["viaggi"]++;
			}
		}
	}
}

// Aggiungiamo il transfer nel viaggio corrispondente
function addTransInViaggio(viaggio, trans_sel, tipo) {
	if(tipo == "arr") var tipoComm = 1; // 1 = commento per gli arrivi
	else 					var tipoComm = 2; // 2 = commento per le partenze
	
	// Se ci sono già viaggi sulla riga inseriamo il separatore "+" tra i due trans
	if(viaggio["nomi"] != "") viaggio["nomi"] += "<span class=\"piu_sep\">+</span>";
	
	viaggio["nomi"] += "<img class=\"img_viaggi\" src=\"./layout/img/trans_"+trans_sel['tipo_transfer']+".png\" />";
	
	viaggio["nomi"] += stato_trans[trans_sel['stato_'+tipo]];
	if(trans_sel['pax_ad'] != 0) viaggio["nomi"] += trans_sel['pax_ad'];
	if(trans_sel['pax_bam'] > 0) viaggio["nomi"] += '+'+trans_sel['pax_bam'];
	viaggio["nomi"] += ' '+format_v(trans_sel['nome']);
	
	viaggio["pax"] += trans_sel['pax_ad'] + trans_sel['pax_bam'];
	
	// Se ci sono commenti li aggiungiamo al viaggio
	if("comm" in trans_sel) {
		var num_comm = trans_sel["comm"].length;
		
		for(var i = 0 ; i < num_comm ; i++) {
			var comm_sel = trans_sel["comm"][i];
			
			// Solo commenti per tutti o per barche e per il tipo di transfer (arr o par) pertinente
			if((comm_sel["tutti"] == 1 || comm_sel["barca"] == 1) &&
				(comm_sel['tipo_commento'] == tipoComm || comm_sel['tipo_commento'] == 0)) {
				// Se ci sono già commenti per il viaggio selezionato inseriamo un separatore
				if(viaggio["comm"] != "") viaggio["comm"] += "<span class=\"comm_sep_viaggi\">&#9679;</span>";
				
				viaggio["comm"] += "<span class=\"comm_viaggi\">";
				viaggio["comm"] += "<span class=\"nome_comm_viaggi\">"+trans_sel["nome"]+" : </span>";
				viaggio["comm"] += trans_sel["comm"][i]["testo"];
				viaggio["comm"] += "</span>";
			}
		}
	}
					
	return viaggio;
}

// Aggiorniamo la data di ultima modifica per la lista
Trans.prototype.set_ts_update = function(tipo) {
	for(var i = 0 ; i < this.num[tipo] ; i++) {
		if(this.ts_list[tipo] < this.list[tipo][i]["data_"+tipo]) {
			this.ts_list[tipo] = this.list[tipo][i]["data_"+tipo];
		}
	}
}

// Settiamo le variabili undefined
Trans.prototype.setUndefined = function(data) {
	for(var i = 0 ; i < 2 ; i++) {
		if(data["trans_"+a_p[i]]) {
			var num_trans = data["trans_"+a_p[i]].length;
			
			for(var j = 0 ; j < num_trans ; j++) {
				var trans_now = data["trans_"+a_p[i]][j];
				
				if(!("link" in trans_now)) trans_now.link = "";
				if(!("tipo_transfer" in trans_now)) trans_now.tipo_transfer = 0;
				if(!("titolo" in trans_now)) trans_now.titolo = 0;
				if(!("nome" in trans_now)) trans_now.nome = "";
				if(!("lingua" in trans_now)) trans_now.lingua = 0;
				if(!("pagamento" in trans_now)) trans_now.pagamento = 0;
				if(!("pax_ad" in trans_now)) trans_now.pax_ad = 0;
				if(!("pax_bam" in trans_now)) trans_now.pax_bam = 0;
				if(!("camera" in trans_now)) trans_now.camera = "";
				if(!("num_tel" in trans_now)) trans_now.num_tel = "";
				if(!("num_tel_sec" in trans_now)) trans_now.num_tel_sec = "";
				if(!("email" in trans_now)) trans_now.email = "";
				if(!("email_sec" in trans_now)) trans_now.email_sec = "";
				if(!("auto_remind" in trans_now)) trans_now.auto_remind = 0;
				if(!("modificabile" in trans_now)) trans_now.modificabile = 0;
				
				// Valori comuni sia agli arrivi che alle partenze
				for(var z = 0 ; z < 2 ; z++) {
					if(!("data_"+a_p[z] in trans_now)) trans_now["data_"+a_p[z]] = null;
					if(!("ora_"+a_p[z] in trans_now)) trans_now["ora_"+a_p[z]] = null;
					if(!("luogo_"+a_p[z] in trans_now)) trans_now["luogo_"+a_p[z]] = "";
					if(!("volo_"+a_p[z] in trans_now)) trans_now["volo_"+a_p[z]] = "";
					if(!("taxi_"+a_p[z] in trans_now)) trans_now["taxi_"+a_p[z]] = 0;
					if(!("ora_taxi_"+a_p[z] in trans_now)) trans_now["ora_taxi_"+a_p[z]] = null;
					if(!("porto_partenza_"+a_p[z] in trans_now)) trans_now["porto_partenza_"+a_p[z]] = 0;
					if(!("barca_"+a_p[z] in trans_now)) trans_now["barca_"+a_p[z]] = 0;
					if(!("porto_arrivo_"+a_p[z] in trans_now)) trans_now["porto_arrivo_"+a_p[z]] = 0;
					if(!("ora_barca_"+a_p[z] in trans_now)) trans_now["ora_barca_"+a_p[z]] = null;
					if(!("ora_barca_"+a_p[z]+"_cal" in trans_now)) trans_now["ora_barca_"+a_p[z]+"_cal"] = null;
					if(!("bagagli_"+a_p[z] in trans_now)) trans_now["bagagli_"+a_p[z]] = 0;
					if(!("camera_"+a_p[z] in trans_now)) trans_now["camera_"+a_p[z]] = 0;
					if(!("time_cam_"+a_p[z] in trans_now)) trans_now["time_cam_"+a_p[z]] = null;
					if(!("stato_"+a_p[z] in trans_now)) trans_now["stato_"+a_p[z]] = 0;
					if(!("operatore_"+a_p[z] in trans_now)) trans_now["operatore_"+a_p[z]] = "";
				}
			}
		}
		
	}
}

// Restituisce la testata della tab per i trans
Trans.prototype.transHead = function(a_p) {
	var headTab = "";
	// Stampiamo la testata degli arrivi
	headTab = "<tr class=\"head_list_trans\">";
	
	// Se la lista è per massimi la stampiamo in francese
	if(tipo_lista == 6) {
		if(a_p == "arr") headTab += "<th>NOM</th><th>ARRIV&Eacute;</th><th>VOL</th><th>HEURE</th><th>TAXI</th><th>HEURE</th><th>BATEAU</th><th>PORT</th><th>HEURE</th>";
		else				  headTab += "<th>NOM</th><th>DEPART</th><th>BATEAU</th><th>HEURE</th><th>TAXI</th><th>HEURE</th><th>ARRIV&Eacute;</th><th>VOL</th><th>HEURE</th>";	
	}
	else {
		if(tipo_lista != 5 && tipo_lista != 7) {
			if(a_p == "arr")	headTab += "<th>A</th>";
			else 					headTab += "<th>P</th>";
		}
		headTab += "<th>NOME</th>";
					
		// MASSIMI TOMMASO E ELICOMPANY non vedranno il numero di camera
		if(tipo_lista != 5 && tipo_lista != 6 && tipo_lista != 7) headTab += "<th class=\"num_cam\">#</th>";
		
		// Specifico agli arrivi
		if(a_p == "arr") {
			if(tipo_lista != 1) {
				headTab += "<th>ARRIVO</th><th>VOLO</th><th>ORA</th><th>TAXI</th><th>ORA</th>";
			}
			
			headTab += "<th>PORTO</th><th>BARCA</th>";
			
			if(tipo_lista == 1) headTab += "<th>ARR</th>";
			else 					  headTab += "<th>ORA</th>";
		}
		// Specifico alle partenze
		else {
			headTab += "<th>PARTENZA</th>";
			headTab += "<th>BARCA</th>";
						
			if(tipo_lista != 1) headTab += "<th>ORA</th>";
			else 					  headTab += "<th>PART</th>"; // Per i facchini
			
			if(tipo_lista != 1) headTab += "<th>TAXI</th><th>ORA</th><th>ARRIVO</th><th>VOLO</th><th>ORA</th>";
		}
		
		if(tipo_lista != 1 && tipo_lista != 5 && tipo_lista != 7)
			headTab += "<th><span class=\"print_hide\"></span></th><th><span class=\"print_hide\"></span></th>";
	
	}
	headTab += "</tr>";
	
	return headTab;
}

Trans.prototype.transLines = function(lines_sel, a_p) {
	var lines = "";
	var num_lines = lines_sel.length;
	
	// Per l'esclusione dei commenti
	if(a_p == "arr") var a_p_list = 1;
	else 				  var a_p_list = 2;
	
	this.tot_trans[a_p] = num_lines;
	this.tot_ad[a_p] = 0;
	this.tot_bam[a_p] = 0;
	
	for(var i = 0 ; i < num_lines ; i++) {
		var trans_now = this.list[a_p][lines_sel[i]];
		
		// Aggiorniamo il totale di adulti e bambini
		this.tot_ad[a_p] += trans_now['pax_ad'];
		this.tot_bam[a_p] += trans_now['pax_bam'];
	
	
		lines += '<tr class="riga c_trans_'+trans_now['stato_'+a_p]+'" id="'+a_p+'_'+trans_now['id']+'">';
		if(tipo_lista != 5 && tipo_lista != 6 && tipo_lista != 7)
			lines += '<td><button class="xqtip" value="last_mod_qtip"><img class="tipo_trans_img" src="./layout/img/trans_'+trans_now['tipo_transfer']+'.png"></button></td>';
		
		lines += '<td class="nome_trans">';
		
		// Mostriamo i punti interrogativi se il trans non è sicuro
		lines += stato_trans[trans_now['stato_'+a_p]];
		
		if(trans_now['pax_ad'] != 0) lines += trans_now['pax_ad'];
		if(trans_now['pax_bam'] > 0) lines += '+'+trans_now['pax_bam'];
		lines += ' '+format_v(trans_now['nome']);
		
		// Se siamo sulla lista reception et ci sono dei commenti
		if(tipo_lista == 0 && ("comm" in trans_now)) {
			var num_comm = trans_now.comm.length;
			
			// Se il trans non è inerente alla lista che stiamo formattando lo ignoriamo nel numero totale di trans
			var comm_esclusi = 0;
			for(var z = 0 ; z < num_comm ; z++) {
				if(trans_now.comm[z].tipo_commento != 0 && trans_now.comm[z].tipo_commento != (a_p_list)) comm_esclusi++;
			}
			num_comm -= comm_esclusi;
			
			if(num_comm > 0) lines += '<button class="comm_num xqtip" value="comm_qtip">'+num_comm+'</button>';
		}
		
		lines += '</td>';
		
		if(tipo_lista != 5 && tipo_lista != 6 && tipo_lista != 7) {
			lines += '<td class="imp_td cam_trans">'+format_v(trans_now['camera'])+'</td>';
		}
		
		if(a_p == "arr") {
			if(tipo_lista != 1) {
				lines += '<td>'+format_v(trans_now['luogo_arr'])+'</td>';
				lines += '<td>'+format_v(trans_now['volo_arr'])+'</td>';
				
				if(trans_now['ora_arr'] == null) lines += '<td class="imp_td"></td>';
				else lines += '<td class="imp_td">'+moment(trans_now['ora_arr']*1000).format("HH:mm")+'</td>';
			}
			if(tipo_lista != 1) {
				lines += '<td>'+taxi[trans_now['taxi_arr']]+'</td>';
				lines += '<td class="imp_td">';
				if(trans_now['ora_taxi_arr'] != null)
					lines += moment(trans_now['ora_taxi_arr']*1000).format("HH:mm");
				lines += '</td>';
			}
			
			lines += '<td>'+porti[trans_now['porto_partenza_arr']]+'/'+porti[trans_now['porto_arrivo_arr']]+'</td>';
			lines += '<td>'+barche[trans_now['barca_arr']]+'</td>';
			if(trans_now['ora_barca_arr'] == null) lines += '<td class="imp_td"></td>';
			else if(tipo_lista != 1) {
				lines += '<td class="imp_td">'+moment(trans_now['ora_barca_arr']*1000).format("HH:mm");
				if(trans_now['ora_barca_arr_cal'] != trans_now['ora_barca_arr'])
					lines += '>'+moment(trans_now['ora_barca_arr_cal']*1000).format("HH:mm");
				lines += '</td>';
			}
			
			else { // Per la lista facchini stampiamo unicamente la data di arrivo della barca a Cavallo
				lines += '<td class="imp_td">';
				if(trans_now['ora_barca_arr_cal'] != trans_now['ora_barca_arr'])
					lines += moment(trans_now['ora_barca_arr_cal']*1000).format("HH:mm");
				else 
					lines += '!PAR '+moment(trans_now['ora_barca_arr']*1000).format("HH:mm");
				lines += '</td>';
			}
		}
		
		// Se si tratta di una partenza mostriamo le info nell'ordine appropriato
		else {
			lines += '<td>'+porti[trans_now['porto_partenza_par']]+'/'+porti[trans_now['porto_arrivo_par']]+'</td>';
			lines += '<td>'+barche[trans_now['barca_par']]+'</td>';
			if(trans_now['ora_barca_par'] == null) lines += '<td class="imp_td"></td>';
			else if(tipo_lista != 1) {
				lines += '<td class="imp_td">'+moment(trans_now['ora_barca_par']*1000).format("HH:mm");
				if(trans_now['ora_barca_par_cal'] != trans_now['ora_barca_par'])
					lines += '>'+moment(trans_now['ora_barca_par_cal']*1000).format("HH:mm");
				lines += '</td>';
			}
			else { // Per la lista facchini stampiamo unicamente la data di arrivo della barca a Cavallo
				lines += '<td class="imp_td">'+moment(trans_now['ora_barca_par']*1000).format("HH:mm")+'</td>';
			}
			if(tipo_lista != 1) {
				lines += '<td>'+taxi[trans_now['taxi_par']]+'</td>';
				lines += '<td class="imp_td">';
				if(trans_now['ora_taxi_par'] != null)
					lines += moment(trans_now['ora_taxi_par']*1000).format("HH:mm");
				lines += '</td>';
				
				lines += '<td>'+format_v(trans_now['luogo_par'])+'</td>';
				lines += '<td>'+format_v(trans_now['volo_par'])+'</td>';
				
				lines += '<td class="imp_td">';
				if(trans_now['ora_par'] != null)
					lines += moment(trans_now['ora_par']*1000).format("HH:mm");
				lines += '</td>';
			}
		}
		
		// Queste info vanno alla fine che sia un arrivo o una partenza
		if(tipo_lista != 1 && tipo_lista != 5 && tipo_lista != 6 && tipo_lista != 7) {
			lines += '<td class="list_img">';
			if(trans_now['email'] != '') lines += '<button class="img_email xqtip" value="email_gtip"><img class="tipo_trans_img" src="./layout/img/email.png"></button>';
			lines +='</td>';
			lines += '<td class="list_img">';
			if(trans_now['num_tel'] != '') lines += '<button class="img_tel xqtip" value="tel_gtip"><img class="tipo_trans_img" src="./layout/img/tel.png"></button>';
			lines +='</td>';
		}
		
		lines += '</tr>';
		
		// Stampa commenti su linea a parte se non si è sulla vista reception
		if(tipo_lista != 0 && ("comm" in trans_now)) {
			var num_comm = trans_now.comm.length;
			
			// Se il trans non è inerente alla lista che stiamo formattando non lo stampiamo
			for(var z = 0 ; z < num_comm ; z++) {
				var comm_sel = trans_now.comm[z];
				if((comm_sel.tipo_commento == 0 || comm_sel.tipo_commento == a_p_list) &&
								  (comm_sel['tutti'] == 1 ||
								  (tipo_lista == 1 && comm_sel['facchini'] == 1) ||
								  (tipo_lista == 5 && comm_sel['barca'] == 1) ||
								  ((tipo_lista == 6 || tipo_lista == 7) && comm_sel['taxi'] == 1))) {
					
					lines += "<tr><td colspan=\""+colspan_com[tipo_lista]+"\" class=\"comm_line\" id=\"comm_"+comm_sel['id']+"\">";
					lines += comm_sel["testo"];
					lines += "</td></tr>";
				}
			}
		}
	}
	
	// Stampiamo a fine tabella i totali dei transfer
	if(num_lines > 0) {
		lines += "<tr><td colspan=\""+colspan_com[tipo_lista]+"\" class=\"tot_lista\">";
		lines += this.tot_trans[a_p]+" transfer - "+this.tot_ad[a_p]+" adulti, "+this.tot_bam[a_p]+" bambini";
		lines += "</td></tr>";
	}
	
	return lines;
}

Trans.prototype.refreshTrans = function() {
			
	// Loop per aggiornare gli arr e le par
	for(var i = 0 ; i < 2 ; i++) {
   	// Se è la prima volta che mostriamo i trans per questo ts (attualmente anche per le modifiche si ristampa tutto)
		if(this.refresh[a_p[i]] > 0) {
			var tab = "";
			var linesTrans = "";
			var num_a_p = this.num[a_p[i]];
			var lines_sel = [];
			
			for(var j = 0 ; j < num_a_p ; j++) {
				var trans_now = this.list[a_p[i]][j];
				
				// Se il transfer non è escuso dall'array in questione lo aggiungiamo alla lista dei trans da vedere
				if($.inArray(trans_now.tipo_transfer, trans_view_exc) == -1
					&& (tipo_lista == 0 // La lista reception non esclude nessun transfer
					|| (tipo_lista == 1) // FACCHINI
					|| (tipo_lista == 5 && trans_now["barca_"+a_p[i]] == 5) // ELICOMPANY
					|| (tipo_lista == 6 && trans_now["taxi_"+a_p[i]] == 1) // MASSIMI
					|| (tipo_lista == 7 && trans_now["taxi_"+a_p[i]] == 2) // TOMMASO
					 )) {
					lines_sel.push(j);
				}
			}
			
			// Formattiamo le linee di trans per la visualizzazione
			if(lines_sel.length > 0)
				linesTrans += this.transLines(lines_sel, a_p[i]);
	
			// display the transfer if there is any
			if(linesTrans != "") {
				var tab = "<table class=\"lista_trans\">";
				tab += this.transHead(a_p[i])+linesTrans;
				tab += "</table>";
			}
			$("#box_"+a_p[i])[0].innerHTML = tab;
		}
		
		// Se bisogna aggiornare i trans per questo ts
		else if(this.refresh[a_p[i]] == 2) {
			// Attualmente tutto è gestito come se fosse una visualizzazione a partire da 0
			
		}
		
	}
	
	// Aggiorniamo le infobolle delle liste solo se ci sono modifiche nei transfer e si sta sulla lista reception
	// Inseriamo anche la possibilità di cliccare sul nome del transfer per modificarlo
	// Aggiorniamo anche i viaggi sulla sinistra
	if(this.refresh["arr"] > 0 || this.refresh["par"] > 0) {		
		// Aggiungiamo l'elemento qtip ai bottoni
		if(tipo_lista == 0) {
			$("tr.riga").has("button.xqtip").each(function() {
				var trans_line = $(this).attr("id").split("_");
				var trans_pos = Trans[ts.valueOf()].transId(trans_line[0], trans_line[1]);
				
				if(trans_pos != null) {
					var trans_sel = Trans[ts.valueOf()].list[trans_line[0]][trans_pos];
					
					if(trans_line[0] == "arr") var a_p_list = 1;
					else 								var a_p_list = 2;
					
					// Aggiungiamo il qtip
					$(this).find(".xqtip").each(function() {
						$(this).qtip(setQtipList($(this), trans_sel, a_p_list));
					});
				}
			});
		
			// Aprirà l'editor dei transfer
			// Diviso in arrivi e partenze per evitare l'apertura di più finestre editor
			if(this.refresh["arr"] > 0) {
				$("#box_arr").find("td.nome_trans").click(function () {
					var trans_line = $(this).parent().attr("id").split("_");
					var trans_pos = Trans[ts.valueOf()].transId(trans_line[0], trans_line[1]);
					
					Trans[ts.valueOf()].editTrans(trans_line);
				});
			}
			if(this.refresh["par"] > 0) {
				$("#box_par").find("td.nome_trans").click(function () {
					var trans_line = $(this).parent().attr("id").split("_");
					var trans_pos = Trans[ts.valueOf()].transId(trans_line[0], trans_line[1]);
					
					Trans[ts.valueOf()].editTrans(trans_line);
				});
			}
		
		}
		
		// Aggiorniamo le liste viaggi
		this.updateViaggi();
		this.refreshViaggi();
	}
	
	// Marchiamo che l'operazione è stata completata
	this.refresh["arr"] = 0;
	this.refresh["par"] = 0;
}

// Per mostrare info supplementari quando si è su alcune parti della riga
function setQtipList(sel, trans_sel, a_p_list=0) {
	
	var tipo_info = sel.val();
	
	var xqtip = {"content":{"text":""},"style":{}};
	
	if(tipo_info == "last_mod_qtip") {
		var ultima_mod = trans_sel["ultima_mod_"+a_p[a_p_list-1]]*1000;
		var op = trans_sel["operatore_"+a_p[a_p_list-1]];
		
		if(op != "")
			xqtip.content.text += " <span class=\"comm_rep\">"+op+"</span> ";
		xqtip.content.text += "<b>Ultima mod.</b> "+moment(ultima_mod).format("DD/MM - HH:mm");
	}
	
	else if(tipo_info == "email_gtip") {
		xqtip.content.text = "<b>Email</b> "+trans_sel["email"];
		if(trans_sel["email_sec"] != "")
			xqtip.content.text += "<b>Email Secondarie</b> "+trans_sel["email_sec"];
	}
	
	else if(tipo_info == "tel_gtip") {
		xqtip.content.text = "<b>Tel</b> "+trans_sel["num_tel"];
		if(trans_sel["num_tel_sec"] != "")
			xqtip.content.text += "<b>Secondari</b> "+trans_sel["num_tel_sec"];
	}
	
	else if(tipo_info == "comm_qtip") {
		var num_comm = trans_sel.comm.length;
		
		xqtip.style.width = 500;
		
		xqtip.content.title = "<b>Commenti Transfer</b>";
		
		xqtip.content.text += "<div class=\"qtip_body_comm\">";
		for(var i = 0 ; i < num_comm ; i++) {
			var comm_sel = trans_sel["comm"][i];
			
			// Nessun controllo sul reparto del commento perchè questo tipo di visualizzazione è disponibile solo per reception
			if((comm_sel['tipo_commento'] == a_p_list || comm_sel['tipo_commento'] == 0)) {
				xqtip.content.text += "<span class=\"comm_time\">"+moment(comm_sel["data"]*1000).format("DD/MM<br />HH:mm")+"</span>";
				xqtip.content.text += comm_sel["testo"];
				xqtip.content.text += "<p class=\"qtip_info_comm\">";
				if(comm_sel["operatore"] != "")
					xqtip.content.text += "<span class=\"comm_rep\">"+comm_sel["operatore"]+"</span> ";
				xqtip.content.text += print_reparti(comm_sel);
				xqtip.content.text += "</p>";
			}
		}
		xqtip.content.text += "</div>";
	}
	
	// Infobolla per dettagli note giornaliere
	else if(tipo_info == "nota_gg_qtip") {
		
		xqtip.content.text += "<div class=\"qtip_body_comm\">";
			
		// Nessun controllo sul reparto del commento perchè questo tipo di visualizzazione è disponibile solo per reception
		xqtip.content.text += "<p class=\"qtip_info_comm\">";
		xqtip.content.text += "<span class=\"comm_time\">"+moment(trans_sel["data_creazione"]*1000).format("DD/MM")+"</span>";
		xqtip.content.text += print_reparti(trans_sel);
		// Da Implementare : eliminare nota !!!!!!!
		xqtip.content.text += "<button onClick=\"elimina(this)\" class=\"clx_nota\" value=\"clxnota_"+trans_sel["id"]+"\">ELIMINA</button>";
		xqtip.content.text += "</p>";
		xqtip.content.text += "</div>";
	}
	
	return xqtip;
}

// Aggiorniamo la visualizzazione per le note giornaliere
Trans.prototype.refreshNote = function() {
	
   // Visualizziamo la lista delle note giornaliere aggiornata
	if(this.refresh["note_gg"] == 1) {		
		var htmlNote = "";
		for(var i = 0 ; i < this.num["note_gg"] ; i++) {
			var nota = this.note_gg[i];
			
			// Se il testo della nota è vuoto significa che la nota è stata cancellata e si passa avanti
			if(nota.testo == "") continue;
			
			if(tipo_lista == 0 // La lista reception non esclude nessun transfer
					|| (tipo_lista == 1 && nota.facchini == 1) // FACCHINI
					|| ((tipo_lista == 2 || tipo_lista == 3 || tipo_lista == 4 || tipo_lista == 5) && nota.barca == 1) // MEZZO DI TRASPORTO
					|| ((tipo_lista == 6 || tipo_lista == 7) && nota.taxi == 1) // MASSIMI e TOMMASO
					 ) {
				
				// Show a separator if there is other notes
				if(htmlNote != "") htmlNote += "<span class=\"note_sep\">&#9679;</span>";
					 	
				htmlNote += "<button class=\"xqtip\" id=\"nota_"+i+"\" value=\"nota_gg_qtip\">";
				htmlNote += nota.testo;
				htmlNote += "</button>";
				
			}
		}
	
		// display the transfer
		$("#note_gg_box")[0].innerHTML = htmlNote;
	}
	
	// Gestione delle infobolle per le note
	if(tipo_lista == 0 && this.refresh["note_gg"] > 0) {
		$("#note_gg_box").find(".xqtip").each(function() {
			// L'id in questione è l'id della notq nell'oggetto e non l'id in MySQL
			var nota_line = $(this).attr("id").split("_");
			
			var nota_sel = Trans[ts.valueOf()]["note_gg"][nota_line[1]];
			
			$(this).qtip(setQtipList($(this), nota_sel));
		});
	}
	
	// Marchiamo che l'operazione è stata completata
	this.refresh["note_gg"] = 0;
	
	// Per evitare di visualizzare una linea gialla vuota nascondiamo l'elemento se non ci sono note
	if($("#note_gg_box").is(":empty")) $("#note_gg_box").hide();
	else 										  $("#note_gg_box").show();
}

// Aggiorniamo la visualizzazione per la lista viaggi
Trans.prototype.refreshViaggi = function() {
	var lines = "";
	var viaggi_show = 0;
	var row_num = 0;
	

	
	for(var i = 0 ; i < this.num["viaggi"] ; i++) {
		var sel = this.viaggi[i];
		
		if(mezzo_viaggi == 0 ||								  // Entrambi i fabirs selezionati
			(mezzo_viaggi == 1 && sel["barca"] == 1) || // Fabir 3
			(mezzo_viaggi == 2 && sel["barca"] == 2)	  // Fabir 4
			) {
			row_num++;
			lines += "<tr class=\"riga_viaggi\">";
			lines += "<td class=\"num_row\">"+row_num+"</td>";
			
			if(mezzo_viaggi == 0) lines += "<td>"+barche[sel["barca"]]+"</td>";
			
			lines += "<td class=\"center\">";
			if(sel["ora"] == null) lines += "??:??";
			else 					 	  lines += moment(sel["ora"]*1000).format("HH:mm");
			lines += "</td>";
			lines += '<td>'+porti[sel["par"]]+'/'+porti[sel["arr"]]+'</td>';
			lines += '<td>'+sel["nomi"]+'</td>';
			if(sel["pax"] == 0) lines += '<td></td>';
			else					  lines += '<td class=\"center\">'+sel["pax"]+'</td>';
			lines += "</tr>";
			
			// Se ci sono dei commenti per il viaggio in questione li stampiamo
			if(sel["comm"]) {
				if(mezzo_viaggi == 0) lines += "<tr><td colspan=\"6\">";
				else 						 lines += "<tr><td colspan=\"5\">"; // Se siamo su una barca specifica c'è la colonna delle barche in meno
				lines += sel["comm"]+"</td></tr>";
			}
		}
	}
	
	// Se ci sono dei viaggi da stampare
	if(lines != "") {
		var tab = "<table id=\"tab_viaggi\">";
		tab += lines;
		tab += "</table>";
		
		$("#box_trans_mezzo")[0].innerHTML = tab;
	}
}

// Cerca un trans nella lista (arr o par, non entrambe) e ritorna il transfer trovato
Trans.prototype.transId = function(tipo, new_id) {
	var num_trans = this.num[tipo];
	
	for(var i = 0 ; i < num_trans ; i++) {
		if(this.list[tipo][i].id == new_id) return i;
	}
	return null;
}

// Fine Oggetto
