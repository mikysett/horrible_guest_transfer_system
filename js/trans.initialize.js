// Funzioni generiche
// Inizializziamo le variabili globali
// Parametriamo la vista e le operazioni in funzione dell'utilizzatore loggato

/* transURL - URL for updating trans */
var transURL = "trans.php";
var last_update = 0;
var isFocused = true; // se la finestra è in primo piano
var a_p = ["arr", "par"];
var mesi = ["GEN", "FEB", "MAR", "APR", "MAG", "GIU", "LUG", "AGO", "SET", "OTT", "NOV", "DIC"];
var gg = ["Lu", "Ma", "Me", "Gi", "Ve", "Sa", "Do"];
var gg_long = ["LUNEDI", "MARTEDI", "MERCOLEDI", "GIOVEDI", "VENERDI", "SABATO", "DOMENICA"];

var colspan_com = [13, 6, 5, 4, 4, 9, 9, 9];

var trans_view_exc = [];
var tipo_lista = 0;

var tipo_trans = ["PERSONE", "MERCE", "IN ESCLUSIVA", "DIPENDENTI", "VILLE", "PROPRIETARI", "RISTORANTE", "ALTRO"];
var barche = ["", "FABIR3", "<i>FABIR4</i>", "ROTATION", "B. PRIVE", "ELICOMPANY", "HELISUD", "ELICO", "ALTRO"];
var barche_nostyle = ["", "FABIR3", "FABIR4", "ROTATION", "B. PRIVE", "ELICOMPANY", "HELISUD", "ELICO", "ALTRO"];
var num_barche = barche.length;
var stato_mezzi = ['NON DISPONIBILE', 'FERMA A CAVALLO', 'FERMA A ST. TE.', 'FERMA A PIANTA.', 'FERMA',
						'PARTITA DA CAVALLO', 'PARTITA DA ST. TE.', 'PARTITA DA PIANTA.', 'PARTITA', 'FUORI USO'];
var num_stato_mezzi = stato_mezzi.length;
var mezzo_viaggi = 0; // 0 = fabir 3 e 4, 1 = fabir 3, 2 = fabir 4
var mezzi_data = [];
var porti = ["", "CAV", "PIANT", "<b>ST.&nbsp;TE</b>", "ALTRO"];
var taxi = ["", "MASSIMI", "TOMMASO", "AUTO PROPRIA", "ALTRO"];
var stato_trans = ["", "<span class=\"bohboh\">??</span>", "<span class=\"boh\">?</span>", "", ""];

/* variables that establish how often to access the server */
var updateInterval = 10000; // how many milliseconds to wait to get new trans
// when set to true, display detailed error trans
var debugMode = true;

function print_reparti(comm) {
	var reparti = "";
	
	if(comm['tutti'] == 1)		reparti += '<span class="comm_rep">TUTTI</span>';
	if(comm['reception'] == 1)	reparti += '<span class="comm_rep">RECEP</span>';
	if(comm['governante'] == 1)reparti += '<span class="comm_rep">GOV</span>';
	if(comm['ristorante'] == 1)reparti += '<span class="comm_rep">RISTO</span>';
	if(comm['facchini'] == 1)	reparti += '<span class="comm_rep">FACC</span>';
	if(comm['barca'] == 1)		reparti += '<span class="comm_rep">BARCA</span>';
	if(comm['taxi'] == 1)		reparti += '<span class="comm_rep">TAXI</span>';
	if(comm['cliente'] == 1)	reparti += '<span class="comm_rep">CLIENTE</span>';
	
	return reparti;
}

// function that displays an error trans
function displayError(trans) 
{
    // display error trans, with more technical details if debugMode is true
    alert("Error accessing the server! " +
                 (debugMode ? trans : ""));
}

// function that displays a PHP error trans
function displayPHPError(error)
{
  displayError ("Error number :" + error.errno + "\r\n" +
              "Text :"+ error.text + "\r\n" +
              "Location :" + error.location + "\r\n" +
              "Line :" + error.line + + "\r\n");
}

