<?php

require_once "../vendor/autoload.php";

use GoSigner\SignerResponse\PayloadParser;

const ENV = "SANDBOX"; //For production, use PROD
const SHARED_USER = "sample"; //For testing only, for a PoC require your credential
const SHARED_KEY = "5daef7d64f955e1d3e61045001036d40"; //For testing only, for a PoC require your credential

$q = $_GET['q'];
$errorMessage = '';
$errorTrace = '';

try {

    $payloadParser = new PayloadParser();
    $payloadParser->setEnv(ENV);
    $payloadParser->setCredentials(SHARED_USER, SHARED_KEY);

    $fromTokenApi = null;

    //Allow return only code to download (For large returns)
    //Use: $payloadSecurity->setPayloadCallbackUrl(false);
    //Use: $payloadSecurity->setPayloadCodeCallbackUrl(true);

    if (strlen($q) == 36) { //Only token
        if ($payloadParser->findByToken($q)) {
            $payloadData = json_encode($payloadParser->getPayloadData(), JSON_PRETTY_PRINT);
            $fromTokenApi = true;
        }
    }

    //Allow return full payload on callback (For small returns)
    //Use: $payloadSecurity->setPayloadCallbackUrl(true);
    //Use: $payloadSecurity->setPayloadCodeCallbackUrl(false);
    else if (strlen($q) % 4 === 0 && !empty(base64_decode($q)) && !empty(@json_decode(base64_decode($q)))) { // is a base64
        $payloadParser->setPayloadData(json_decode(base64_decode($q), true));
        $payloadData = json_encode($payloadParser->getPayloadData(), JSON_PRETTY_PRINT);
        $fromTokenApi = false;
    }

    //Unknown data on parameter
    else {
        $errorMessage = "Unknown response";
        $errorTrace = "...";
    }

    if (!is_null($fromTokenApi)) {
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

        <?php if (!empty($payloadData) && $fromTokenApi): ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Data returned through the callback URL token consumed by the
                    API</div>
                <div class="card-body">
                    <p><strong>Payload Data (Base64 decoded):</strong></p>
                    <pre><?= $payloadData; ?></pre>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($payloadData) && !$fromTokenApi): ?>
            <div class="card mb-4">
                <div class="card-header bg-success text-white">Data returned via callback URL</div>
                <div class="card-body">
                    <p><strong>Payload Data (Base64 decoded):</strong></p>
                    <pre><?= $payloadData; ?></pre>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($files)): ?>
            <div class="card mb-4">
                <div class="card-header bg-info text-white">Download signed files</div>
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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>