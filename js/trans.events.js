$(document).ready(function() 
{
	moment.tz.setDefault("Europe/Rome");
	ts = moment($("#date_list_ts").val()*1000);

	// Tipo di transfer disattivati e rispettivo bottone, 0 di default
	var num_opz = $("#num_hide");
	num_opz.hide();
	
	// Creamo l'oggetto Trans per la data di oggi
	Trans[ts.valueOf()] = new Trans(ts.valueOf());
	
	setDateString(ts, "#date_list", 1);
	
	// Settiamo la lista di stati per i mezzi
	for(var i = 1 ; i <= 2 ; i++) {
		var stati_mezzo = '<div class="stati_mezzo">';
		for(var j = 0 ; j < num_stato_mezzi ; j++) {
			// i = mezzo e j = stato mezzo
			stati_mezzo += '<button class="tipo_stato m_'+j+'" value="'+i+'_'+j+'">'+stato_mezzi[j]+'</button>';
		}
		stati_mezzo += '</div>';
		
		$("#mezzo"+i).append(stati_mezzo);
	}
	
	 // Per cambiare data
    $("#ricerca_data").find("button.change_day").click(
      function(e) {
	      // References to the elements that need to be cleaned
			var note_gg_clx = $("#note_gg_box").children("button.xqtip,span");
			var box_clx = $("#box_arr,#box_par,#box_trans_mezzo").children("table");
			var qtip_clx = $("body").children('div[id^="qtip"]');
			
			// Asynchronously remove the unused elements
			setTimeout(function() {
				note_gg_clx.detach().remove(); // Remove only buttons
				box_clx.detach().remove(); // Remove containers who need refresh
				qtip_clx.detach().remove();
				
				note_gg_clx = null;
				box_clx = null;
				qtip_clx = null;
			}, 100);
      	
      	var new_ts;
      	
      	var attr_id = $(this).attr("id");
      	
      	if(attr_id === "next_day")			ts.add(1, "day");
      	else if(attr_id === "prev_day")	ts.add(-1, "day");
      	
      	else if(attr_id === "set_day") {
      		new_ts = setDay($("#date_list").val());
      		
      		// Se non siamo riusciti a estrapolare una data esciamo dalla funzione senza aggiornare niente
      		if(new_ts == null) return;
      		
      		else ts = new_ts;
      	}
         
         // Aggiorniamo la data visualizzata in alto a destra
         setDateString(ts, "#date_list", 1);
         
         // Se l'oggetto per la data in questione non è ancora stato creato lo creamo
         if(typeof Trans[ts.valueOf()] === "undefined") {
         	Trans[ts.valueOf()] = new Trans(ts.valueOf());
         }
         // Se l'oggetto già esiste aggiorniamo la visualizzazione
         else {
         	Trans[ts.valueOf()].refresh["arr"] = 1;
         	Trans[ts.valueOf()].refresh["par"] = 1;
         	Trans[ts.valueOf()].refresh["note_gg"] = 1;
      	
      		Trans[ts.valueOf()].refreshTrans();
      		Trans[ts.valueOf()].refreshNote();
         }
         
         // Verifichiamo gli aggiornamenti per la data scelta
   		retrieveNewTrans(false);
      }
    );
    
	 // Per mostrare/nascondere tipi di transfer
    $("#trans_type_view button").click(
      function(e) {
      	var trans_type = parseInt($(this).val());
      	
      	// Se si tratta di un tipo specifico non abbiamo bisogno della lista di tutti i bottoni
      	if(trans_type <= 0)	var bottoni = $("#type_view_opz button");
      	
      	// Se vogliamo vedere tutti i tipi di trans
      	if(trans_type == -1) {
      		// Riportiamo la vista di tutti i bottoni in positivo
      		bottoni.removeClass("opz_no");
      		bottoni.addClass("opz_si");
      		
      		// Svuotiamo l'array delle escusioni
      		trans_view_exc = [];
      	}
      	
      	// Se vogliamo rimuovere tutti i tipi di trans
      	else if(trans_type == -2) {
      		// Riportiamo la vista di tutti i bottoni in positivo
      		bottoni.removeClass("opz_si");
      		bottoni.addClass("opz_no");
      		
      		// Inseriamo tutte le escusioni nell'array
      		trans_view_exc = [0,1,2,3,4,5,6,7];
      	}
      	
      	// Se vogliamo vedere/nascondere un tipo di trans specifico
      	else {
				var posizione = $.inArray(trans_type, trans_view_exc);
				
				// Se si vuole mostrare un tipo ora nascosto
				if(posizione != -1) {
					$(this).removeClass("opz_no");
					$(this).addClass("opz_si");
					trans_view_exc.splice(posizione, 1);
				}
				// Se si vuole nascondere un tipo di elemento
				else {
      			$(this).removeClass("opz_si");
					$(this).addClass("opz_no");
					trans_view_exc.push(trans_type);
				}
      	}
      	
      	// Aggiorniamo il numero di elementi nascosti in opzioni
      	var num_hide = trans_view_exc.length;
      	
      	if(num_hide == 0) {
      		num_opz.hide();
      		num_opz[0].innerHTML = "";
      	}
      	else {
      		num_opz[0].innerHTML = num_hide;
      		num_opz.show();
      	}
      	
      	// Aggiorniamo le liste
      	Trans[ts.valueOf()].refresh["arr"] = 1;
      	Trans[ts.valueOf()].refresh["par"] = 1;
      	
      	Trans[ts.valueOf()].refreshTrans();
      }
    );
    
	 // Per definire il tipo di lista
    $("#list_view button").click(
      function(e) {
      	var old_tipo_lista = tipo_lista;
      	tipo_lista = parseInt($(this).val());
      	
      	// Referenza a tutti bottoni
      	var bottoni = $("#list_view").find("button");
      	
      	// Aggiorniamo la visualizzazione dei bottoni
   		bottoni.removeClass("lista_sel");
   		$(this).addClass("lista_sel");
   		
   		// Se passiamo dalla lista barche ad un'altro tipo di lista
   		if(old_tipo_lista == 2 && tipo_lista != 2) {
   			$("div#mezzi_box").removeClass("mezzi_large", 200, function () {
   				// Se le note sono nelle barche le riportiamo alla lista originaria
   				$("#note_gg_box").detach().prependTo("#liste_box");
      			
      			// Aggiorniamo le note dopo la modifica grafica se passiamo da barche a standard
      			Trans[ts.valueOf()].refresh["note_gg"] = 1;
      			Trans[ts.valueOf()].refreshNote();
   				
   				$("div#liste_box").slideDown(200);
   			});
   		}
 	
      	// Aggiorniamo le liste dei transfer solo se si tratta di reception, facchini, elicompany o taxi
      	if(tipo_lista != 2) {
	      	Trans[ts.valueOf()].refresh["arr"] = 1;
	      	Trans[ts.valueOf()].refresh["par"] = 1;
	      	Trans[ts.valueOf()].refreshTrans();
	      	
	      	// Se non abbiamo già aggiornato le note per passaggio da barche a standard
	      	if(old_tipo_lista != 2) {
      			Trans[ts.valueOf()].refresh["note_gg"] = 1;
      			Trans[ts.valueOf()].refreshNote();
	      	}
      	}
      	
      	// Se si tratta della lista barche modifichiamo la visualizzazione
      	// Se siamo già nelle visualizzazione barche non operiamo modifiche
      	else if(old_tipo_lista != 2) {
      		// Mostriamo/nascondiamo le liste sulla sinistra
      		$("div#liste_box").slideUp(200, function() {
      			// Aggiorniamo le note prima della modifica grafica se passiamo da standard a barche
      			Trans[ts.valueOf()].refresh["note_gg"] = 1;
      			Trans[ts.valueOf()].refreshNote();
      			
      			var mezzi_box = $("div#mezzi_box");
      			
      			// Se ci sono delle note giornaliere per le barche le prendiamo e le inseriamo nella lista barche
      			var note_gg_box = $("#note_gg_box");
   				note_gg_box = note_gg_box.detach();
   				note_gg_box.prependTo(mezzi_box);
      			
      			// Ingrandiamo/rimpiccioliamo la lista dei mezzi
      			mezzi_box.addClass("mezzi_large", 200);
      		});
      		

      	}
      }
    );
    
    // Per definire il tipo di tabella per i viaggi
    $("#scelta_mezzo button").click(
      function(e) {
      	// Eliminiamo il focus dal bottone precendende	
      	$(this).parent().find("button.mezzo_focus").removeClass("mezzo_focus");
      	$(this).addClass("mezzo_focus"); // Diamo il focus al nuovo bottone
      	
      	mezzo_viaggi = parseInt($(this).val());
      	Trans[ts.valueOf()].refreshViaggi();
      	
      	var bottoni = $("#type_view_opz button");
      	
      }
    );
    
    // Per visualizzare il menu dei diversi stati dei mezzi
     $("div#mezzo1,div#mezzo2").click(function() {
     	 $(this).children(".stati_mezzo").slideDown(100);
     });

    // Per nascondere il menu dei diversi stati dei mezzi
     $("div#mezzo1,div#mezzo2").mouseleave(function() {
     	 $(this).children(".stati_mezzo").slideUp(100);
     });
    
    // Per cambiare lo stato di un mezzo
    $("div#mezzo1,div#mezzo2").find("button").click(function(event) {
    	// Evitiamo che il click si propaghi al parent (altrimenti il menu verrebbe di nuovo visualizzato)
    	event.stopPropagation();
    	
    	var param = {
    		"type" : "changeMezzo",
    		"data" : $(this).val() };
    	
    	// Nascondiamo il menu
    	$(this).parent().hide("puff", 200);
    	
    	// Aggiorniamo i dati sul server e prendiamo i dati aggiornati
    	retrieveNewTrans(false, param);
    });
    
    // Per la modifica dei numeri di camera come bulk actions
    $("nav#head_menu").find("button#assegna_camere").click(function() {
    	var op = $(this).val();
    	
    	// Se si tratta di inserire i numeri di camera
    	if(op == "assegna") {
    		$(this).val("salva");
    		$(this).text("SALVA CAMERE");
    		
    		$("table.lista_trans").find("td.cam_trans").each(function () {
				var cam = $(this).text();
				
				var trans_line = $(this).parent().attr("id").split("_");
				
				var input = "<input class=\"field_cam\" type=\"text\" name=\""+trans_line[1]+"\" value=\""+cam+"\" />";
				
				$(this)[0].innerHTML = input;
    		});
    	}
    	
    	// Se si tratta di salvare i numeri inseriti
    	else {
    		// Riportiamo il bottone al valore assegna camere
    		$(this).val("assegna");
    		$(this).text("ASSEGNA CAMERE");
    		
    		// Inizializziamo i parametri da inviare nella chiamata XHR
    		var param = {
		    		"type" : "updateCam",
		    		"data" : {} };
		   var i = 0;
    		
    		$("table.lista_trans").find("td.cam_trans").each(function () {
    			var input = $(this).children("input");
    			
    			// Se non ci sono gli input probabilmente si è cambiato pagina mentre si modificavano le camere
    			// Questo controllo evita errori nel codice
    			if(input.length) {
					var trans_line = $(this).parent().attr("id").split("_");
					var cam = input.val();
					
					new_cam = {"id":trans_line[1],"cam":cam};
					
					param["data"][i] = new_cam;
					i++;
					
					input.detach().remove();
					$(this)[0].innerHTML = cam;
				}
    		});
    		
	    	// Aggiorniamo i dati sul server e prendiamo i dati aggiornati
	    	if(i > 0) retrieveNewTrans(false, param);
    	}
    });
    
    // Per aggiungere una nota giornaliera
    $("nav#menu_nav").children("#add_note_img").click(function() {
    	var box_note_gg = "<div id=\"add_note_box\">";
    	
	    	box_note_gg += "<button id=\"hide_note_gg_box\"></button>";
	    	box_note_gg += "<input placeholder=\"NOTA GIORNALIERA\" type=\"text\" name=\"testo_nota_gg\" id=\"add_nota\" value=\"\">";
	    	box_note_gg += "<div id=\"reparti\">";
		    	box_note_gg += "<button class=\"rep_note_gg\" value=\"tutti\">Tutti</button>";
		    	box_note_gg += "<button class=\"rep_note_gg\" value=\"reception\">Reception</button>";
		    	box_note_gg += "<button class=\"rep_note_gg\" value=\"facchini\">Facchini</button>";
		    	box_note_gg += "<button class=\"rep_note_gg\" value=\"barca\">Barca</button>";
		    	box_note_gg += "<button class=\"rep_note_gg\" value=\"taxi\">Taxi</button>";
	    	box_note_gg += "</div>";
	    	box_note_gg += "<div id=\"op_note_gg\">";
		    	box_note_gg += "<button id=\"salva_nota_gg\">Salva</button>";
	    	box_note_gg += "</div>";
    	box_note_gg += "</div>";
    	
    	var box_note = $(box_note_gg).appendTo("nav#menu_nav").show(300);
    	
    	// Diamo il focus al testo
    	$(box_note).children("#add_nota").focus();
    	
    	// Se stiamo selezionando un reparto
    	$(box_note).find("#reparti button").click(function() {
    		if($(this).hasClass("rep_sel")) $(this).removeClass("rep_sel");
    		else 										 $(this).addClass("rep_sel");
    	});
    	
    	// Se stiamo nascondendo il box per le note giornaliere lo eliminiamo anche alla fine del processo
    	$(box_note).children("#hide_note_gg_box").click(function() {
    		$(box_note).hide(200, "swing", function() {$(this).detach().remove();});
    	});
    	
    	// Se stiamo salvando la nota
    	$(box_note).find("#salva_nota_gg").click(function() {
    		// Inizializziamo i parametri da inviare nella chiamata XHR
    		var param = {
		    		"type" : "addNotaGG",
		    		"data" : {} };
		   param["data"]["text"] = $(box_note).children("#add_nota").val();
		   param["data"]["rep"] = [];
		   
		   // Salviamo la nota unicamente se non è vuota, altrimenti non facciamo niente
		   if(param["data"]["text"] != "") {
			   // Inseriamo i reparti selezionati nei parametri
			   var i = 0;
			   $(box_note).find("#reparti button.rep_sel").each(function () {
			   	param["data"]["rep"][i] = $(this).val();
			   	i++;
			   });
	    		
	    		// Nascondiamo e eliminiamo il box prima della chiamata ajax per più di fluidità
	    		$(box_note).hide(200, "swing", function() {$(this).detach().remove();});
	    		
	    		retrieveNewTrans(false, param);
	    	}
    	});
    });
    
    // Se la finestra non è più in primo piano evitiamo di fare chiamate ajax
    $(window).focus(function() {
        isFocused = true;
    	  retrieveNewTrans();
    }).blur(function() {
        isFocused = false;
    });
    
    retrieveNewTrans();
});