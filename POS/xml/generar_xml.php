<?php
class XML {
    private $name;
    private $childs;
    private $attributes;
    private $data;
    private $addVer;
    private $version;
    public function __construct( $name, $addv = true ) {
        $this->name = $name;
        $this->childs = array();
        $this->attributes = array();
        $this->data = "";
        $this->addVersion = false;
        $this->version = "";
        if( $addv )
            $this->addVersion();
    }
    private function addVersion($version = '"1.0"', $encoding = '"UTF-8"' ) {/*\"1.0\" encoding=\"utf-8\*/
        $this->version = '<?xml version="1.0" encoding="UTF-8"?>'."\r\n";
        $this->addVer = true;
        
    }
    
    public function createNode( $name ) {
        $node = new self( $name, false ); 
        $this->childs[] = $node;
        return $node;
    }

    public function appendChild( /*GeckoXML*/ $node ) {
        $this->childs[] = $node;
        return $node;
    }
    
    public function setAttribute( $name, $value ) {
        $this->attributes[$name] = $value;
    }
    
    public function setAttributes( $attributes ) {
        if( !is_array( $attributes ) ) {
			 echo "<script>alert('El atributo esperado no es un Arrego,". gettype( $attributes ) ."');</script>";
        }
        foreach( $attributes as $name => $value ) {
            $this->setAttribute( $name, $value );
        }
    }
    public function setData( $data ) {
        if( !is_string( $data ) ) {
            $data = $this->varToString( $data );
        }
        $this->data = $data ;
    }

    private function toString() {
        return $this->__toString();
    }  
   
    public function __toString() {
        $name = $this->name;
        $data = $this->data;
        $xml = "";
        $attrs = "";
        
        if( count( $this->attributes ) > 0 ) {
            foreach( $this->attributes as $aname => $avalue ) {
                $attrs .= " $aname=\"$avalue\"";
            }
        }
        if( empty( $data ) && ( count( $this->childs ) > 0 ) ) { // Node tag get childs
            $begintag = "<$name$attrs>\r\n";
            $endtag = "</$name>\r\n";
            foreach( $this->childs as $child ) {
                $xml .= $child->toString();
            }
            $xml = $begintag . $xml . $endtag;
        } else {
       //     if( empty( $data ) ) { // empty node
//                $xml = "<$name$attrs />\r\n";
//            } else {
                $xml = "<$name$attrs>$data</$name>\r\n";
           // }
        }
        if( $this->addVer ) {
            $xml = $this->version . $xml;
        }
        return $xml;
    }
    public function guardarXML( $fname ) {
        $fh = @fopen( $fname, "w" );
        if( !$fh ) {
           echo "<script>alert('No se puede abrir el archivo ($fname)');</script>"; 
        }
        
        $rst = fwrite( $fh, $this->toString() );
        if( $rst === false ) {
             echo "<script>alert('No se puede escribir el archivo ($fname)');</script>";
        }
        return fclose( $fh );
    }

    private function varToString( $data ) {
        if( is_array( $data ) ) {
            return print_r( $data, true );
        }
        
        if( is_object( $data ) ) {
            return get_object_vars( $data );
        }
    }
}
?> 