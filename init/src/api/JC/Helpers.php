<?php

/**
 * Created by PhpStorm.
 * User: Josue
 * Date: 7/17/2016
 * Time: 5:31 PM
 */

namespace JC;

include_once __DIR__ . '/Helpers.php';
include_once __DIR__ . '/database/config.php';
include_once __DIR__ . '/database/constants.php';
include_once __DIR__ . '/database/DBO.php';

use JC\Database\DBO;

class Helpers
{
    const XERO_CODE = 1;
    const QUICKBOOKS_CODE = 2;

    public static function qbStructure($userid, array $arrayData, $time)
    {
        if (isset($arrayData['LinkedTxn'])) {
            $arrayData['LinkedTxn'] = self::parseQbPayments($arrayData['LinkedTxn']);
        } else {
            $arrayData['LinkedTxn'] = null;
        }

        return array(
            'userid' => $userid,
            'invoice_id' => $arrayData['Id'],
            'invoice_number' => $arrayData['DocNumber'],
            'invoice_type' => 'exclusive',
            'company_id' => $arrayData['CustomerRef'],
            'company_name' => $arrayData['BillAddr']['Line1'],
            'txn_status' => $arrayData['TxnStatus'],
            'txn_date' => $arrayData['TxnDate'],
            'due_date' => $arrayData['DueDate'],
            'total_tax' => $arrayData['TxnTaxDetail']['TotalTax'],
            'sub_total' => ($arrayData['TotalAmt'] - $arrayData['TxnTaxDetail']['TotalTax']),
            'total' => $arrayData['TotalAmt'],
            'currency_type' => $arrayData['CurrencyRef'],
            'amount_paid' => $arrayData['Deposit'],
            "payment_ids" => $arrayData['LinkedTxn'],
            'amount_due' => $arrayData['Balance'],
            'amount_credited' => '0',
            'accounting_api' => self::QUICKBOOKS_CODE,
            'invoice_sent' => 'true',
            'timestamp' => $time
        );
    }

    public static function xeroStructure($userid, array $arrayData, $time, $insertData = false)
    {
        if (isset($arrayData['Payments'])) {
            $arrayData['Payments'] = self::parseXeroPayments($arrayData['Payments'], $time, $insertData);
        } else {
            $arrayData['Payments'] = null;
        }

        return array(
            'userid' => $userid,
            'invoice_id' => $arrayData['InvoiceID'],
            'invoice_number' => empty($arrayData['InvoiceNumber']) ? null : strtolower($arrayData['InvoiceNumber']),
            'invoice_type' => $arrayData['LineAmountTypes'],
            'company_id' => $arrayData['Contact']['ContactID'],
            'company_name' => $arrayData['Contact']['Name'],
            'txn_status' => $arrayData['Status'],
            'txn_date' => $arrayData['Date'],
            'due_date' => $arrayData['DueDate'],
            'total_tax' => $arrayData['TotalTax'],
            'sub_total' => $arrayData['SubTotal'],
            'total' => $arrayData['Total'],
            'currency_type' => empty($arrayData['CurrencyCode']) ? null : strtolower($arrayData['CurrencyCode']),
            'amount_paid' => $arrayData['AmountPaid'],
            "payment_ids" => $arrayData['Payments'],
            'amount_due' => $arrayData['AmountDue'],
            'amount_credited' => $arrayData['AmountCredited'],
            'accounting_api' => self::XERO_CODE,
            'invoice_sent' => empty($arrayData['SentToContact']) ? null : strtolower($arrayData['SentToContact']),
            'timestamp' => $time
        );
    }


    public static function qbPaymentStructure(array $data, $time)
    {
        return array(
            'paymentid' => $data['Id'],
            'date' => $data['MetaData']['CreateTime'],
            'amount' => $data['TotalAmt'],
            'timestamp' => $time
        );
    }

