// Operazioni possibili solo per gli utilizzatori reception

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
			xqtip.content.text += "<br /><b>Email Secondarie</b> "+trans_sel["email_sec"];
	}
	
	else if(tipo_info == "tel_gtip") {
		xqtip.content.text = "<b>Tel</b> "+trans_sel["num_tel"];
		if(trans_sel["num_tel_sec"] != "")
			xqtip.content.text += "<br /><b>Secondari</b> "+trans_sel["num_tel_sec"];
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

// Stampa la riga per aggiungere un commento
function ge_commenti(tipo_com) {
	// tipo_com : 0 = GENRICI, 1 = ARRIVO, 2 = PARTENZA
	
	var titolo_com = ['GENERICI', 'ARRIVO', 'PARTENZA'];
	var commStr = "";
	
	commStr += '<div class="cont_com">';
	commStr += '<p class="titolo_com"><span class="frmAddComm">Aggiungi commento</span> ';
	
	
	// Inseriamo scelte rapide se non siamo nei generici
	if(tipo_com != 0) {
		commStr += '<span class="com_rapidi_box">';
		commStr += '<span class="com_rapido">';
		commStr += '<input type="checkbox" id="'+tipo_com+'rapid_com0" name="'+tipo_com+'rapid_com0" value="CONFERMA TELEFONICA" />';
		commStr += '<label class="rapid_com" for="'+tipo_com+'rapid_com0"> CONF TEL</label>';
		commStr += '</span>';
		
		commStr += '<span class="com_rapido">';
		commStr += ' <input type="checkbox" id="'+tipo_com+'rapid_com1" name="'+tipo_com+'rapid_com1" value="CONFERMA VIA EMAIL" />';
		commStr += '<label class="rapid_com" for="'+tipo_com+'rapid_com1"> CONF EMAIL</label>';
		commStr += '</span>';
		
		commStr += '<span class="com_rapido">';
		commStr += ' <input type="checkbox" id="'+tipo_com+'rapid_com2" name="'+tipo_com+'rapid_com2" value="LASCIATO MSG IN SEGRETERIA" />';
		commStr += '<label class="rapid_com" for="'+tipo_com+'rapid_com2"> MSG SEG</label>';
		commStr += '</span>';
		
		commStr += '<span class="com_rapido">';
		commStr += ' <input type="checkbox" id="'+tipo_com+'rapid_com3" name="'+tipo_com+'rapid_com3" value="INVIATO EMAIL" />';
		commStr += '<label class="rapid_com" for="'+tipo_com+'rapid_com3"> INV EMAIL</label>';
		commStr += '</span>';
		commStr += '</span>';
	}
	
	commStr += '</p>';
	
	// Inseriamo form per aggiungere commenti
	commStr += '<div class="commento">';
	commStr += '<p class="testata_com">';
	commStr += '<span class="reparti">';
	commStr += ' <input type="checkbox" id="tutti_com' + tipo_com + '" name="tutti_com' + tipo_com + '" value="' + tipo_com + '" />';
	commStr += '<label class="tutti_com" for="tutti_com' + tipo_com + '"> TUTTI</label>';
	commStr += ' <input type="checkbox" id="reception_com' + tipo_com + '" name="reception_com' + tipo_com + '" value="' + tipo_com + '" />';
	commStr += '<label class="reception_com" for="reception_com' + tipo_com + '"> RECEP</label>';
	commStr += ' <input type="checkbox" id="governante_com' + tipo_com + '" name="governante_com' + tipo_com + '" value="' + tipo_com + '" />';
	commStr += '<label class="governante_com" for="governante_com' + tipo_com + '"> GOV</label>';
	commStr += ' <input type="checkbox" id="ristorante_com' + tipo_com + '" name="ristorante_com' + tipo_com + '" value="' + tipo_com + '" />';
	commStr += '<label class="ristorante_com" for="ristorante_com' + tipo_com + '"> RISTO</label>';
	commStr += ' <input type="checkbox" id="facchini_com' + tipo_com + '" name="facchini_com' + tipo_com + '" value="' + tipo_com + '" />';
	commStr += '<label class="facchini_com" for="facchini_com' + tipo_com + '"> FACC</label>';
	commStr += ' <input type="checkbox" id="barca_com' + tipo_com + '" name="barca_com' + tipo_com + '" value="' + tipo_com + '" />';
	commStr += '<label class="barca_com" for="barca_com' + tipo_com + '"> BARCA</label>';
	commStr += ' <input type="checkbox" id="taxi_com' + tipo_com + '" name="taxi_com' + tipo_com + '" value="' + tipo_com + '" />';
	commStr += '<label class="taxi_com" for="taxi_com' + tipo_com + '"> TAXI</label>';
	commStr += ' <input type="checkbox" id="cliente_com' + tipo_com + '" name="cliente_com' + tipo_com + '" value="' + tipo_com + '" />';
	commStr += '<label class="taxi_com" for="cliente_com' + tipo_com + '"> CLIENTE</label>';
	commStr += '</span>';
	commStr += '</p>';
	
	commStr += '<input class="field op_com" placeholder="OP" type="text" name="operatore_com'+tipo_com+'" value="" />';
	commStr += '<input placeholder="COMMENTO" type="text" name="testo_com'+tipo_com+'" class="field testo_com" value="" />';
	commStr += '<div style="clear:both"></div>';

	commStr += '</div>';

	commStr += '</div>';
	
	return commStr;
}

// Ritorna una stringa con i commenti formattati
Trans.prototype.stampaComm = function(transSel, tipo=0) {
	if(tipo == "arr")			 tipo = 1;
	else if(tipo == "par")  tipo = 2;
	var printComm = "";
	
	if("comm" in transSel) {
		var numComm = transSel.comm.length;
		
		for(var i = 0 ; i < numComm ; i++) {
			var commSel = transSel.comm[i];
			if(commSel["tipo_commento"] == tipo) {
				printComm += '<div class="frmComm">';
				
				printComm += '<button onClick="elimina(this)" class="clx_comm" value="clxcomm_'+commSel["id"]+'_'+commSel["tipo_commento"]+'_'+transSel["id"]+'">x</button>';
				
				printComm += '<span class="ora_comm">'+moment(commSel["data"]*1000).format("DD/MM[|]HH:mm")+'</span>';
		
				printComm += '<span class="frmCommReparti">';
				if(commSel['tutti'] == 1)		printComm += 'TUTTI ';
				if(commSel['reception'] == 1)	printComm += 'RECEP ';
				if(commSel['governante'] == 1)printComm += 'GOV ';
				if(commSel['ristorante'] == 1)printComm += 'RISTO ';
				if(commSel['facchini'] == 1)	printComm += 'FACC ';
				if(commSel['barca'] == 1)		printComm += 'BARCA ';
				if(commSel['taxi'] == 1)		printComm += 'TAXI ';
				if(commSel['cliente'] == 1)	printComm += 'CLIENTE ';
				printComm += ' - </span>';
				
				printComm += '<span class="testo_comm">'+commSel["testo"]+'</span>';
				printComm += '</div>';
			}
		}
	}
	
	return printComm;
}

// Finestra di editing dei transfer
// TransInfo[0] = tipo Lista e TransInfo[1] = id trans
// Visual permette di mostrare la versione più recente una volta ricevuta la risposta ajax
// visual = refreshVisual
Trans.prototype.editTrans = function(transInfo=null, visual=null) {
	var transEdit = "";
	var newTrans = true;
	
	// Se il transfer va creato gli diamo i valori di default
	if(transInfo == null) {
		var transSel = {};
		
		transSel['tipo_transfer'] 		 = 0;
		transSel['titolo'] 					 = 0;
		transSel['nome'] 					 = null;
		transSel['pax_ad'] 					 = 2;
		transSel['pax_bam'] 				 = null;
		transSel['camera'] 					 = null;
		transSel['lingua'] 					 = 0;
		
		transSel['auto_remind'] 			 = 1;
		transSel['modificabile'] 			 = 10;
		
		transSel['num_tel'] 				 = null;
		transSel['num_tel_sec'] 			 = null;
		transSel['email'] 					 = null;
		transSel['email_sec'] 				 = null;
		
		transSel['data_arr'] 				 = null;
		transSel['ora_arr'] 				 = null;
		transSel['luogo_arr'] 				 = '';
		transSel['volo_arr']				 = null;
		transSel['taxi_arr']				 = null;
		transSel['ora_taxi_arr']			 = null;
		transSel['porto_partenza_arr']	 = 0;
		transSel['barca_arr']				 = 0;
		transSel['porto_arrivo_arr']		 = 1;
		transSel['ora_barca_arr']			 = null;
		transSel['stato_arr']				 = 1;
		transSel['operatore_arr']			 = null;
		transSel['ultima_mod_arr']		 = null;
		
		transSel['data_par'] 				 = null;
		transSel['ora_par'] 				 = null;
		transSel['luogo_par'] 				 = null;
		transSel['volo_par']				 = null;
		transSel['taxi_par']				 = 0;
		transSel['ora_taxi_par']			 = null;
		transSel['porto_partenza_par']	 = 1;
		transSel['barca_par']				 = 0;
		transSel['porto_arrivo_par']		 = 0;
		transSel['ora_barca_par']			 = null;
		transSel['stato_par']				 = 1;
		transSel['operatore_par']			 = null;
		transSel['ultima_mod_par']		 = null;
	}
	
	// Se il transfer va modificato parametriamo i valori in funzione dell'id
	else {
		newTrans = false;
		lista = transInfo[0];
		pos = this.transId(lista, transInfo[1]);
		transSel = this.list[lista][pos];
		
		// Verifichiamo che non ci sia una versione più recente del transfer
		
		// Passiamo i parametri alla chiamata ajax
		var param = {
		"type" : "modificaTrans", // per exempio: clxnota
		"data" : {
			"id":transSel["id"],
			"ultima_mod_arr":transSel["ultima_mod_arr"],
			"ultima_mod_par":transSel["ultima_mod_par"],
			"lista":lista
			}};
		
		retrieveNewTrans(false, param, 'CheckTrans');
	}
	
	console.log(transSel);
	
		// Inizio formulario
		transEdit += '<div id="box_edit_trans">';
		// class Inner serve come wrap per i bottoni posizionati absolute bottom
		transEdit += '<form name="dati" action="ge_trans.php" method="post" id="form_edit_trans" enctype="multipart/form-data"><div class="inner">';
		
		transEdit += '<div class="title_edit">';
			if(newTrans == false)  transEdit += 'MODIFICA TRANSFER ';
			else 							transEdit += 'NUOVO TRANSFER ';
			
			transEdit += '<select name="tipo_transfer" class="frm_tipo_trans">';
			transEdit += '<option '; if(transSel['tipo_transfer'] == 0) transEdit += 'selected="selected" '; transEdit += 'value="0">CLIENTI HOTEL</option>';
			transEdit += '<option '; if(transSel['tipo_transfer'] == 1) transEdit += 'selected="selected" '; transEdit += 'value="1">MERCE</option>';
			transEdit += '<option '; if(transSel['tipo_transfer'] == 2) transEdit += 'selected="selected" '; transEdit += 'value="2">IN ESCLUSIVA</option>';
			transEdit += '<option '; if(transSel['tipo_transfer'] == 3) transEdit += 'selected="selected" '; transEdit += 'value="3">DIPENDENTI</option>';
			transEdit += '<option '; if(transSel['tipo_transfer'] == 4) transEdit += 'selected="selected" '; transEdit += 'value="4">VILLE</option>';
			transEdit += '<option '; if(transSel['tipo_transfer'] == 5) transEdit += 'selected="selected" '; transEdit += 'value="5">PROPRIETARI</option>';
			transEdit += '<option '; if(transSel['tipo_transfer'] == 6) transEdit += 'selected="selected" '; transEdit += 'value="6">RISTORANTE</option>';
			transEdit += '<option '; if(transSel['tipo_transfer'] == 7) transEdit += 'selected="selected" '; transEdit += 'value="7">ALTRO</option>';
			transEdit += '</select>';
			/* lingue inutili per l'implementazione attuale
			transEdit += '<select name="lingua" class="frm_tipo_trans">';
			transEdit += '<option '; if(transSel['lingua'] == 0) transEdit += 'selected="selected" '; transEdit += 'value="0">FR</option>';
			transEdit += '<option '; if(transSel['lingua'] == 1) transEdit += 'selected="selected" '; transEdit += 'value="1">IT</option>';
			transEdit += '<option '; if(transSel['lingua'] == 2) transEdit += 'selected="selected" '; transEdit += 'value="2">EN</option>';
			transEdit += '</select>'; */
			
		transEdit += '</div>';
		
		transEdit += '<div class="colonna">';
		
		// Info comuni sia all'arrivo che alla partenza
		
		transEdit += '<div class="frm_box_generico">';

		transEdit += '<table>';
				transEdit += '<tr>';
				
					transEdit += '<td colspan="1">';
					transEdit += '<select name="titolo">';
					transEdit += '<option '; if(transSel['titolo'] == 0) transEdit += 'selected="selected" '; transEdit += 'value="0">M.</option>';
					transEdit += '<option '; if(transSel['titolo'] == 1) transEdit += 'selected="selected" '; transEdit += 'value="1">Mme</option>';
					transEdit += '</select>';
					transEdit += '</td>';
					
					transEdit += '<td colspan="2" style="min-width: 230px">';
						transEdit += '<input placeholder="NOME TRANSFER" type="text" name="nome" class="field" value="'+str_show(transSel['nome'])+'" autofocus />';
					transEdit += '</td>';
					transEdit += '<td colspan="1"><input placeholder="ADULTI" type="text" name="pax_ad" class="field" value="'+transSel['pax_ad']+'" /></td>';
					transEdit += '<td colspan="1"><input placeholder="BAMBINI" type="text" name="pax_bam" class="field" value="' + transSel['pax_bam']+'" /></td>';
					transEdit += '<td colspan="1"><input placeholder="CAMERA" type="text" name="camera" class="field" value="' + transSel['camera']+'" /></td>';
				transEdit += '</tr>';
				transEdit += '<tr>';
					transEdit += '<td colspan="3"><input placeholder="EMAIL" type="text" name="email" class="field" value="' + str_show(transSel['email'])+'" /></td>';
					transEdit += '<td colspan="3"><input placeholder="TELEFONO" type="text" name="num_tel" class="field" value="' + str_show(transSel['num_tel'])+'" /></td>';
				transEdit += '</tr>';
				transEdit += '<tr>';
					transEdit += '<td colspan="3"><input placeholder="EMAILS SECONDARIE" type="text" name="email_sec" class="field" value="' + str_show(transSel['email_sec'])+'" /></td>';
					transEdit += '<td colspan="3"><input placeholder="TELEFONI SECONDARI" type="text" name="num_tel_sec" class="field" value="' + str_show(transSel['num_tel_sec'])+'" /></td>';
				transEdit += '</tr>';
			
			transEdit += '</table>';
		transEdit += '</div>'; // Chiude form1_650
		transEdit += '</div>'; // Fine colonna con dati generici
		
		// Colonna in alto a destra con commenti generici
		transEdit += '<div class="colonna frm_col_par">';
		// Stampiamo commenti generici	
		var commGenerici = this.stampaComm(transSel);
		if(commGenerici != "") transEdit += commGenerici; // Commenti già esistenti
		transEdit += ge_commenti(0); // riga per inserire nuovi commenti
		transEdit += '</div>'; // Fine colonna con commenti generici
		
		// Settiamo le variabili per la stampa
		var smp_data = ['',''];
		var smp_luogo = ['',''];
		var smp_taxi = ['',''];
		var smp_mezzo = ['',''];
		
		var tip = 'arr';
		var tipo_nome = 'ARRIVO';
		
		transEdit += '<div id="frm_container_a_p">';
		for(var i = 0 ; i < 2 ; i++) {

			
			if(i == 1) {
				tip = 'par'; tipo_nome = 'PARTENZA';
				transEdit += '<div class="colonna frm_col_par">';
			}
			else {
				transEdit += '<div class="colonna">';
			}
			
			transEdit += '<div class="frm_box_a_p bor_trans_'+transSel['stato_'+tip]+'">';
			
			transEdit += '<p class="titolo bk_trans_'+transSel['stato_'+tip]+'">';
			transEdit += tipo_nome+' ';
			
			transEdit += '<select name="stato_'+tip+'" class="scelta_stato">';
			transEdit += '<option '; if(transSel['stato_'+tip] == 1) transEdit += 'selected="selected" '; transEdit += 'value="1">NON CONFERMATO</option>';
			transEdit += '<option '; if(transSel['stato_'+tip] == 2) transEdit += 'selected="selected" '; transEdit += 'value="2">DA RICONFERMARE</option>';
			transEdit += '<option '; if(transSel['stato_'+tip] == 3) transEdit += 'selected="selected" '; transEdit += 'value="3">CONFERMATO</option>';
			// transEdit += '<option '; if(transSel['stato_'+tip] == 4) transEdit += 'selected="selected" '; transEdit += 'value="4">COMPILATO DA CLIENTE</option>';
			transEdit += '<option '; if(transSel['stato_'+tip] == 0) transEdit += 'selected="selected" '; transEdit += 'value="0">NON PREVISTO</option>';
			transEdit += '</select>';
			
			
			if(newTrans == false) {
				transEdit += ' <span class="data_modifica">mod. il ' +moment(transSel['ultima_mod_'+tip]*1000).format('DD/MM - HH:mm') + '</span></p>';
				
			} // Fine di if modifica_pre
			
			else transEdit += '</p>';
			
			if(transSel['stato_'+tip] == 0) transEdit += '<div class="show_spe">';
			
			transEdit += '<table class="frm_table">';
			
			
			smp_data[i] += '<td colspan="1">';
			if(transSel['data_'+tip] != null) {
				smp_data[i] += '<span class="day_week_ge_trans">'+gg[moment(transSel['data_'+tip]*1000).format('e')]+'</span>';
				smp_data[i] += '<input placeholder="DATA '+tipo_nome+'" type="text" name="data_'+tip+'" class="field data_trans" value="'+moment(transSel['data_'+tip]*1000).format('DD/MM/YYYY')+'" />';
			}	
			else {
				smp_data[i] += '<input placeholder="DATA '+tipo_nome+'" type="text" name="data_'+tip+'" class="field" value="" />';
			}	
			smp_data[i] += '</td>';
			
			smp_luogo[i] += '<td colspan="1">';
			smp_luogo[i] += '<input placeholder="LUOGO ARRIVO" type="text" name="luogo_'+tip+'" class="field" value="'+transSel['luogo_'+tip]+'" />';
			smp_luogo[i] += '</td>';
			smp_luogo[i] += '<td colspan="1">';
			smp_luogo[i] += '<input placeholder="N. VOLO" type="text" name="volo_'+tip+'" class="field" value="'+transSel['volo_'+tip]+'" />';
			smp_luogo[i] += '</td>';
			if(transSel['ora_'+tip] != null && transSel['ora_'+tip] != 0) {
				smp_luogo[i] += '<td><input placeholder="HH" type="text" name="ora_hh_'+tip+'" class="field" value="'+moment(transSel['ora_'+tip]*1000).format('HH')+'" /></td>';
				smp_luogo[i] += '<td><input placeholder="MM" type="text" name="ora_mm_'+tip+'" class="field" value="'+moment(transSel['ora_'+tip]*1000).format('mm')+'" /></td>';
			}
			else {
				smp_luogo[i] += '<td><input placeholder="HH" type="text" name="ora_hh_'+tip+'" class="field" value="" /></td>';
				smp_luogo[i] += '<td><input placeholder="MM" type="text" name="ora_mm_'+tip+'" class="field" value="" /></td>';
			}
			
			smp_taxi[i] += '<tr>';
			smp_taxi[i] += '<td colspan="2"></td>';
			smp_taxi[i] += '<td colspan="1">';
			smp_taxi[i] += '<select name="taxi_'+tip+'">';
			smp_taxi[i] += '<option '; if(transSel['taxi_'+tip] == 0) smp_taxi[i] += 'selected="selected" '; smp_taxi[i] += 'value="0">NO TAXI</option>';
			smp_taxi[i] += '<option '; if(transSel['taxi_'+tip] == 1) smp_taxi[i] += 'selected="selected" '; smp_taxi[i] += 'value="1">MASSIMI</option>';
			smp_taxi[i] += '<option '; if(transSel['taxi_'+tip] == 2) smp_taxi[i] += 'selected="selected" '; smp_taxi[i] += 'value="2">TOMMASO</option>';
			smp_taxi[i] += '<option '; if(transSel['taxi_'+tip] == 3) smp_taxi[i] += 'selected="selected" '; smp_taxi[i] += 'value="3">AUTO PROPRIA</option>';
			smp_taxi[i] += '<option '; if(transSel['taxi_'+tip] == 4) smp_taxi[i] += 'selected="selected" '; smp_taxi[i] += 'value="4">ALTRO</option>';
			smp_taxi[i] += '</select>';
			smp_taxi[i] += '</td>';
			if(transSel['ora_taxi_'+tip] != null && transSel['ora_taxi_'+tip] != 0) {
				smp_taxi[i] += '<td colspan="1"><input placeholder="HH" type="text" name="ora_taxi_hh_'+tip+'" class="field" value="'+moment(transSel['ora_taxi_'+tip]*1000).format('HH')+'" /></td>';
				smp_taxi[i] += '<td colspan="1"><input placeholder="MM" type="text" name="ora_taxi_mm_'+tip+'" class="field" value="'+moment(transSel['ora_taxi_'+tip]*1000).format('mm')+'" /></td>';
			}
			else {
				smp_taxi[i] += '<td colspan="1"><input placeholder="HH" type="text" name="ora_taxi_hh_'+tip+'" class="field" value="" /></td>';
				smp_taxi[i] += '<td colspan="1"><input placeholder="MM" type="text" name="ora_taxi_mm_'+tip+'" class="field" value="" /></td>';
			}
			smp_taxi[i] += '</tr>';

			smp_mezzo[i] += '<tr>';
			smp_mezzo[i] += '<td colspan="1">';
			smp_mezzo[i] += '<select name="barca_'+tip+'">';
			smp_mezzo[i] += '<option '; if(transSel['barca_'+tip] == 0) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="0">NESSUNO</option>';
			smp_mezzo[i] += '<option '; if(transSel['barca_'+tip] == 1) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="1">FABIR 3</option>';
			smp_mezzo[i] += '<option '; if(transSel['barca_'+tip] == 2) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="2">FABIR 4</option>';
			smp_mezzo[i] += '<option '; if(transSel['barca_'+tip] == 3) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="3">ROTATION</option>';
			smp_mezzo[i] += '<option '; if(transSel['barca_'+tip] == 4) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="4">PRIVATO</option>';
			smp_mezzo[i] += '<option '; if(transSel['barca_'+tip] == 5) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="5">ELICOMPANY</option>';
			smp_mezzo[i] += '<option '; if(transSel['barca_'+tip] == 6) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="6">HELISUD</option>';
			smp_mezzo[i] += '<option '; if(transSel['barca_'+tip] == 7) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="7">ELICO</option>';
			smp_mezzo[i] += '<option '; if(transSel['barca_'+tip] == 8) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="8">ALTRO</option>';
			smp_mezzo[i] += '</select>';
			smp_mezzo[i] += '</td>';
			
			smp_mezzo[i] += '<td colspan="1">';
			smp_mezzo[i] += '<select name="porto_partenza_'+tip+'">';
			smp_mezzo[i] += '<option '; if(transSel['porto_partenza_'+tip] == 0) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="0">NESSUNO</option>';
			if(i != 0) // Se è un arrivo togliamo Cavallo
				smp_mezzo[i] += '<option '; if(transSel['porto_partenza_'+tip] == 1) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="1">CAVALLO</option>';
			smp_mezzo[i] += '<option '; if(transSel['porto_partenza_'+tip] == 2) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="2">PIANTARELLA</option>';
			smp_mezzo[i] += '<option '; if(transSel['porto_partenza_'+tip] == 3) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="3">ST. TERESA</option>';
			smp_mezzo[i] += '</select>';
			smp_mezzo[i] += '</td>';
			smp_mezzo[i] += '<td colspan="1">';
			smp_mezzo[i] += '<select name="porto_arrivo_'+tip+'">';
			smp_mezzo[i] += '<option '; if(transSel['porto_arrivo_'+tip] == 0) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="0">NESSUNO</option>';
			
			if(i == 0) // Se è una partenza togliamo Cavallo
				smp_mezzo[i] += '<option '; if(transSel['porto_arrivo_'+tip] == 1) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="1">CAVALLO</option>';
			smp_mezzo[i] += '<option '; if(transSel['porto_arrivo_'+tip] == 2) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="2">PIANTARELLA</option>';
			smp_mezzo[i] += '<option '; if(transSel['porto_arrivo_'+tip] == 3) smp_mezzo[i] += 'selected="selected" '; smp_mezzo[i] += 'value="3">ST. TERESA</option>';
			smp_mezzo[i] += '</select>';
			smp_mezzo[i] += '</td>';
			if(transSel['ora_barca_'+tip] != null && transSel['ora_barca_'+tip] != 0) {
				smp_mezzo[i] += '<td><input placeholder="HH" type="text" name="ora_barca_hh_'+tip+'" class="field" value="'+moment(transSel['ora_barca_'+tip]*1000).format('HH')+'" /></td>';
				smp_mezzo[i] += '<td><input placeholder="MM" type="text" name="ora_barca_mm_'+tip+'" class="field" value="'+moment(transSel['ora_barca_'+tip]*1000).format('mm')+'" /></td>';
			}
			else {
				smp_mezzo[i] += '<td><input placeholder="HH" type="text" name="ora_barca_hh_'+tip+'" class="field" value="" /></td>';
				smp_mezzo[i] += '<td><input placeholder="MM" type="text" name="ora_barca_mm_'+tip+'" class="field" value="" /></td>';
			}
			smp_mezzo[i] += '</tr>';
			
			// Stampiamo la scheda arrivo
			if(i == 0) {
				transEdit += '<tr>';
				transEdit += smp_data[0]+smp_luogo[0];
				transEdit += '</tr>';
				transEdit += smp_taxi[0];
				transEdit += '<tr><td>MEZZO</td></tr>';
				transEdit += smp_mezzo[0];
				
			}
			
			// Stampiamo la scheda partenza
			else {
				transEdit += '<tr>'+smp_data[1]+'<td></td><td></td><td colspan="2">MEZZO</td></tr>';
				transEdit += smp_mezzo[1];
				transEdit += smp_taxi[1];
				transEdit += '<tr><td></td>'+smp_luogo[1]+'</tr>';
			}
			transEdit += '<tr><td></td><td></td><td></td><td></td>';						
			transEdit += '<td colspan="1">';
			transEdit += '<input placeholder="OPERATORE" type="text" name="operatore_'+tip+'" class="field" value="'+transSel['operatore_'+tip]+'" />';
			transEdit += '</td>';
			transEdit += '</tr>';
			
			transEdit += '</table>';
			if(transSel['stato_'+tip] == 0) transEdit += '</div>'; // Si chiude show_spe se il transfer è nascosto
			
			// Stampiamo commenti per qrrivi e partenze	
			var commTipo = this.stampaComm(transSel, tip);
			
			if(commTipo != "") {
				transEdit += commTipo;
			}
			
			// riga per inserire nuovi commenti
			transEdit += ge_commenti(i+1);
			
			transEdit += '</div>'; // Chiudiamo form1_650
			
			transEdit += '</div>'; // Chiudiamo la colonna
		}
		
		transEdit += '<p class="clx_float"></p></div>'; // fine frm_container_a_p

		transEdit += '<div id="pulsanti">';
				
		// Se stiamo operando su una prenotazione già creata
		if(newTrans == false) {
			transEdit += '<p id="data_creazione">Creato il ' +moment(transSel['data_creazione']*1000).format('DD/MM/YYYY[ alle ]HH:mm') + '</p>';
			
			transEdit += '<button class="bottone clx_button">ELIMINA</button> ';
			transEdit += '<button class="bottone" id="annulla">ANNULLA</button> ';
			transEdit += '<input class="bottone" id="duplica" name="duplica" type="submit" value="DUPLICA" /> ';
			transEdit += '<input class="bottone" id="modifica" name="modifica" type="submit" value="MODIFICA" />';
			transEdit += '<input type="hidden" name="id" value="'+transSel['id']+'" />'; // Per poter modificare l'elemento
		}
		else {
			transEdit += '<button class="bottone" id="annulla">ANNULLA</button> ';
			transEdit += '<input class="bottone" id="inserisci" name="inserisci" type="submit" value="INSERISCI" />';
		}
					
		transEdit += '</div>'; // Fine pulsanti_bottom

		transEdit += '</div></form>';
		transEdit += '</div>';
	
	// Inseriamo nel DOM l'interfaccia di edizione dell'infobolla
	
	var daSfocare = $("body").children("div#corpo_a,nav");
	
	// Se stiamo visualizzando l'infobolla
	if(visual == null) {
		transEdit = $(transEdit).prependTo("body");
		
		// Mostriamo l'infobolla e sfochiamo lo sfondo
		daSfocare.addClass("sfoca");
		transEdit.children("#form_edit_trans").show("slide", 200);
	}
	// Se stiamo aggiornando i dati del trans nell'infobolla non facciamo animazioni
	else if(visual == "refreshTrans") {
		$("#box_edit_trans").detach().remove();
		transEdit = $(transEdit).prependTo("body");
		transEdit.children("#form_edit_trans").show();
	}
	
	// Creiamo i listner per le operazioni sui transfer
	
	// Evitiamo l'invio classico del form
	transEdit.find("#pulsanti").find("button,input").click(function (event) {
		event.preventDefault();
		
		var buttonId = $(this).attr("id");
		
		// Se si tratta di duplicare il trans
		if (buttonId == "modifica") {
			var form = document.getElementById("form_edit_trans").elements;
			
			// Passiamo i parametri alla chiamata ajax
			var param = {
			"type" : "modificaTrans", // per exempio: clxnota
			"data" : $(form).serializeArray() }; // id dell'elemento
			
			console.log(param);
			retrieveNewTrans(false, param);
		}
		
		// Nascondiamo il form e torniamo alla lista trans
		transEdit.children("#form_edit_trans").hide("slide", 200, function () {
			daSfocare.removeClass("sfoca");
			transEdit.detach().remove();
		});
	});
	
	// Mostriamo/nascondiamo l'aggiunta di un commento
	transEdit.find("span.frmAddComm").click(function () {
		
		$(this).parent().parent().find(".commento").slideToggle(100);
	});
}
