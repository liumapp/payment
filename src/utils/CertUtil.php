<?php
/**
 * Created by PhpStorm.
 * User: liumeishengqi
 * Date: 4/5/17
 * Time: 7:39 PM
 */

namespace liumapp\payment\utils;

class Cert
{
    public $cert;
    public $certId;
    public $key;
}

class CertUtil{

    private static $signCerts = array();
    private static $encryptCerts = array();
    private static $verifyCerts = array();

    private static function initSignCert($certPath, $certPwd){
        $cert = new Cert();

        $pkcs12certdata = file_get_contents ( $certPath );

        openssl_pkcs12_read ( $pkcs12certdata, $certs, $certPwd );
        $x509data = $certs ['cert'];

        openssl_x509_read ( $x509data );
        $certdata = openssl_x509_parse ( $x509data );
        $cert->certId = $certdata ['serialNumber'];

// 		$certId = CertSerialUtil::getSerial($x509data, $errMsg);
// 		if($certId === false){
//         	return;
// 		}
//         $cert->certId = $certId;

        $cert->key = $certs ['pkey'];
        $cert->cert = $x509data;

        CertUtil::$signCerts[$certPath] = $cert;
    }

    public static function getSignKeyFromPfx($certPath=SDK_SIGN_CERT_PATH, $certPwd=SDK_SIGN_CERT_PWD)
    {
        if (!array_key_exists($certPath, CertUtil::$signCerts)) {
            self::initSignCert($certPath, $certPwd);
        }
        return CertUtil::$signCerts[$certPath] -> key;
    }

    public static function getSignCertIdFromPfx($certPath=SDK_SIGN_CERT_PATH, $certPwd=SDK_SIGN_CERT_PWD)
    {
        if (!array_key_exists($certPath, CertUtil::$signCerts)) {
            self::initSignCert($certPath, $certPwd);
        }
        return CertUtil::$signCerts[$certPath] -> certId;
    }

    private static function initEncryptCert($cert_path)
    {
        $cert = new Cert();
        $x509data = file_get_contents ( $cert_path );

        openssl_x509_read ( $x509data );
        $certdata = openssl_x509_parse ( $x509data );
        $cert->certId = $certdata ['serialNumber'];

// 	    $certId = CertSerialUtil::getSerial($x509data, $errMsg);
// 	    if($certId === false){
// 	    	return;
// 	    }
// 	    $cert->certId = $certId;

        $cert->key = $x509data;
        CertUtil::$encryptCerts[$cert_path] = $cert;
    }

    public static function getEncryptCertId($cert_path=SDK_ENCRYPT_CERT_PATH){
        if(!array_key_exists($cert_path, CertUtil::$encryptCerts)){
            self::initEncryptCert($cert_path);
        }
        return CertUtil::$encryptCerts[$cert_path] -> certId;
    }

    public static function getEncryptKey($cert_path=SDK_ENCRYPT_CERT_PATH){
        if(!array_key_exists($cert_path, CertUtil::$encryptCerts)){
            self::initEncryptCert($cert_path);
        }
        return CertUtil::$encryptCerts[$cert_path] -> key;
    }

    private static function initVerifyCerts($cert_dir=SDK_VERIFY_CERT_DIR) {
        $handle = opendir ( $cert_dir );
        if (!$handle) {
            return;
        }
        while ($file = readdir($handle)) {
            clearstatcache();
            $filePath = $cert_dir . '/' . $file;
            if (is_file($filePath)) {
                if (pathinfo($file, PATHINFO_EXTENSION) == 'cer') {
                    $x509data = file_get_contents($filePath);
                    $cert = new Cert();
                    openssl_x509_read($x509data);
                    $certdata = openssl_x509_parse($x509data);
                    $cert->certId = $certdata ['serialNumber'];

//                     $certId = CertSerialUtil::getSerial($x509data, $errMsg);
//                     if($certId === false){
//                     	$logger->LogInfo("签名证书读取序列号失败：" . $errMsg);
//                     	return;
//                     }
//                     $cert->certId = $certId;

                    $cert->key = $x509data;
                    CertUtil::$verifyCerts[$cert->certId] = $cert;
                }
            }
        }
        closedir ( $handle );
    }

    public static function getVerifyCertByCertId($certId){
        if(count(CertUtil::$verifyCerts) == 0){
            self::initVerifyCerts();
        }
        if(count(CertUtil::$verifyCerts) == 0){
            return null;
        }
        if(array_key_exists($certId, CertUtil::$verifyCerts)){
            return CertUtil::$verifyCerts[$certId]->key;
        } else {
            return null;
        }
    }

