<?php
/**
 * Created by PhpStorm.
 * User: sostenes
 * Date: 02/09/14
 * Time: 16:03
 */

class Handler{

    private static $patterns = array("/@ID@/","/@MESSAGE@/","/@RESPONSE@/");
    private static $template = '<root><id>@ID@</id><message><![CDATA[@MESSAGE@]]></message><response><![CDATA[@RESPONSE@]]></response></root>';

    public static $responseHeader;

    public static function success($message, $response=''){

        $replace = array('1', $message, $response);

        $xml = preg_replace(self::$patterns, $replace, self::$template);

        echo $xml;
        die();
    }

    public static function error($message, $response=''){

        $replace = array('2', $message, $response);

        $xml = preg_replace(self::$patterns, $replace, self::$template);

        self::sendEmail($xml);

        echo $xml;
        die();
    }

    private function sendEmail($xml){

        try{
            $emailConf = Config::get('email');

            $mail = new PHPMailer();

            $mail->isSMTP();
            $mail->CharSet  = $emailConf['charset'];
            $mail->Host     = $emailConf['host'];
            $mail->SMTPAuth = $emailConf['auth'];
            $mail->Username = $emailConf['username'];
            $mail->Password = $emailConf['password'];
            $mail->Port     = $emailConf['port'];
            $mail->From     = $emailConf['from'];
            $mail->FromName = $emailConf['from_name'];
            $mail->Subject  = 'Falha na integração';
            $mail->Body     = "Ocorreu erro na integração de dados com Vtex." . "\r\n" . "\r\n";
            $mail->Body     .= "Ambiente: " . ( ( (Config::get('development') === true) ? 'Desenvolvimento' : 'Produção')) . "\r\n" . "\r\n";
            $mail->Body     .= "URL Segments: " . json_encode(URI::get_segments()) . "\r\n" . "\r\n";
            $mail->Body     .= "POST: " . print_r($_POST, true) . "\r\n" . "\r\n";
            $mail->Body     .= "Response Header: " . self::$responseHeader . "\r\n" . "\r\n";
            $mail->Body     .= 'Saída do script: ' . $xml;

            $mail->addAddress($emailConf['address']);
            $mail->isHTML(false);
            $mail->send();

            return true;

        }catch (Exception $e){
            return false;
        }

    }

}