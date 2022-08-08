function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}

function downloadXLS(filename){
	//var datapesquisa = document.getElementById(dataPesquisaInicial);
    var tableID = "printableTable"
	var tabelarel = document.getElementById(tableID);
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    var ho = String(today.getHours()).padStart(2, '0');
    var mi = String(today.getMinutes()).padStart(2, '0');
    var se = String(today.getSeconds()).padStart(2, '0');

    // Bootstrap configuration classes ["base", "theme", "container"].
    TableExport.prototype.bootstrap = ["btn", "btn-default", "btn-toolbar"];
	//TableExport.prototype.xlsx.buttonContent = 'My Content';
	var table = new TableExport(tabelarel,{
		//filename: "Visitas_"+datapesquisa.value.replaceAll('/', '-'),
		filename: filename+"_"+dd+mm+yyyy+ho+mi+se,
		bootstrap: true,
		exportButtons: false,
        formats: ["xlsx"],
		sheetname: "visitas"
    });

	var XLSX = table.CONSTANTS.FORMAT.XLSX;
	var XLS = table.CONSTANTS.FORMAT.XLS;
	var CSV = table.CONSTANTS.FORMAT.CSV;
	var exportData = table.getExportData();
	var xlsxData = exportData[tableID][XLSX];
	var XLSbutton = document.getElementById('customXLSButton');

	XLSbutton.addEventListener('click', function (e) {
		table.export2file(xlsxData.data, xlsxData.mimeType, xlsxData.filename, xlsxData.fileExtension, xlsxData.merges, xlsxData.RTL, xlsxData.sheetname);
	});

}

function dataParaDate(valor){
    var arrData = valor.split('/');

    var stringFormatada = arrData[1] + '-' + arrData[0] + '-' + arrData[2];
    var dataFormatada = new Date(stringFormatada);
    return dataFormatada;
}



function enableScripts(){
    // a key map of allowed keys
    var allowedKeys = {
        37: 'left',
        38: 'up',
        39: 'right',
        40: 'down',
        65: 'a',
        66: 'b'
    };

    // the 'official' Konami Code sequence
    var konamiCode = ['up', 'up', 'down', 'down', 'left', 'right', 'left', 'right', 'b', 'a'];

    // a variable to remember the 'position' the user has reached so far.
    var konamiCodePosition = 0;

    // add keydown event listener
    document.addEventListener('keydown', function(e) {
        // get the value of the key code from the key map
        var key = allowedKeys[e.keyCode];
        // get the value of the required key from the konami code
        var requiredKey = konamiCode[konamiCodePosition];

        // compare the key with the required key
        if (key == requiredKey) {

        // move to the next key in the konami code sequence
        konamiCodePosition++;

        // if the last key is reached, activate cheats
        if (konamiCodePosition == konamiCode.length) {
            activateCheats();
            konamiCodePosition = 0;
        }
        } else {
        konamiCodePosition = 0;
        }
    });
}

function activateCheats() {
    imgnum = Math.floor((Math.random() * 61) + 1);
    $('#sitelogo').attr("src","/img/orly/"+imgnum+".gif");
    alert("Sim, nós temos Konami Code!");
}

