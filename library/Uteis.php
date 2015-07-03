<?php
if (!function_exists('kshuffle')) {

    function kshuffle(&$array) {
        if (!is_array($array) || empty($array)) {
            return false;
        }
        $tmp = array();
        foreach ($array as $key => $value) {
            $tmp[] = array('k' => $key, 'v' => $value);
        }
        shuffle($tmp);
        $array = array();
        foreach ($tmp as $entry) {
            $array[$entry['k']] = $entry['v'];
        }
        return true;
    }

}

class Uteis {

    ///// MANIPULAÇÃO DE STRING		

    static function kshuffle(&$array) {
        if (!is_array($array) || empty($array)) {
            return false;
        }
        $tmp = array();
        foreach ($array as $key => $value) {
            $tmp[] = array('k' => $key, 'v' => $value);
        }
        shuffle($tmp);
        $array = array();
        foreach ($tmp as $entry) {
            $array[$entry['k']] = $entry['v'];
        }
        return true;
    }

    static function remove_querystring_var($url, $key) {
        $query = rawurldecode($url);
        foreach ((array) $key AS $k) {
            $query = preg_replace('/(?:&|(\?)?)' . preg_quote($k) . '=[^&]*(?(1)&|)?/i', "$1", $query);
        }
        return $query;
    }

    static function vazio($texto) {
        return !preg_match('/\S/', $texto);
    }

    static function alphaNumeric($string, $format = "", $upper = false) {

        $string = Strings_UTF8::remove_accented_chars($string);
        $string = ( $format != "" ) ? preg_replace($format, "", $string) : $string;

        return $string;
    }

    static function numeric($string, $format = "/[^0-9]/") {
        return preg_replace($format, "", trim($string));
    }

    static function nl2br($string) {
        return preg_replace('`\\n`', '<br />', $string);
    }

    static function mb_trim($string, $chars = '', $chars_array = array()) {
        for ($x = 0; $x < iconv_strlen($chars); $x++)
            $chars_array[] = preg_quote(iconv_substr($chars, $x, 1));
        $encoded_char_list = implode("|", array_merge(array("\s", "\t", "\n", "\r", "\0", "\x0B"), $chars_array));

        $string = mb_ereg_replace("^($encoded_char_list)*", "", $string);
        $string = mb_ereg_replace("($encoded_char_list)*$", "", $string);
        return $string;
    }

    static function recursive_trim($trim, $charlist = null) {
        if (is_array($trim)) {
            $result = array();
            foreach ($trim as $key => $value) {
                if (is_array($value))
                    $result[$key] = self::recursive_trim($value, $charlist);
                else
                    $result[$key] = self::mb_trim($value, $charlist);
            }
        } else {
            $result = self::mb_trim($trim, $charlist);
        }

        return $result;
    }

// fim da função    

    static function singularPlural($strMsgSingular, $strMsgPlural, $intTotal, $replace = true) {
        if ($intTotal > 1) {
            return ( $replace ) ? sprintf($strMsgPlural, $intTotal) : $strMsgPlural;
        } else {
            return ( $replace ) ? sprintf($strMsgSingular, $intTotal) : $strMsgSingular;
        }
    }

    static function wordwrap($str, $width = 25, $break = "\n") {
        return preg_replace('/([^\s]{' . $width . '})(?=[^\s])/mu', '$1' . $break, $str);
    }

    static function resumo($texto, $size = 200, $complete = '...') {
        $texto = htmlspecialchars_decode(strip_tags($texto), ENT_QUOTES);
        $resumo = $texto;
        if (strlen($texto) > $size) {
            $resumo = substr($texto, 0, $size);
            $last_space = ( strripos($resumo, ' ') ) ? strripos($resumo, ' ') : strlen($resumo);
            $resumo = substr($resumo, 0, $last_space) . $complete;
        }

        return $resumo;
    }