function retrieveNewTrans(timeOut=true, param=false, modeXHR='RetrieveNew') 
{
  	// Settiamo i parametri standard
  	var parametri = {
   	mode: modeXHR,
   	date_list: Trans[ts.valueOf()].ts,
   	last_update: Trans[ts.valueOf()].ts_update
	};
	// Se ci sono parametri aggiuntivi li aggiungiamo ai parametri da inviare
	if(param != false) parametri.extra = param;
	console.log(parametri);
    $.ajax({
        url: transURL,
        type: 'POST',
        data: $.param(parametri),
        dataType: 'json',
        error: function(xhr, textStatus, errorThrown) {
            displayError(textStatus);
        },
        success: function(data, textStatus) {
            if(data.errno != null)
              displayPHPError(data);
            else {
            	// Se c'è un'operazione chiamiamo la funzione per mostrare le operazioni
            	if(data.op != null) showOp(data.op);
            	
            	// Se il transfer che stiamo modificando va aggiornato perchè cambiato nel frattempo
            	if(modeXHR == 'CheckTrans') {
            		// Se ci sono aggiornamenti sul trans
            		if(data.info != null) {
            			var trans_up = Trans[ts.valueOf()];
            			var transInfo = data.info;
            			
	            		// Aggiorniamo il trans nella nostra lista di dati
	            		lista = transInfo[0];
							pos = trans_up.transId(lista, transInfo[1]);
							
							trans_up.list[lista][pos] = data.transUpdated;
							
	            		// Aggiorniamo la finestra editTrans
		   				trans_up.editTrans(data.info, "refreshTrans");
	   				}
            	}
            	
            	// Se abbiamo solo eliminato un commento non aggiorniamo la lista perché non è stata rilavorata in background
            	else if(modeXHR == 'RetrieveNew') {
	            	// Aggiorniamo i dati e il last update dell'oggetto
	            	Trans[ts.valueOf()].update(data);
	            	
	            	// Aggiorniamo lo stato dei mezzi se la variabile esiste
	            	if(data.mezzi != null) {
	            		updateMezzi(data.mezzi);	
	            	}
            	}
             }
            
            // restart sequence
            if(timeOut == true && isFocused == true)
            	setTimeout("retrieveNewTrans();", updateInterval);
        }
    });
}

function showOp(op=null) {
	
}

function updateMezzi(mezzi, repeat=true) {
	
	// Se è la prima volta che prendiamo i mezzi
	if(mezzi_data.length == 0) {
		mezzi_data = mezzi;
		
		// Stampiamo lo stato dei mezzi che verrà richiamato a intervalli
		refreshMezzi();
	}
	
	// Se invece i mezzi vanno aggiornati
	else {
		var num_mezzi = mezzi.length;
		var num_mezzi_data = mezzi_data.length;
		
		for(var i = 0 ; i < num_mezzi ; i++) {
			var id = mezzi[i]["mezzo"];
			
			for(var j = 0 ; j < num_mezzi_data ; j++) {
				// Se il mezzo già esiste e va aggiornato
				if (id == mezzi_data[j]["mezzo"]) {
					mezzi_data[j] = mezzi[i];
					break;
				}
			}
			
			// Se il mezzo non è stato trovato nei mezzi già presenti lo si inserisce a fine tabella
			if(j == num_mezzi) mezzi_data.push(mezzi[i]);
		}
		
		// Stampiamo lo stato dei mezzi senza richiamarlo puntualmente (già richiamato dal primo call)
		refreshMezzi(false);
	}
}

