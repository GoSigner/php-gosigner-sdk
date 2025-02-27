<?php 
    require_once "request-sign-eletronic.php"; //Include "request" for example
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoSigner Sample Modal</title>
    <!-- BootstrapVue CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-vue@2.21.2/dist/bootstrap-vue.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5" id="app">
        <h1 class="mb-4">GoSigner Sample</h1>

        <h5 v-if="redirectMessage">{{redirectMessage}}</h5>

        <?php if(!empty($errorMessage)):?>
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">Error</div>
            <div class="card-body">
                <p><strong>Message: <?= $errorMessage;?> </strong></p>
                <pre><?= $errorTrace; ?></pre>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($apiToken) || !empty($apiUiLinkWithToken)):?>
            <button v-bind:disable="redirectMessage"  class="btn btn-info" @click="openModal">Open modal</button>
        <?php endif; ?>
        
        <!-- Sign Modal:start -->
        <b-modal id="signModal" hide-footer size="lg" ref="signModal">
            <iframe style="border:none" :src="modalSignLink" width="100%" height="800" allow="geolocation"></iframe>
        </b-modal>
        <!-- Sign Modal:end -->

    </div>
</body>
<!-- Vue.js -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.min.js"></script>
<!-- BootstrapVue -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-vue@2.21.2/dist/bootstrap-vue.min.js"></script>
<script>
    new Vue({
        el: "#app",
        data() {
            return {
                modalSignLink: "<?= $apiUiLinkWithToken;?>&iframe=true", // Link to open modal
                redirectMessage: '', // Variable to display the redirect message
            };
        },
        methods: {
            openModal() {
                this.$refs.signModal.show(); //Show modal
            },
            delayedRedirect(url) {
                this.redirectMessage = `You will be redirected to: ${url}`; // Set the redirect message
                setTimeout(() => {
                window.location.href = url; // Redirect the user after 3 seconds
                }, 3000); // 3000ms = 3 seconds
            },
            modalSignListenerEvent(event) {
                // if (event.data === 'signedOk') {
                //     this.$refs.signModal.hide(); // Hide modal
                // }

                // Splits the string at the first occurrence of the '|' separator
                const [prefix, url] = event.data.split('|');

                // Check if the first part is 'callbackUrl'
                if (prefix === 'callbackUrl') {
                    this.$refs.signModal.hide(); //  Hide modal
                    this.delayedRedirect(url);
                    // alert(`Redirecionar usuário para: ${url}`); //Here you retrieve the initiated callback endpoint, it is possible to trigger something in the backend from here, or redirect the user for example
                    // console.log(event);
                }
            }
        },
        mounted() {
            window.addEventListener('message', this.modalSignListenerEvent, false);
        },
        beforeDestroy() {
            window.removeEventListener('message', this.modalSignListenerEvent, false);
        }
    });
</script>
</html>
