<?php 
    require_once "request-session-eletronic.php"; //Include "request" for example
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoSigner Sample Simple</title>
    <!-- BootstrapVue CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-vue@2.21.2/dist/bootstrap-vue.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5" id="app">
        <h1 class="mb-4">GoSigner Sample</h1>

        <?php if(!empty($errorMessage)):?>
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">Error</div>
            <div class="card-body">
                <p><strong>Message: <?= $errorMessage;?> </strong></p>
                <pre><?= $errorTrace; ?></pre>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($payloadData) || !empty($payloadToken)):?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Direct Link by Concat-URL</div>
            <div class="card-body">
                <p><strong>Payload Data (Base64 encoded):</strong></p>
                <pre><?= base64_encode($payloadData) ?></pre>
                <p><strong>Payload Token:</strong> <?= $payloadToken ?></p>
                <p><strong>Partner/SharedUser:</strong> <?= SHARED_USER ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($apiToken) || !empty($apiUiLinkWithToken)):?>
        <div class="card mb-4">
            <div class="card-header bg-success text-white">Direct Link by API</div>
            <div class="card-body">
                <p><strong>API Token (onlyToken):</strong> <?= $apiToken ?></p>
                <p><strong>Link to Redirect:</strong> <a href="<?= $apiUiLinkWithToken ?>" target="_blank">Open Link</a></p>
                <p><strong>Link to Embed:</strong> <a href="<?= $apiUiLinkWithToken ?>&iframe=true" target="_blank">Open Embedded Link</a></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <!-- Vue.js -->
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.min.js"></script>
    <!-- BootstrapVue -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-vue@2.21.2/dist/bootstrap-vue.min.js"></script>
</body>
</html>
