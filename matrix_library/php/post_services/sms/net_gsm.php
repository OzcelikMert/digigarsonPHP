<?php
namespace matrix_library\php\post_services\sms;
use SoapClient;

class net_gsm{
    function __construct($message,$phone){
        try {
            $client = new SoapClient("http://soap.netgsm.com.tr:8080/Sms_webservis/SMS?wsdl");
            $Result = $client -> smsGonder1NV2(array(
                'username'=>'',
                'password' => '',
                'header' => 'Mimi',
                'msg' => $message,
                'gsm' => $phone,
                'filter' => '',
                'startdate'  => '',
                'stopdate'  => '',
                'encoding' => 'utf8'
            ));
        } catch (Exception $exc){}
    }

}

?>
