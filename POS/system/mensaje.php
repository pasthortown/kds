<?php

function message($msg,$n){
echo( "<script language='JavaScript'>" .
      "alert(\"$msg\");" .
      "if($n==1) history.back();" .
      "</script>");
}

function mensaje($msg,$n){
echo( "<script language='JavaScript'>" .
      "alert(\"$msg\");" .
      "if($n==1) history.back();" .
      "if($n==2) window.close();" .
      "</script>");
}
  
function error($msg,$n){
echo( "<script language='JavaScript'>" .
      "msgbox(\"$msg\");" .
      "if($n==1) history.back();" .
      "if($n==2) window.close();" .
      "</script>");
}
?>