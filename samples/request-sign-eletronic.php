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
    $payloadComposer->setCallbackUrl("http://localhost:9000/response-sign.php?q=");
    $payloadComposer->setWebhookUrl("https://webhook.site/38c373d8-92bc-41b3-9978-6c67aa89ad3b");

    $payloadUi = new Ui();
    $payloadUi->setUsername("04660457192"); // CPF or CNPJ
    $payloadUi->setName("Paulo Filipe");
    $payloadUi->setCellphone("62991838359");
    $payloadUi->setEmail("paulo@gosigner.com.br");
    $payloadUi->setColor("#FFFF00");
    $payloadUi->setScope("signature_session");
    $payloadUi->setLifetime(60 * 60 * 24 * 7); // 7 days in seconds
    $payloadUi->setPreferPreview("file");
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
    $payloadSecurity->setAllowEditLifetime(true);
    $payloadSecurity->setAllowEditScope(true);

    //Allow return only code to download
    $payloadSecurity->setPayloadCallbackUrl(false);
    $payloadSecurity->setPayloadCodeCallbackUrl(true);

    //Allow return full payload on callback
    // $payloadSecurity->setPayloadCallbackUrl(true);
    // $payloadSecurity->setPayloadCodeCallbackUrl(false);

    $payloadSecurity->addProviderType("ELETRONIC");
    $payloadSecurity->addProviderMfa("geoLocation");
    $payloadSecurity->addProviderMfa("email");
    $payloadSecurity->addProviderMfa("cellphone");
    $payloadComposer->setSecurity($payloadSecurity);

    $file1 = new File();
    $file1->setName("My file name sample 1");
    $file1->setDescription("My file description sample 1");
    $file1->setSrc("https://www.gemboxsoftware.com/pdf/examples/204/resources/Hello%20World.pdf"); 
    $file1SignatureSettings = new SignatureSetting();
    $file1SignatureSettings->setType("DOC-pdf");
    $file1SignatureSettings->setVisibleSignAppearanceConfig(1, 390, 10, 200, 28);
    $file1->setSignatureSetting($file1SignatureSettings);
    $payloadComposer->addFile($file1); 

    $file2 = new File();
    $file2->setName("My file name sample 2");
    $file2->setDescription("My file description sample 2");
    $file2->setSrc("https://www.gemboxsoftware.com/pdf/examples/204/resources/Hello%20World.pdf"); 
    $file2SignatureSettings = new SignatureSetting();
    $file2SignatureSettings->setType("DOC-pdf");
    $file2SignatureSettings->setPolicy("PAdES-AD_RB");
    $file2SignatureSettings->setVisibleSignatureCustomTemplateSrc("https://gestao-online-sites.s3.sa-east-1.amazonaws.com/gocrypto.com.br/assets/tests/template.html");
    $file2SignatureSettings->setVisibleSignAppearanceConfig(1, 390, 300, 200, 28);
    $file2->setSignatureSetting($file2SignatureSettings);
    $payloadComposer->addFile($file2);

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