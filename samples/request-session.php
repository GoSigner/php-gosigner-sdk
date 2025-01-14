<?php

require_once "../vendor/autoload.php";

use \GoSigner\File;
use \GoSigner\SignerRequest\PayloadComposer;
use \GoSigner\SignerRequest\Ui;
use \GoSigner\SignerRequest\Security;
use \GoSigner\SignerRequest\SignatureSetting;

const ENV = "SANDBOX"; //For production, use PROD
const SHARED_USER = "sample"; //For testing only, for a PoC require your credential
const SHARED_KEY = "5daef7d64f955e1d3e61045001036d40"; //For testing only, for a PoC require your credential

$errorMessage = '';
$errorTrace = '';

try{
    $payloadComposer = new PayloadComposer();
    $payloadComposer->setEnv(ENV);
    $payloadComposer->setCredentials(SHARED_USER, SHARED_KEY);

    //For testing only, avoid using this in production
    $payloadComposer->setSkipCorsFileUrl(true); 


    // $payloadComposer->setCallbackUrl("https://meulink.com.br?token=");
    $payloadComposer->setCallbackUrl("http://localhost:9000/response-session.php?q=");
    $payloadComposer->setWebhookUrl("https://webhook.site/38c373d8-92bc-41b3-9978-6c67aa89ad3b");

    $payloadUi = new Ui();
    $payloadUi->setButton("Abrir sessão");
    // $payloadUi->setUsername("04660457192"); // CPF or CNPJ
    $payloadUi->setColor("#FFFF00");
    $payloadUi->setBg("#f9f9f9");
    $payloadUi->setScope("signature_session");
    $payloadUi->setLifetime(60 * 60 * 24 * 7); // 7 days in seconds
    $payloadUi->setPreferPreview("description");
    $payloadComposer->setUi($payloadUi);

    // $filters = [
    //     [
    //         "validity" => "now",
    //         "issuer=>organizationName" => "ICP-Brasil",
    //         "subjectAltName=>otherName=>2.16.76.1.3.1" => "********04660457192**************************"
    //     ]
    // ];
    // $payloadComposer->setCertificatesFilters($filters);

    $payloadSecurity = new Security();
    // $payloadSecurity->setAllowChangeUsername(false);
    $payloadSecurity->setAllowEditLifetime(false);
    $payloadSecurity->setAllowEditScope(false);
    $payloadSecurity->setAllowAutocontinue(true);

    $payloadSecurity->addProviderType("CLOUD"); //Only local is accept for session
    $payloadComposer->setSecurity($payloadSecurity);

    $payloadComposer->setSessionDescription("Ao autorizar, você permite que o software EXEMPLO 123 utilize o seu certificado. Finalidades de Xpto1, xpto2 etc");

    $payloadData = $payloadComposer->toJson();

    $payloadToken = $payloadComposer->generateToken();
    $apiToken = $payloadComposer->signForegroundLink(true);
    $apiUiLinkWithToken = $payloadComposer->signForegroundLink(false);
}
catch(\Exception $ex){
    $errorMessage = $ex->getMessage();
    $errorTrace = $ex->getTraceAsString();
}
?>