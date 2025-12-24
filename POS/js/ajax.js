function nuevoAjax()
{ 
	/* Crea el objeto AJAX. Esta funcion es generica para cualquier utilidad de este tipo, por
	lo que se puede copiar tal como esta aqui */
	var xmlhttp=false; 
	try 
	{ 
		// Creacion del objeto AJAX para navegadores no IE
		xmlhttp=new ActiveXObject("Msxml2.XMLHTTP"); 
	}
	catch(e)
	{ 
		try
		{ 
			// Creacion del objet AJAX para IE 
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); 
		} 
		catch(E) { xmlhttp=false; }
	}
	if (!xmlhttp && typeof XMLHttpRequest!="undefined") { xmlhttp=new XMLHttpRequest(); } 

	return xmlhttp; 
}
String.prototype.tratarResponseText=function(){
			var pat=/<script[^>]*>([\S\s]*?)<\/script[^>]*>/ig;
			var pat2=/\b\s+src=[^>\s]+\b/g;
			var elementos = this.match(pat) || [];
			for(i=0;i<elementos.length;i++) {
				var nuevoScript = document.createElement('script');
				nuevoScript.type = 'text/javascript';
				var tienesrc=elementos[i].match(pat2) || [];
				if(tienesrc.length){
					nuevoScript.src=tienesrc[0].split("'").join('').split('"').join('').split('src=').join('').split(' ').join('');
				}else{
					var elemento = elementos[i].replace(pat,'$1','');
					nuevoScript.text = elemento;
				}
				document.getElementsByTagName('body')[0].appendChild(nuevoScript);
			}
			return this.replace(pat,'');
		}
		
function SetContainerHTML(contenedor,responseText){
	contenedor.innerHTML = responseText.tratarResponseText();
}
		