////READY
$(document).ready(function(){						   
	send={"consultaformaPago":1};	
				$.ajax
					({
					async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"config_corteCaja.php"										,data:send,success:function(datos)
						{
							if(datos.str>0)							
								{
								$("#formaPago").empty();
								for(i=0;i<datos.str;i++)
									{				
										html="<input type='text' value="+datos[i]['fmp_descripcion']+">";			
										$("#formaPago").append(html);				
									}				
								}						
						}
					});	

	});

function fn_popup()
{					x = (screen.width);
					y = (screen.height);
					aviso=window.open("popup.php","Aviso","width=700,height=550,scrollbars=YES")
					aviso.moveTo((x-700)/2, (y-700)/2)//posición del aviso en la pantalla
}	
