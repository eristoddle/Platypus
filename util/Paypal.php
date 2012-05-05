<?php
namespace app\util;

class Paypal {
    protected static $_connection = array();

    public static function config(array $conn = array()) {
        self::$_connection = $conn;
    }

    public static function doCapture($authId, $captureAmount, $currency, $complete)
    {
        if (empty(self::$_connection)) {
            throw new \Exception('Paypal not configured.');
        }

        extract(self::$_connection);

        $fields = array(
            'METHOD' => 'DoCapture',
            'VERSION' => '87.0',
            'USER' => urlencode($username),
            'PWD' => urlencode($password),
            'SIGNATURE' => urlencode($signature),
            'AUTHORIZATIONID' => urlencode($authId),
            'AMT' => urlencode($captureAmount),
            'CURRENCYCODE' => $currency,
            'COMPLETETYPE' => $complete ? 'Complete' : 'NotComplete'
        );

        $ch = curl_init($target_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($fields));

        $response = curl_exec($ch);
        parse_str($response, $response_vars);

        curl_close($ch);

        $result = array();
        foreach ($response_vars as $key => $value) {
            $result[strtolower($key)] = $value;
        }

        return $result;
    }
}