    public static function test() {

        $x509data = file_get_contents ( "d:/certs/acp_test_enc.cer" );
// 		$resource = openssl_x509_read ( $x509data );
        // $certdata = openssl_x509_parse ( $resource ); //<=这句尼玛内存泄漏啊根本释放不掉啊啊啊啊啊啊啊
        // echo $certdata ['serialNumber']; //<=就是需要这个数据啦
        // echo $x509data;
        // unset($certdata); //<=没有什么用
        // openssl_x509_free($resource); //<=没有什么用x2
        echo CertSerialUtil::getSerial ( $x509data, $errMsg ) . "\n";
    }
}

class CertSerialUtil {

    private static function bytesToInteger($bytes) {
        $val = 0;
        for($i = 0; $i < count ( $bytes ); $i ++) {
// 			$val += (($bytes [$i] & 0xff) << (8 * (count ( $bytes ) - 1 - $i)));
            $val += $bytes [$i] * pow(256, count ( $bytes ) - 1 - $i);
// 			echo $val . "<br>\n";
        }
        return $val;
    }

    private static function bytesToBigInteger($bytes) {
        $val = 0;
        for($i = 0; $i < count ( $bytes ); $i ++) {
            $val = bcadd($val, bcmul($bytes [$i], bcpow(256, count ( $bytes ) - 1 - $i)));
// 			echo $val . "<br>\n";
        }
        return $val;
    }

    private static function toStr($bytes) {
        $str = '';
        foreach($bytes as $ch) {
            $str .= chr($ch);
        }
        return $str;
    }

    public static function getSerial($fileData, &$errMsg) {

// 		$fileData = str_replace('\n','',$fileData);
// 		$fileData = str_replace('\r','',$fileData);

        $start = "-----BEGIN CERTIFICATE-----";
        $end = "-----END CERTIFICATE-----";
        $data = trim ( $fileData );
        if (substr ( $data, 0, strlen ( $start ) ) != $start ||
            substr ( $data, strlen ( $data ) - strlen ( $end ) ) != $end) {
            // echo $fileData;
            $errMsg = "error pem data";
            return false;
        }

        $data = substr ( $data, strlen ( $start ), strlen ( $data ) - strlen ( $end ) - strlen ( $start ) );
        $bindata = base64_decode ( $data );
        $bindata = unpack ( 'C*', $bindata );

        $byte = array_shift ( $bindata );
        if ($byte != 0x30) {
            $errMsg = "1st tag " . $byte . " is not 30";
            return false;
        }

        $length = CertSerialUtil::readLength ( $bindata );
        $byte = array_shift ( $bindata );
        if ($byte != 0x30) {
            $errMsg = "2nd tag " . $byte . " is not 30";
            return false;
        }

        $length = CertSerialUtil::readLength ( $bindata );
        $byte = array_shift ( $bindata );
// 		echo $byte . "<br>\n";
        if ($byte == 0xa0) { //version tag.
            $length = CertSerialUtil::readLength ( $bindata );
            CertSerialUtil::readData ( $bindata, $length );
            $byte = array_shift ( $bindata );
        }

// 		echo $byte . "<br>\n";
        if ($byte != 0x02) { //x509v1 has no version tag, x509v3 has.
            $errMsg = "4th/3rd tag " . $byte . " is not 02";
            return false;
        }
        $length = CertSerialUtil::readLength ( $bindata );
        $serial = CertSerialUtil::readData ( $bindata, $length );
// 		echo bin2hex(CertSerialUtil::toStr( $serial ));
        return CertSerialUtil::bytesToBigInteger($serial);
    }

    private static function readLength(&$bindata) {
        $byte = array_shift ( $bindata );
        if ($byte < 0x80) {
            $length = $byte;
        } else {
            $lenOfLength = $byte - 0x80;
            for($i = 0; $i < $lenOfLength; $i ++) {
                $lenBytes [] = array_shift ( $bindata );
            }
            $length = CertSerialUtil::bytesToInteger ( $lenBytes );
        }
        return $length;
    }

    private static function readData(&$bindata, $length) {
        $data = array ();
        for($i = 0; $i < $length; $i ++) {
            $data [] = array_shift ( $bindata );
        }
        return $data;
    }
}



