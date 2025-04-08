<?php

require_once "../vendor/autoload.php";

use \GoSigner\File;
use \GoSigner\SignerResponse\PayloadParser;
use \GoSigner\SignerRequest\PayloadComposer;
use \GoSigner\SignerRequest\SignatureSetting;

const ENV = "SANDBOX"; //For production, use PROD
const SHARED_USER = "sample"; //For testing only, for a PoC require your credential
const SHARED_KEY = "5daef7d64f955e1d3e61045001036d40"; //For testing only, for a PoC require your credential

$q = $_GET['q'];
$errorMessage = '';
$errorTrace = '';

try {

    $payloadParserSession = new PayloadParser();
    $payloadParserSession->setEnv(ENV);
    $payloadParserSession->setCredentials(SHARED_USER, SHARED_KEY);

    $fromTokenApi = null;

    //Allow return full payload on callback (For small returns)
    //Use: $payloadSecurity->setPayloadCallbackUrl(true);
    //Use: $payloadSecurity->setPayloadCodeCallbackUrl(false);
    if (strlen($q) % 4 === 0 && !empty(base64_decode($q)) && !empty(@json_decode(base64_decode($q)))) { // is a base64
        $payloadParserSession->setPayloadData(json_decode(base64_decode($q), true));
        $payloadData = json_encode($payloadParserSession->getPayloadData(), JSON_PRETTY_PRINT);
        $fromTokenApi = false;
    }

    //Unknown data on parameter
    else {
        $errorMessage = "Unknown response";
        $errorTrace = "...";
    }

    //Did everything go ok? Use session token to sign a mocked PDF file (Storage this: selected certificate and token session)
    if(!empty($payloadParserSession->getPayloadSelectedCertificateSession() || $payloadParserSession->getPayloadData()['provider'] == 'ELETRONIC')){
        
        $payloadComposer = new PayloadComposer();
        $payloadComposer->setEnv(ENV);
        $payloadComposer->setCredentials(SHARED_USER, SHARED_KEY);
        $payloadComposer->setWebhookUrl("https://webhook.site/38c373d8-92bc-41b3-9978-6c67aa89ad3b");

        $file1 = new File();
        $file1->setName("My file name sample 1");
        $file1->setDescription("My file description sample 1");
        $file1->setSrc("https://www.gemboxsoftware.com/pdf/examples/204/resources/Hello%20World.pdf"); 
        $file1SignatureSettings = new SignatureSetting();
        $file1SignatureSettings->setType("DOC-pdf");
        $file1SignatureSettings->setVisibleSignatureCustomTemplateSrc("https://gestao-online-sites.s3.sa-east-1.amazonaws.com/gocrypto.com.br/assets/tests/template.html");
        $file1SignatureSettings->setVisibleSignAppearanceConfig(1, 150, 300, 200, 28);
        $file1->setSignatureSetting($file1SignatureSettings);
        $payloadComposer->addFile($file1); 

        // $file2 = new File();
        // $file2->setName("My file name sample 2");
        // $file2->setDescription("My file description sample 2");
        // $file2->setSrc("https://www.gemboxsoftware.com/pdf/examples/204/resources/Hello%20World.pdf"); 
        // $file2SignatureSettings = new SignatureSetting();
        // $file2SignatureSettings->setType("DOC-pdf");
        // $file2SignatureSettings->setPolicy("PAdES-AD_RB");
        // $file2SignatureSettings->setVisibleSignAppearanceConfig(1, 390, 10, 200, 28);
        // $file2->setSignatureSetting($file2SignatureSettings);
        // $payloadComposer->addFile($file2);

        // echo "Selected certificate: " . $payloadParserSession->getPayloadSelectedCertificateSession() . PHP_EOL;
        // echo "Token session: " . $payloadParserSession->getPayloadTokenSession() . PHP_EOL;
        $payloadDataResponse = $payloadComposer->signBackground($payloadParserSession->getPayloadTokenSession());

        $payloadParser = new PayloadParser();
        $payloadParser->setEnv(ENV);
        $payloadParser->setCredentials(SHARED_USER, SHARED_KEY);
        $payloadParser->setPayloadData($payloadDataResponse);

        $files = $payloadParser->getFiles();
    }

} catch (\Exception $ex) {
    $errorMessage = $ex->getMessage();
    $errorTrace = $ex->getTraceAsString();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoSigner Example</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4">GoSigner Example</h1>

        <?php if (!empty($errorMessage)): ?>
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">Error</div>
                <div class="card-body">
                    <p><strong>Message: <?= $errorMessage; ?> </strong></p>
                    <pre><?= $errorTrace; ?></pre>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($payloadData) && !$fromTokenApi): ?>
            <div class="card mb-4">
                <div class="card-header bg-success text-white">Data returned via callback URL (Part 1)</div>
                <div class="card-body">
                    <p><strong>Payload Data Session (Base64 decoded):</strong></p>
                    <pre><?= $payloadData; ?></pre>
                </div>
            </div>
        <?php endif; ?>


        <?php if (!empty($payloadComposer) && !empty($payloadComposer->toArray())): ?>
            <div class="card mb-4">
                <div class="card-header bg-success text-white">Data sended to API (Part 2)</div>
                <div class="card-body">
                    <p><strong>Payload Data (Base64 decoded):</strong></p>
                    <pre><?= json_encode($payloadComposer->toArray(),JSON_PRETTY_PRINT); ?></pre>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($payloadDataResponse)): ?>
            <div class="card mb-4">
                <div class="card-header bg-success text-white">Data returned via API (Part 2)</div>
                <div class="card-body">
                    <p><strong>Payload Data (Base64 decoded):</strong></p>
                    <pre id="pluginResponse"><?= json_encode($payloadDataResponse, JSON_PRETTY_PRINT); ?></pre>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($files)): ?>
            <div class="card mb-4">
                <div class="card-header bg-info text-white">Download signed files (Part 2)</div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($files as $file): ?>
                            <div class="col-md-3 mb-4">
                                <a href="<?= $file->getSrc(); ?>" target="_blank">
                                    <button class="btn btn-info w-100 m-2">
                                        Download file <?= $file->getId(); ?>
                                    </button>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Spinner async waiting locale call -->
        <div id="loading-spinner" class="mb-3" style=" display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(255,255,255,0.7); /* fundo semitransparente */ z-index: 9999; align-items: center; justify-content: center;">
            <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Axios CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Qs CDN -->
    <script src="https://cdn.jsdelivr.net/npm/qs/dist/qs.min.js"></script>

    <?php if(!empty($payloadDataResponse['repeatRequestWithComplement'])):?>
    <script>
        window.addEventListener('load', function () {

            //Make local request (to connect locale plugin)
            var responseData = <?php echo json_encode($payloadDataResponse, JSON_PRETTY_PRINT); ?>;
                if (responseData['callPlugin'] !== undefined) {
                    let promise = new Promise((res, rej) => {

                        //Sleep await load or error
                        var img = document.createElement('img');
                        img.src = responseData['callPlugin'];

                        const spinner = document.getElementById("loading-spinner");
                        spinner.style.display = "flex"; // show spinner
                        
                        img.onload = function () {

                            spinner.style.display = "none"; // hide spinner

                            <?php
                                
                            $token = $payloadParserSession->getPayloadTokenSession();
                            $tokenParts = explode(":",$token);
                            $username = $tokenParts[0];
                            $password = $tokenParts[1];
                    
                            $passwordParts = explode("@", $password);
                            $bearerToken = $passwordParts[0];
                            $providerId = $passwordParts[1];    
                            ?>


                            var baseUrl = "<?= $payloadParserSession->getBaseUrl()?>/sign?";
                            var token = "<?= $bearerToken;?>";

                            var urlParams = [];
                            if (responseData['repeatRequestWithComplement'] !== undefined) {
                                for (var paramName in responseData['repeatRequestWithComplement']) {
                                    urlParams[paramName] = responseData['repeatRequestWithComplement'][paramName];
                                }
                            }

                            var newUrl = baseUrl + Qs.stringify(urlParams);
                            console.log("Local plugin called, with success: " + newUrl);

                            axios({
                                method: "POST",
                                data: {},
                                url: newUrl,
                                headers: {
                                    'Authorization': token
                                }
                            }).then(function (response) {
                                
                                switch(response.data.code){
                                        case "SIGNED_OK_LOCAL":
                                        case "SIGNED_OK":
                                            console.log("Assinado com sucesso");
                                            var data = response.data;
                                            document.getElementById("pluginResponse").textContent = JSON.stringify(data); //Replace on html (sample page)
                                            break;

                                    }
                            })
                            .catch(function (error) {
                                if(error.response.data.code !== undefined){
                                    switch(error.response.data.code){
                                        case "FORCE_REPEAT":
                                            console.log("Request repeat this request"); //Attention for loop prevent
                                            break;
                                        case "INVALID_PARAM":
                                            alert("Parâmetro inválido ao assinar");
                                            break;
                                        case "INVALID_DEFAULT_CERTIFICATE":
                                            alert("Não foi selecionado um certificado");
                                            break;
                                        case "DOCUMENTS_NOT_SIGNED":
                                            alert("Os documentos ainda não foram assinados, aguarde alguns segundos e tente novamente");
                                            break;
                                        case "INVALID_TRANSACTION_ID":
                                            alert("ID de transação (assinatura) inválida");
                                            break;
                                        case "INVALID_TRANSACTION_ACTION":
                                            alert("Ação invaĺida na transação");
                                            break;
                                        case "INVALID_RAW_SIGNATURE":
                                            alert("Falha ao checar bytes de criptografia e integridade após a assinatura");
                                            break;
                                        case "UNKNOWN_ERROR_WHEN_SIGNING_0":
                                        case "UNKNOWN_ERROR_WHEN_SIGNING_1":
                                        case "UNKNOWN_ERROR_WHEN_SIGNING_3":
                                            alert("Erro interno durante a assinatura");
                                            break;

                                    }
                                }
                                else{
                                    alert("Um erro desconhecido ocorreu");
                                }
                                return 0;
                            });
                        };
                        img.onerror = function () {

                            spinner.style.display = "none"; // hide spinner

                            alert("Fail on call locale plugin");
                        };
                    });
                }
            });
    </script>
    <?php endif; ?>
</body>

</html>