    public static function parseQbPayments(array $data)
    {
        $outputData = array();

        if ($data != null or sizeof($data) > 0) {
            foreach ($data as $key => $value) {
                if (is_numeric($key)) {
                    if ($value['TxnType'] == 'Payment') {
                        $outputData = array_merge($outputData, array($value['TxnId']));
                    }
                } else {
                    if ($data['TxnType'] == 'Payment') {
                        $outputData = array_merge($outputData, array($data['TxnId']));
                    }
                }
            }
            if (sizeof($outputData) == 0)
                $outputData = null;
        } else {
            return null;
        }

//        var_dump(json_encode($outputData));
        return json_encode($outputData);
    }

    public static function wasRequestedLessThanOneHour($userid, $time)
    {
        $storeKeys = new DBO(DB_NAME_TF, DB_USER, DB_PASS);
        $query = $storeKeys->prepare("SELECT * FROM " . TB_DATA_REQ . " WHERE userid=:userid ORDER BY timestamp DESC LIMIT 1");
        $query->bindValue(":userid", $userid);

        $query->execute();
        $timeStamp = $query->fetchAll();
//        var_dump($timeStamp);
        if (sizeof($timeStamp) > 0) {
            $timeStamp = $timeStamp[0]['timestamp'];
//            var_dump(((int)$time - (int)$timeStamp));
            return ($timeStamp - $time < 3600);
        }
        return false;
    }

    public static function parseXeroPayments(array $data, $time, $insertData = false)
    {
        $outputData = array();
        if ($data != null or sizeof($data) > 0) {
            foreach ($data['Payment'] as $key => $value) {
                if (is_numeric($key)) {
                    $outputData = array_merge($outputData, array($value['PaymentID']));
                    if ($insertData)
                        self::insertData(TB_PAYMENTS, self::xeroPaymentStructure($value, $time));
                } else {
                    if ($key == 'PaymentID') {
                        $outputData = array_merge($outputData, array($value));
                        if ($insertData)
                            self::insertData(TB_PAYMENTS, self::xeroPaymentStructure($data['Payment'], $time));
                    }

                }
            }
        } else {
            return null;
        }
//        var_dump(json_encode($outputData));
        return json_encode($outputData);
    }

    public static function xeroPaymentStructure(array $data, $time)
    {
        return array(
            'paymentid' => $data['PaymentID'],
            'date' => $data['Date'],
            'amount' => $data['Amount'],
            'timestamp' => $time
        );
    }

    public static function insertData($tbName, array $arrayData)
    {
        $storeKeys = new DBO(DB_NAME_TF, DB_USER, DB_PASS);

        $queryString = "INSERT INTO " . $tbName . " SET ";
        $queryString .= self::createSetQuery($arrayData);

//        echo $queryString . "<br>";

        $query = $storeKeys->prepare($queryString);

        foreach ($arrayData as $key => $value) {
            $query->bindValue(":$key", $value);
        }

        return $query->execute();
    }

    public static function createSetQuery(array $arrayData)
    {
        $queryString = "";

        $count = 0;
        $dataSize = sizeof($arrayData);
        foreach ($arrayData as $key => $value) {
            if ($count == ($dataSize - 1)) {
                $queryString .= " $key=:$key;";
            } else {
                $queryString .= " $key=:$key,";
            }
            $count++;
        }

        return $queryString;
    }

    public static function getUserId($realmId)
    {
        if ($realmId == null or empty($realmId))
            return null;
        $storeKeys = new DBO(DB_NAME_TF, DB_USER, DB_PASS);
        $query = $storeKeys->prepare("SELECT userid FROM " . TB_CLIENTS . " WHERE realmid=:realmid");
        $query->bindValue(':realmid', $realmId);
        $query->execute();
        $userRealmId = $query->fetchAll();
        if (sizeof($userRealmId[0]) > 0)
            return $userRealmId[0]['userid'];
        return null;
    }

    public static function getAllUsers()
    {
        $storeKeys = new DBO(DB_NAME_TF, DB_USER, DB_PASS);
        $query = $storeKeys->prepare("SELECT * FROM " . TB_CLIENTS);
        $query->execute();
        $userRealmId = $query->fetchAll();
        if (sizeof($userRealmId) > 0)
            return $userRealmId;
        return null;
    }