    static function cleanTextArea($string, $cleanHtml = false) {
        $string = self::mb_trim($string);
        if (!self::vazio($string)) {
            if ($cleanHtml)
                $string = Strings_UTF8::cleanTextHtml($string);

            return self::compactaTexto($string);
            #$newString = $string;
            $newString = preg_replace('/[ ]*$/ms', '', $newString);
            $newString = preg_replace('/(<br \/>|<br>)\z/m', '', $newString);
            $newString = preg_replace('/[\\r\\t]/m', '', $newString); #/[\\n\\r\\t]/
            return $newString;
        }
        return null;
    }

    static function cleanTextHtml($string) {
        $newString = html_entity_decode($string, ENT_QUOTES, "UTF-8");
        $newString = htmlspecialchars_decode(strip_tags($newString), ENT_QUOTES);

        return $newString;
    }

    static function textExposed($string, $size = 200, $break = "\n") {
        $newString = '';
        if (strlen($string) > $size) {
            $newString .= self::nl2br(mb_substr($string, 0, $size));
            $newString .= '<span class="text_exposed_hide">...</span><span class="text_exposed_show">';
            $newString .= self::nl2br(mb_substr($string, $size));
            $newString .= '<span class="text_exposed_link"><a href="javascript:void(0);">Fechar</a></span></span><span class="text_exposed_hide"><span class="text_exposed_link"><a href="javascript:void(0);">Ver mais</a></span></span>';
        } else {
            return self::wordwrap($string, $break);
        }

        return self::wordwrap($newString, $break);
    }

    static function splitSearchTerms($search, $minchars = 2) {
        // Removendo espaços duplos
        $what_terms = preg_replace('/\s\s+/', ' ', preg_quote($search));

        // Pedaços
        $what_pieces = preg_split('/\s/', preg_replace('/("[^"]*")/', '', $what_terms), - 1, PREG_SPLIT_NO_EMPTY);

        // Termos entre aspas
        $what_quoted = array();
        preg_match_all('/"([^"]+)"/', $what_terms, $what_quoted);
        array_shift($what_quoted);
        $what_quoted = $what_quoted [0];

        $what_array = array_unique(array_merge($what_quoted, $what_pieces));

        foreach ($what_array as $key => $what) {
            if (strlen($what) < $minchars)
                unset($what_array [$key]);
        }

        return $what_array;
    }

