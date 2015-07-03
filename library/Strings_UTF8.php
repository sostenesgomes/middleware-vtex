<?php

class Strings_UTF8 {

    static function toSlug($string, $space="-")
    {  
		$string = Encoding::fixUTF8(Encoding::toUTF8($string));
        $string = self::mb_strtolower(self::mb_trim($string));
        $string = self::cleanTextHtml($string);

        if (function_exists('iconv')) {
            $string = @iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        }

        $string = preg_replace('/[^a-z0-9 _.-]/', '', $string);
        $string = preg_replace('/[\s\-_]+/', $space, $string);  
        $string = str_replace(' ', $space, $string);  

        return $string;  
    }   	
	
    static function cleanTextHtml($string)
    {
        $newString      =   html_entity_decode($string, ENT_QUOTES, "UTF-8");
        $newString      =   htmlspecialchars_decode(strip_tags($newString), ENT_QUOTES);
        
        return $newString;
    }    
    
    static function mb_strtoupper($texto) {
    
        return mb_strtoupper($texto);
    
    } // 

    static function mb_ucwords($texto) {
    
        return ucwords(self::mb_strtolower($texto));
    
    } // fim da função

    static function mb_strtolower($texto) {
    
        return mb_strtolower($texto);
    
    } // 

    static function mb_trim($string, $chars = '', $chars_array = array()) 
    {
        for( $x=0; $x<iconv_strlen( $chars ); $x++ ) $chars_array[] = preg_quote( iconv_substr( $chars, $x, 1 ) );
        $encoded_char_list = implode( "|", array_merge( array( "\s","\t","\n","\r", "\0", "\x0B" ), $chars_array ) );
    
        $string = mb_ereg_replace( "^($encoded_char_list)*", "", $string );
        $string = mb_ereg_replace( "($encoded_char_list)*$", "", $string );
        return $string;        
    }
    

    static function my_trim($trim, $charlist=null) {
        if ( is_array($trim) ) {
            $result = array();
            foreach($trim as $key => $value){
                if ( is_array($value) ) $result[$key] = self::my_trim($value, $charlist);
                else $result[$key] = self::mb_trim($value, $charlist);
            }
        } else {
            $result = self::mb_trim($trim, $charlist);
        }
        
        return $result;
    
    } // fim da função    
    
	/**
	 *	\brief Troca caracteres acentuados por não acentuado
	 */
	public static function remove_accented_chars($txt) {
		$txt = mb_ereg_replace('[áàâãåäªÁÀÂÄÃª]', 'a', $txt);
		$txt = mb_ereg_replace('[éèêëÉÈÊË]', 'e', $txt);
		$txt = mb_ereg_replace('[íìîïÍÌÎÏ]', 'i', $txt);
		$txt = mb_ereg_replace('[óòôõöºÓÒÔÕÖº]', 'o', $txt);
		$txt = mb_ereg_replace('[úùûüÚÙÛÜµ]', 'u', $txt);
		$txt = mb_ereg_replace('[ñÑ]', 'n', $txt);
		$txt = mb_ereg_replace('[çÇ]', 'c', $txt);
		$txt = mb_ereg_replace('[ÿ¥]', 'y', $txt);
		$txt = mb_ereg_replace('[¹]', '1', $txt);
		$txt = mb_ereg_replace('[²]', '2', $txt);
		$txt = mb_ereg_replace('[³]', '3', $txt);
		$txt = mb_ereg_replace('[Ææ]', 'ae', $txt);
		$txt = mb_ereg_replace('[Øø]', '0', $txt);
		$txt = mb_ereg_replace('[†°¢£§•¶ß®©™´¨≠±≤≥∂∑∏π∫Ω]', '', $txt);

		return $txt;
	}

	/**
	 *	/brief Converte uma string UTF-8 para Windows-CP-1252
	 */
	public static function convert_to_windowscp1252($string) {
		$chars = array( 'Ç', 'Ä', '£', 'Ä', 'Å', 'Ç', 'É', 'Ñ', 'Ö', 'Ü', 'á', 'à', 'â', 'ä', 'ã', 'å', 'ç', 'é', 'è', 'ê', 'ë', 'í', 'ì', 'î', 'ï', 'ñ', 'ó', 'ò', 'ô', 'ö', 'õ', 'ú', 'ù', 'û', 'ü', '†', '°', '¢', '£', '§', '•', '¶', 'ß', '®', '©', '™', '´', '¨', '≠', 'Æ', 'Ø', '∞', '±', '≤', '≥', '¥', 'µ', '∂', '∑', '∏', 'π', '∫', 'ª', 'º', 'Ω', 'æ', 'ø', );
		$cp1252 = array( chr(128), chr(146), chr(163), chr(192), chr(193), chr(194), chr(195), chr(196), chr(197), chr(198), chr(199), chr(200), chr(201), chr(202), chr(203), chr(204), chr(205), chr(206), chr(207), chr(208), chr(209), chr(210), chr(211), chr(212), chr(213), chr(214), chr(215), chr(216), chr(217), chr(218), chr(219), chr(220), chr(221), chr(222), chr(223), chr(224), chr(225), chr(226), chr(227), chr(228), chr(229), chr(230), chr(231), chr(232), chr(233), chr(234), chr(235), chr(236), chr(237), chr(238), chr(239), chr(240), chr(241), chr(242), chr(243), chr(244), chr(245), chr(246), chr(247), chr(248), chr(249), chr(250), chr(251), chr(252), chr(253), chr(254), chr(255), );

		return str_replace($chars, $cp1252, $string);
	}
}
?>