    public static function insertCredentials($userId, $token, $secret, $apiType)
    {
        $storeKeys = new DBO(DB_NAME_TF, DB_USER, DB_PASS);
        $query = $storeKeys->prepare("INSERT INTO " . TB_CREDENTIALS . " SET userid=:userid,oauthTokenSecret=:oauthTokenSecret, accounting_api=:accounting_api, oauthToken=:oauthToken, date_granted=:date_granted");
        $query->bindValue(":userid", $userId);
        $query->bindValue(":accounting_api", $apiType);
        $query->bindValue(":oauthToken", $token);
        $query->bindValue(":oauthTokenSecret", $secret);
        $query->bindValue(":date_granted", time());
        return $query->execute();
    }

    public static function updateCredentials($userId, $token, $secret)
    {
        $storeKeys = new DBO(DB_NAME_TF, DB_USER, DB_PASS);
        $query = $storeKeys->prepare("UPDATE " . TB_CREDENTIALS . " SET oauthTokenSecret=:oauthTokenSecret, oauthToken=:oauthToken, date_granted=:date_granted WHERE userid=:userid");
        $query->bindValue(":userid", $userId);
        $query->bindValue(":oauthToken", $token);
        $query->bindValue(":oauthTokenSecret", $secret);
        $query->bindValue(":date_granted", time());
        return $query->execute();
    }

    public static function insertUser($realmId, $apiType, $companyName = null)
    {
        if ($realmId == null or empty($realmId))
            return null;

        $storeKeys = new DBO(DB_NAME_TF, DB_USER, DB_PASS);

        if (!self::userExist($realmId)) {
            if ($companyName != null)
                $query = $storeKeys->prepare("INSERT INTO " . TB_CLIENTS . " SET realmid=:realmid, accounting_api=:accounting_api, company_name=:company_name, timestamp=:timestamp");
            else
                $query = $storeKeys->prepare("INSERT INTO " . TB_CLIENTS . " SET realmid=:realmid, accounting_api=:accounting_api, timestamp=:timestamp");
            $query->bindValue(":realmid", $realmId);
            $query->bindValue(":accounting_api", $apiType);
            if ($companyName != null)
                $query->bindValue(":company_name", $companyName);
            $query->bindValue(":timestamp", time());
            $query->execute();
        }
    }

    public static function userExist($realmId)
    {
        $users = new DBO(DB_NAME_TF, DB_USER, DB_PASS);
        $query = $users->prepare("SELECT * FROM " . TB_CLIENTS . " WHERE realmid=:realmid");
        $query->bindValue(":realmid", $realmId);
        $query->execute();
        $answer = $query->fetchAll();
        return (sizeof($answer) > 0);
    }

    public static function getCredentialsFromUser($realmId)
    {
        if ($realmId == null or empty($realmId))
            return null;

        $storeKeys = new DBO(DB_NAME_TF, DB_USER, DB_PASS);
        $query = $storeKeys->prepare("SELECT userid FROM " . TB_CLIENTS . " WHERE realmid=:realmid");
        $query->bindValue(":realmid", $realmId);
        $query->execute();
        $result = $query->fetchAll();
        if (sizeof($result[0]) > 0) {
            $query = $storeKeys->prepare("SELECT oauthToken, oauthTokenSecret FROM " . TB_CREDENTIALS . " WHERE userid=:userid");
            $query->bindValue(":userid", $result[0]['userid']);
            $query->execute();
            $result = $query->fetchAll();
            if (sizeof($result[0]) > 0)
                return array('oauthToken' => $result[0]['oauthToken'], 'oauthTokenSecret' => $result[0]['oauthTokenSecret']);
            else
                return null;
        }
        return null;
    }

    public static function get_root_folder($root_folder)
    {
        $getPrevPath = explode("init", $root_folder);
        $get_root = explode("\\", $getPrevPath[0]);

        $size = sizeof($get_root);
        # last item will always be empty because it is always '\'
        if ($get_root[$size - 1] != "")
            return $get_root[$size - 1];
        else
            return $get_root[$size - 2];
    }
}