    public static function extrairPalavrasChaves($string = NULL, $max_count = 10) {
        #echo PHP_EOL . 'ORIGINAL ========================================' . PHP_EOL;
        #echo $string;

        $string = Uteis::cleanTextArea($string, true);

        #echo PHP_EOL . PHP_EOL . 'CLEANTEXTAREA ========================================' . PHP_EOL . PHP_EOL . PHP_EOL;
        #echo $string;

        if (Uteis::vazio($string)) {
            return '';
        }

        $stop_words = file(Kernel::get_conf('sys_path') . DIRECTORY_SEPARATOR . '_data' . DIRECTORY_SEPARATOR . 'stopWords_pt-BR3.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        #echo PHP_EOL . PHP_EOL . 'STOPWORDS ========================================' . PHP_EOL . PHP_EOL . PHP_EOL;
        #var_dump($stop_words);
        //$string = utf8_decode($string);

        $match_words = array();
        preg_match_all('/\b.*?\b/ium', $string, $match_words);
        $match_words = $match_words[0];

        #echo PHP_EOL . PHP_EOL . 'WORDS ========================================' . PHP_EOL . PHP_EOL . PHP_EOL;
        #var_dump($match_words);

        foreach ($match_words as $key => $item) {
            if ($item == '' || in_array(mb_strtolower($item), $stop_words) || mb_strlen($item) < 2) {
                unset($match_words[$key]);
            } else {
                $match_words[$key] = mb_strtolower($item);
            }
        }

        #echo PHP_EOL . PHP_EOL . 'WORDS SEM STOPWORDS ========================================' . PHP_EOL . PHP_EOL . PHP_EOL;
        #var_dump($match_words);
        //$word_count = str_word_count( utf8_decode(implode(" ", $match_words)) , 1, '0123456789');
        $word_count = preg_split('/\W+/iu', implode(" ", $match_words), -1, PREG_SPLIT_NO_EMPTY);

        #echo PHP_EOL . PHP_EOL . 'WORD COUNT ========================================' . PHP_EOL . PHP_EOL . PHP_EOL;
        #var_dump($word_count);


        $frequency = array_count_values($word_count);
        arsort($frequency);

        $keywords = array_slice($frequency, 0, $max_count, 1);
        $keywords = join(',', array_keys($keywords));
        #echo PHP_EOL . PHP_EOL . 'KEY WORDS ========================================' . PHP_EOL . PHP_EOL . PHP_EOL;
        #var_dump($keywords);

        return $keywords;
    }

    static function compactaTexto($string) {
        $content = preg_replace('/^[\r\n]/', '', $string); // remove primeira linha vazia

        $return = preg_replace('/\r/m', '', $content); // remove os recuos
        $return = preg_replace('/[\t ]+/m', ' ', $return); // remove espaços duplicados por 1 apenas
        $return = preg_replace('/[\t ]*$/ms', '', $return); // remove tabulação e espaço no fim das linhas
        $return = preg_replace('/^[\t ]*/ms', '', $return); // remove tabulação e espaço no início das linhas
        $string = preg_replace('/\n+/ms', "\n", $return); // substitui múltiplas linhas por 1 só
        #$string = preg_replace('/\n(\n)+/ms', "\n\n", $return); // substitui múltiplas linhas por 1 só

        return self::mb_trim($string);
    }

    static function sec2hms($sec, $padHours = true) {

        $hms = "";

        $hours = intval(intval($sec) / 3600);
        $hms .= ($padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ':' : $hours . ':';

        $minutes = intval(($sec / 60) % 60);
        $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT) . ':';

        $seconds = intval($sec % 60);
        $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

        // done!
        return $hms;
    }

    static function human_time_diff($from, $to = '') {
        if (empty($to))
            $to = time();
        $diff = (int) abs($to - $from);
        if ($diff <= 3600) {
            $mins = round($diff / 60);
            if ($mins <= 1) {
                $mins = 1;
            }
            /* translators: min=minute */
            $since = sprintf(self::singularPlural('%s minuto', '%s minutos', $mins), $mins);
        } else if (($diff <= 86400) && ($diff > 3600)) {
            $hours = round($diff / 3600);
            if ($hours <= 1) {
                $hours = 1;
            }
            $since = sprintf(self::singularPlural('%s hora', '%s horas', $hours), $hours);
        } elseif ($diff >= 86400) {
            $days = round($diff / 86400);
            if ($days <= 1) {
                $days = 1;
            }
            $since = sprintf(self::singularPlural('%s dia', '%s dias', $days), $days);
        }
        return $since;
    }

    //Criptogria de SENHA

    static function randomChars($chars = null, $size = 10) {
        $chars = ( NULL == $chars ) ? '0123456789abcdefghijklmnopqrstuvwxyz' : $chars;
        $string = '';
        for ($p = 0; $p < $size; $p++) {
            $string .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $string;
    }
    
    static function validaEmail($string) {
        if (preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $string))
            return true;
        return false;
    }

    static function criptografarSenha($email, $senha) {
        $md5 = md5(strtolower($email . $senha));
        return base64_encode($md5 ^ md5($senha));
    }

    ///// ARQUIVOS 

    static function getArquivoExt($file) {
        if (file_exists($file)) {
            $info = pathinfo($file);
            return strtolower($info['extension']);
        }
        return ("Error Occurred: File Does Not Exist");
    }

    static function gerarNomeArquivo($nome = '', $prefix = null, $sufix = null, $ext = null) {
        //$prefix = (( $prefix ) ? $prefix : preg_replace('/[\.\,/]','x', microtime(TRUE))) . '_';
        $prefix = (( $prefix ) ? $prefix : time()) . '_';
        $sufix = ( $sufix ) ? '_' . $sufix : '';
        $ext = ( $ext ) ? '.' . strtolower($ext) : '';
        return $prefix . md5($nome) . $sufix . $ext;
    }

    static function removerArquivo($files) {
        foreach ((array) $files AS $file) {
            if (is_file($file))
                unlink($file);
        }
    }

    static function enviarArquivosTemp($file_transfer = null, $receive = array()) {
        ini_set('upload_tmp_dir', Kernel::get_conf('tmp_path'));

        foreach (glob(Kernel::get_conf('tmp_path') . '/*') AS $key => $file) {
            if (time() - filemtime($file) > 3600)
                @unlink($file);
        }

        $upload = ( $file_transfer instanceof Zend_File_Transfer ) ? $file_transfer : new Zend_File_Transfer();

        //$upload->clearValidators();

        $files = array();
        foreach ($upload->getFileInfo() AS $name => $file) {
            if (count($receive) > 0 AND !in_array($name, $receive))
                continue;

            if ($upload->isUploaded($name) AND !$upload->isReceived($name)) {
                $upload->addFilter('Rename', array(
                    'target' => Kernel::get_conf('tmp_path') . DIRECTORY_SEPARATOR . 'tmp-' . preg_replace('/[\.\,]/', 'x', microtime(TRUE)) . '-' . Strings_UTF8::toSlug($upload->getFileName($name, false)),
                    'overwrite' => true
                ));

                if ($upload->receive($name)) {
                    $files[$name] = $upload->getFileName($name);
                }
            }
        }

        return ( $file_transfer ) ? $upload : $files;
    }

    public static function gerarNomeDir($id, $dirBase = '', $url = false) {
        $barra = ($url ? '/' : DIRECTORY_SEPARATOR);
        return $dirBase . (substr($dirBase, -1) != $barra ? $barra : '') . (int) ($id / 1000000) . $barra . (int) (($id % 1000000) / 1000) . $barra . $id;
    }

    static function criarDir($dir, $permissao = 0755) {
        if (!is_dir($dir)) {
            mkdir($dir, $permissao, true);
        }

        // make sure the directory is writeable
        if (!is_writeable($dir)) {
            @chmod($dir, $permissao);

            // throw an exception if not writeable
            if (!is_writeable($dir)) {
                throw new Exception('Dir is not writeable, and could not correct permissions: ' . $dir);
            }
        }

        return $dir;
    }

    static function removerDir($dir) {
        if(!$dh = @opendir($dir))
            return;
        
        while (($obj = readdir($dh))) {
            if($obj=='.' || $obj=='..')
                continue;
            if (is_dir($dir.'/'.$obj))
                self::removerDir($dir.'/'.$obj);
            else if (is_file($dir.'/'.$obj))
                @unlink($dir . '/' . $obj);
        }
        
        closedir($dh);
        @rmdir($dir);
    }

    ///// FORMATAÇÕES     

    static function formataData($d, $format = 'dd/MM/yyyy') {
        if (!self::vazio($d)) {
            $date = new Zend_Date($d, 'pt_BR');
            return $date->toString($format);
        } else {
            return $d;
        }
    }

    static function formataCpfCnpj($campo, $formatado = true) {
        $tamanho = (strlen($campo) - 2);
        if ($tamanho != 9 && $tamanho != 12) {
            return false;
        }

        if ($formatado) {
            $mascara = ($tamanho == 9) ? '###.###.###-##' : '##.###.###/####-##';

            $indice = -1;
            for ($i = 0; $i < strlen($mascara); $i++) {
                if ($mascara[$i] == '#')
                    $mascara[$i] = $campo[++$indice];
            }
            $retorno = $mascara;
        }else {
            $retorno = $campo;
        }

        return $retorno;
    }

    static function formataCep($campo, $formatado = true) {
        $campo = Uteis::numeric($campo);
        $tamanho = (strlen($campo) - 3);
        if ($tamanho < 4) {
            return false;
        }
        
        $campo = Uteis::numeric(str_pad($campo, 8, '0', STR_PAD_LEFT));

        if ($formatado) {
            $mascara = '#####-###';

            $indice = -1;
            for ($i = 0; $i < strlen($mascara); $i++) {
                if ($mascara[$i] == '#')
                    $mascara[$i] = $campo[++$indice];
            }
            $retorno = $mascara;
        }else {
            $retorno = $campo;
        }

        return $retorno;
    }

    ///// LOG EM ARQUIVO

    public static function gerarLog($dir, $tabela, $msg) {
        $diretorio = Kernel::get_conf('sys_path') . DIRECTORY_SEPARATOR . '_log' . DIRECTORY_SEPARATOR . $dir;
        Uteis::criarDir($diretorio);
        $log = new AdminLog($dir);
        $log->addValorXML('acao', $msg);
        $log->addValorXML('tabela', $tabela);
        return $log;
    }

    public static function xml2Array($contents, $get_attributes = 1, $priority = 'tag') {
        if (!$contents)
            return array();

        if (!function_exists('xml_parser_create')) {
            //print "'xml_parser_create()' function not found!";
            return array();
        }

        //Get the XML parser of PHP - PHP must have this module for the parser to work
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);

        if (!$xml_values)
            return; //Hmm...

            
//Initializations
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();

        $current = &$xml_array; //Refference
        //Go through the tags.
        $repeated_tag_index = array(); //Multiple tags with same name will be turned into an array
        foreach ($xml_values as $data) {
            unset($attributes, $value); //Remove existing values, or there will be trouble
            //This command will extract these variables into the foreach scope
            // tag(string), type(string), level(int), attributes(array).
            extract($data); //We could use the array by itself, but this cooler.

            $result = array();
            $attributes_data = array();

            if (isset($value)) {
                if ($priority == 'tag')
                    $result = $value;
                else
                    $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
            }

            //Set the attributes too.
            if (isset($attributes) and $get_attributes) {
                foreach ($attributes as $attr => $val) {
                    if ($priority == 'tag')
                        $attributes_data[$attr] = $val;
                    else
                        $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }

            //See tag status and do the needed.
            if ($type == "open") {//The starting of the tag '<tag>'
                $parent[$level - 1] = &$current;
                if (!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                    $current[$tag] = $result;
                    if ($attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                    $repeated_tag_index[$tag . '_' . $level] = 1;

                    $current = &$current[$tag];
                } else { //There was another element with the same tag name
                    if (isset($current[$tag][0])) {//If there is a 0th element it is already an array
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else {//This section will make the value an array if multiple tags with the same name appear together
                        $current[$tag] = array($current[$tag], $result); //This will combine the existing item and the new item together to make an array
                        $repeated_tag_index[$tag . '_' . $level] = 2;

                        if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }
                    }
                    $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                    $current = &$current[$tag][$last_item_index];
                }
            } elseif ($type == "complete") { //Tags that ends in 1 line '<tag />'
                //See if the key is already taken.
                if (!isset($current[$tag])) { //New Key
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                } else { //If taken, put all things inside a list(array)
                    if (isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...
                        // ...push the new element into that array.
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;

                        if ($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else { //If it is not an array...
                        $current[$tag] = array($current[$tag], $result); //...Make it an array using using the existing value and the new value
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        if ($priority == 'tag' and $get_attributes) {
                            if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }

                            if ($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                    }
                }
            } elseif ($type == 'close') { //End of tag '</tag>'
                $current = &$parent[$level - 1];
            }
        }

        return($xml_array);
    }

    ///// ORDENAÇÃO DE VETORES DE OBJETOS
    // Comparison function
    function cmp($a, $b) {
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }

    public static function sortByOneKey(array $array, $key, $asc = true) {
        $result = array();

        $values = array();
        foreach ($array as $id => $item) {
            if ($item instanceof DAO) {
                $values[$id] = $item->{"get" . $item->formataCampoNome($key)}();
            } else if (isSet($item[$key])) {
                $values[$id] = $item[$key];
            } else if (!is_array($item)) {
                $values[$id] = $item;
            }
        }

        if ($asc) {
            asort($values);
        } else {
            arsort($values);
        }

        foreach ($values as $key => $value) {
            $result[$key] = $array[$key];
        }
        return $result;
    }

    static function shuffleAssoc($list) {
        if (!is_array($list))
            return $list;

        $keys = array_keys($list);
        shuffle($keys);
        $random = array();
        foreach ($keys as $key) {
            $random[$key] = $list[$key];
        }

        return $random;
    }

    static function is_serialized($data) {
        // if it isn't a string, it isn't serialized
        if (!is_string($data))
            return false;
        $data = trim($data);
        if ('N;' == $data)
            return true;
        if (!preg_match('/^([adObis]):/', $data, $badions))
            return false;
        switch ($badions[1]) {
            case 'a' :
            case 'O' :
            case 's' :
                if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data))
                    return true;
                break;
            case 'b' :
            case 'i' :
            case 'd' :
                if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data))
                    return true;
                break;
        }
        return false;
    }

    public static function getRealIpAddr($return_both = false) {
        $ip = NULL;
        $proxy = NULL;

        if ( (Kernel::get_conf('sys_external_cache') === true) and (isset($_SERVER['HTTP_TRUE_CLIENT_IP']))){
            $ip = $proxy = $_SERVER['HTTP_TRUE_CLIENT_IP'];
        }else{
            if (isSet($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                if (isSet($_SERVER["HTTP_CLIENT_IP"])) {
                    $proxy = $_SERVER["HTTP_CLIENT_IP"];
                } else {
                    $proxy = $_SERVER["REMOTE_ADDR"];
                }
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else {
                if (isSet($_SERVER["HTTP_CLIENT_IP"])) {
                    $ip = $_SERVER["HTTP_CLIENT_IP"];
                } else {
                    $ip = $_SERVER["REMOTE_ADDR"];
                }
            }
        }

        return ( $return_both ) ? array('ip' => $ip, 'proxy' => $proxy) : ( ( NULL != $proxy ) ? $proxy : $ip );
    }

    static function &getLocalFromIP($ip) {
        $LDAP_HOST = "ldap2.{your-store}.com.br";
        $LDAP_PORT = "389";
        $LDAP_SUFIX = "dc={your-store},dc=com,dc=br";
        $LDAP_USERS = "ou=usuarios";
        $LDAP_LOCAL = "ou=lojas";
        $MASKS = "255.255.255.192:255.255.255.128:255.255.255.0:255.255.254.0:255.255.252.0:255.255.248.0:255.255.240.0";

        $maskList = explode(":", $MASKS);
        $ds = ldap_connect("ldap://" . $LDAP_HOST, $LDAP_PORT);
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds);
        $ret = "";
        if ($r) {
            foreach ($maskList as $mask) {
                $net = self::getnets($ip, $mask);
                $sr = ldap_search($ds, $LDAP_LOCAL . "," . $LDAP_SUFIX, "(nmIP=" . $net . ")");
                $info = ldap_get_entries($ds, $sr);
                //             echo "Count: " . $info["count"] . " - IP: " . $ip .  " - Net: " . $net . " - Mask: " . $mask . "<br/>";
                for ($i = 0; $i < $info["count"]; $i++) {
                    $ret = $info[$i]["filial"][0];
                    return $ret;
                }
            }
        } else {
            return -1;
        }
    }

    static function getnets($ip, $mask) {
        return long2ip(self::myip2long($ip) & self::myip2long($mask));
    }

    static function myip2long($ip) {
        if (is_numeric($ip)) {
            return $ip;
        } else {
            return ip2long($ip);
        }
    }

    /**
     * @param unknown_type $objeto - O Objeto a ser formatado para impressão
     * @param unknown_type $var_dump - se vai ser impresso na função var_dump. False para print_r
     * @return string
     */
    static function printr($objeto, $die = true, $var_dump = false) {
        echo '<pre>';
        $var_dump ? var_dump($objeto) : print_r($objeto);
        echo '</pre>';

        if ($die == true){
            die();
        }else{
            return;
        }

    }

    /**
     * Função que conta o tempo de execução de um script
     */
    static function microtime_float() {
        list ($msec, $sec) = explode(' ', microtime());
        $microtime = (float) $msec + (float) $sec;
        return $microtime;
    }

    /**
     * Função que conta o tempo de execução de um script em segundos
     * @param $inicio - Momento do inicio de execução do script
     * @param $fim    - Momento do fim de execução do script
     * 
     */
    static function tempoExecucao($inicio, $fim) {
        echo 'Tempo de Execução do Script: ' . round($fim - $inicio, 3) . ' Segundos';
    }

    /**
     * Retorna um array com informacoes sobre o browser do cliente
     * 
     * @return multitype:string unknown
     */
    static public function getBrowser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // pegar a versao corrente 
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';

        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            // Verificar se a versao e anterior ou posterior o nome
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // verificar se e um numero
        if ($version == null || $version == "") {
            $version = "?";
        }

        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
    }

    /**
     * Função que trunca valores para duas casas decimais
     * @param $valor - Valor a Ser Truncado
     *
     */
    static function truncate($valor) {

        if (strpos($valor, ',')) {
            $partes = explode(',', $valor);
            $decimal = substr($partes[1], 0, 2);

            $valorTruncado = $partes[0] . '.' . $decimal;
        } else if (strpos($valor, '.')) {
            $partes = explode('.', $valor);
            $decimal = $partes[1];

            $valorTruncado = $partes[0] . '.' . $decimal;
        } else {
            $valorTruncado = $valor;
        }

        return $valorTruncado;
    }

    /**
     * Retorna a diferenca, em minutos, entre dateTime1 - dateTime2, podendo esta diferenca ser negativa ou positiva
     * 
     * @author Silas dos Santos
     * @version 17 AGO 2012
     * @since 17 AGO 2012 - Silas dos Santos / Desenvolvimento
     * 
     * @param string $dateTime1 no formato Y/m/d hh:mm:ss
     * @param string $dateTime2 no formato Y/m/d hh:mm:ss
     * 
     * @return int
     */
    static public function diffDateTimeMinutes($dateTime1, $dateTime2) {
        $date1 = new Datetime($dateTime1);
        $date2 = new Datetime($dateTime2);
        $diff = $date1->diff($date2);

        // 8766 horas no ano
        $diff->y = $diff->y * 8766 * 60; // anos para minutos
        $month = ( $diff->m / 12 ) * 8766 * 60; // meses para minutos
        $diff->d = $diff->d * 24 * 60; // dias para minutos
        $diff->h = $diff->h * 60; // horas para minutos
        $sec = (float) $diff->s / 60; // segundo para minutos

        $tempo = $diff->y + $month + $diff->d + $diff->h + $diff->i + $sec;
        $tempo *= $diff->invert == true ? -1 : 1;
        return $tempo;
    }

    static public function StringValor($valor) {

        if (strstr($valor, ",")) {
            $exp = explode(",", (string) $valor);
        }

        if (strstr($valor, ".")) {
            $exp = explode(".", (string) $valor);
        }

        if (strlen($exp[1]) == 1) {
            $exp[1] = $exp[1] . "0";
        }

        $valor = $exp[0] . "," . $exp[1];
        return $valor;
    }

    static function valorNumerico($valor, $formato = "us") {
        if ($formato == "us") {

            $valor = str_replace(".", "", $valor);

            if (strstr($valor, ",")) {
                $exp = explode(",", (string) $valor);
                $valor = $exp[0] . '.' . $exp[1];
            }
        }
        return $valor;
    }

    /**
     * função para transformar a string em array dependendendo ou não do delimitador na string
     * 
     * @author: Sóstenes Gomes
     * @since 20/08/2013
     * @version 1.0
     * @param string $string
     * @param string $delimiter
     * @return array $array com a string transformada em array
     */
    static function stringToarray($string, $delimiter=null){
        
        if ($delimiter !== null){
            if (strstr($string, $delimiter)){
                $array = explode($delimiter, $string);
            }else{
                $array = array($string);
            }
        }else{
            $array = array($string);
        }
        
        return $array;
        
    }

    /**
     * Informa se uma dada string e uma string no padrao json
     * @param String $string
     * @return Boolean
     */
    static function isStringJson($string) {
        if (preg_match('/^[\[\{]\"/', $string)) {
            $aJson = json_decode($string, true);
            if (!is_null($aJson))
               return true;
        }
        
        return false;
    }

    static function xml_entities($string) {
        return strtr(
            $string,
            array(
                "<" => "&lt;",
                ">" => "&gt;",
                '"' => "&quot;",
                "'" => "&apos;",
                "&" => "&amp;",
                "" => ""
            )
        );
    }

    static function arrayFilterPreserveZero($var){
        return ($var !== NULL && $var !== FALSE && $var !== '');
    }

    static function utf8_strtr($str) {

        $from = "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ";
        $to = "aaaaeeiooouucAAAAEEIOOOUUC";

        $keys = array();
        $values = array();
        preg_match_all('/./u', $from, $keys);
        preg_match_all('/./u', $to, $values);
        $mapping = array_combine($keys[0], $values[0]);

        return strtr($str, $mapping);
    }

    /**
     * @author Silas Santos
     * @version V1.0
     * @since 11-06-2014 / Silas - Implementacao inicial V1.0
     * 
     * @param String $strMoney pode ser: X ou X,XX ou X.XXX,XX ou X.XXX.XXX,XX
     * @param Boolean $inverse float2money, a conversao procede de float para money
     * @return mixed no formato X ou X.XX ou XXXX.XX ou XXXXXXX.XX ou $strMoney caso haja erro 
     */
    static function money2float($strMoney, $inverse = false) {
        if (empty($strMoney)) {
            $strMoney = $inverse ? '0,00' : '0.00';
        }
        else {
            $tmpStrMoney = $strMoney;
            
            if (! $inverse) {
                if (strpos($strMoney, '.') !== false) {
                    $tmpStrMoney = str_replace('.', '', $strMoney);
                }
    
                if (strpos($tmpStrMoney, ',') !== false) {
                    $tmpStrMoney = str_replace(',', '.', $tmpStrMoney);
                }
                
                if (! empty($tmpStrMoney) && is_numeric($tmpStrMoney)) {
                    $strMoney = (float) $tmpStrMoney;
                }
            }
            else if (is_float($strMoney) || is_int($strMoney) || is_numeric($strMoney)) {
                $strMoney = Uteis::moneyFormat((float) $tmpStrMoney);
            }
        }
        
        return $strMoney;
    }

    /**
     * Faz arredondamento sempre para cima com 2 casas decimais.
     * 
     * @param $value
     * @param int $places
     * @return float
     * 
     * @see http://php.net/ceil#50448
     * @author steve_phpnet // nanovox \\ com
     */
    static function roundUp($value, $places = 2) {
        if ($places < 0) {
            $places = 0;
        }
        
        $mult = pow(10, $places);
        return ceil($value * $mult) / $mult;
    }

    static public function isJson($string){

        $string = trim($string);

        if(self::vazio($string))
            return false;

        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    
}
