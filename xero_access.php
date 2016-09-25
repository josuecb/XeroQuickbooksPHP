<?php
/**
 * Created by PhpStorm.
 * User: Josue
 * Date: 8/2/2016
 * Time: 2:25 PM
 */
include __DIR__ . '/init/src/api/xero-sdk/config.php';

$XeroOAuth = oauth_config();

$XeroOAuth->config['access_token'] = $storedOauth['oauthToken'];
$XeroOAuth->config['access_token_secret'] = $storedOauth['oauthTokenSecret'];

$response = $XeroOAuth->request('GET', $XeroOAuth->url('Invoices', 'core'), array('order' => 'Total DESC'));
if ($XeroOAuth->response['code'] == 200) {
    $invoices = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);

    parseInvoices($invoices->Invoices[0], $request_time, $storeInDatabase);
} else {
    var_dump($XeroOAuth->response);
}

function parseInvoices($Invoices, $request_time, $storeInDatabase)
{
    include $_SERVER['DOCUMENT_ROOT'] . EXTENSION_ROOT . '/init/pages/xeroInvoices.html.php';
}