function refreshMezzi(repeat=true) {
	var num_mezzi = mezzi_data.length;
	var time_now = new Date();
	var time_now = time_now.getTime()/1000;
	
	for(var i = 0 ; i < num_mezzi ; i++) {
		var id = mezzi_data[i]["mezzo"];
		var stato_mezzo = $("span#stato_mezzo"+id);
		
		// Ripuliamo lo stato del mezzo prima di ricrearlo
		stato_mezzo.children().detach().remove();
		
		var info_mezzo = "<span class=\"m_all m_"+mezzi_data[i]["stato"]+"\">"+stato_mezzi[mezzi_data[i]["stato"]];
		
		// Se bisogna mostrare da quanti minuti è partita la barca
		if(mezzi_data[i]["timestamp_stato"]) {
			var num_min = Math.floor((time_now - mezzi_data[i]["timestamp_stato"])/60);
			info_mezzo += "<span class=\"ora_stato\" id=\"ts_mezzo"+mezzi_data[i]["stato"]+"\">";
			info_mezzo += num_min+" min";
			info_mezzo += "</span>";
		
		}
		info_mezzo += "</span>";
		
		$("span#stato_mezzo"+id)[0].innerHTML = info_mezzo;
	}
	
	// Autoupdate il numero di minuti
	if(repeat == true) setTimeout("refreshMezzi();", 60000);
}

// Stampa la data secondo il formato e nell'id o classe richiesta
function setDateString(ts, id, format) {
	/* Formats :
	1: dd-mm-YYYY
	
	*/
	if(format == 1) {
		$(id).val(ts.format("DD/MM/YYYY"));
		
		// Se stiamo visualizzando la data principale modifichiamo anche il giorno della settimana
		if(id == "#date_list") {
			
			$("#ts_day_week")[0].innerHTML = gg[ts.isoWeekday()-1];
		}
	}
}

function setDay(date_str) {
	var date_str = date_str;
	if(date_str == "") return null;
	
	date_str = date_str.replace(/\.|\,|;|-|_/g, "/");
	var day_array = date_str.split("/");
	
	var num_day = day_array.length;

	// Se non esiste nemmeno la prima entrata o non è un int ritorniamo errore
	if(num_day > 0 && !isNaN(day_array[0])) {
		
		// Se il mese non è stato inserito o non è un int si inserisce il mese corrente
		if(num_day == 1 || isNaN(day_array[1])) day_array[1] = ts.format("M");
		else 												 day_array[1] = parseInt(day_array[1]);
		
		// Se l'anno non è stato inserito o non è un int si inserisce l'anno corrente
		if(num_day < 3 || isNaN(day_array[2])) {
			
			// Se l'anno non è stato inserito l'anno di default è quello attuale
			day_array[2] = ts.format("YYYY");
		}

		return moment.tz(day_array[2]+"-"+day_array[1]+"-"+day_array[0]+" 00:00", "YYYY/M/D", "Europe/Rome");
	}
	
	return null;
}

// Formatta una string per visualizzarla correttamente
function format_v(str) {
	// Operazioni sulle stringhe
	
	return str;
}

function str_show(str) {
	// Operazioni sulle stringhe
	
	return str;
}

// Per cancellare un elemento
function elimina(e) {
	var element = $(e).val().split("_");
	
	// Passiamo i parametri alla chiamata ajax
	var param = {
	"type" : element[0] }; // per esempio: clxnota
	
	// Se si tratta di eliminare un commento di un transfer
	if(element[0] == "clxcomm") {
		var modeXHR = "RemoveComm";
		
		param["data"] = {
			"id":element[1],
			"tipo":element[2],
			"idTrans":element[3]
		};
		
		// Eliminiamo graficamente il commento e poi lo cancelliamo anche dal DOM
		var commCLX = $(e).parent();
		commCLX.hide("slide", 200, function () {
			commCLX.detach().remove();
		});
	}
	
	// Se si tratta di eliminare una nota giornaliera
	else if(element[0] == "clxnota") {	// Passiamo i parametri alla chiamata ajax
		var modeXHR = "RetrieveNew";
	
		param["data"] = element[1]; // id dell'elemento
	}
	
	retrieveNewTrans(false, param, modeXHR);
}
