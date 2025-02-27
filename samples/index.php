<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoSigner Samples</title>
    <!-- BootstrapVue CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-vue@2.21.2/dist/bootstrap-vue.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5" id="app">
        <h1 class="mb-4">GoSigner Sample</h1>

        <div>
            <h4 class="text text-center">Digital Signature</h4>
            <hr>
            <p>Signature direct</p>
            <a href="/request-sign-view-modal.php"><button class="btn btn-info">Start With - Sign View Modal (embed)</button></a>
            <a href="/request-sign-view-simple.php"><button class="btn btn-info">Start With - Sign Simple URL (popup or redirect)</button></a>
        
            <hr>
            <p>Open session, and sign after (only CLOUD provider)</p>
            <a href="/request-session-view-modal.php"><button class="btn btn-info">Start With - Sign View Modal (embed)</button></a>
            <a href="/request-session-view-simple.php"><button class="btn btn-info">Open session - Sign Simple URL (popup or redirect)</button></a>
        </div>

        <div>
            <h4 class="text text-center m-4">Eletronic Signature</h4>

            <hr>
            <p>Signature direct</p>
            <a href="/request-sign-eletronic-view-modal.php"><button class="btn btn-info">Start With - Sign View Modal (embed)</button></a>
            <a href="/request-sign-eletronic-view-simple.php"><button class="btn btn-info">Start With - Sign Simple URL (popup or redirect)</button></a>
        
            <hr>
            <p>Open session, and sign after</p>
            <a href="/request-session-eletronic-view-modal.php"><button class="btn btn-info">Start With - Sign View Modal (embed)</button></a>
            <a href="/request-session-eletronic-view-simple.php"><button class="btn btn-info">Open session - Sign Simple URL (popup or redirect)</button></a>
        </div>
    </div>
</body>
<!-- Vue.js -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.min.js"></script>
<!-- BootstrapVue -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-vue@2.21.2/dist/bootstrap-vue.min.js"></script>
</html>
