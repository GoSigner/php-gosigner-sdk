# GoSigner PHP SDK

Uma biblioteca para integrar sua aplicação com os serviços do GoCrypto (https://docs.gocrypto.com.br).

## O que é a solução?

Unificamos e simplificamos o uso de diversos dispositivos criptográficos em uma única plataforma de rápida integração. Cuidamos de todo o ciclo de vida do processo desde a autenticação, tokenização e geração dos resumos criptográficos. 

## Essa solução é a ideal para o meu negócio?

Precisa oferecer suporte a assinatura e/ou criptografia nos padrões ICP-Brasil no seu sistema e não quer perder tempo desenvolvendo e mantendo uma solução para comunicação com certificados A1/A3 e ainda se preocupando com diversos protocolos de conexão com smartcards/tokens etc? Se sim, essa é a solução ideal para você conseguir estar a frente do mercado e continuar com o foco principal do seu software.

## Quais são os dispositivos compatíveis?

São compatíveis todos os certificados digitais aderentes a ICP-Brasil. O que é preciso observar é onde esse certificado está armazenado.

Os certificados comumente encontrados no mercado são os do tipo A1 e A3. Aí que está a principal diferença, os certificados do tipo A3 precisam ser emitidos e armazenados em dispositivos de segurança tais como: tokens, cartões e HSMs. Já os certificados do tipo A1 podem ser emitidos diretamente no seu computador fora de um hardware de segurança específico para tal finalidade (por isso os certificados A1 possuem um nível de classificação de segurança inferior e consequentemente só podem receber um menor tempo de expiração/vencimento)

No universo WEB acessar dispositivos de hardware via porta USB/rede (A3) ou arquivos na máquina (A1) requerem um plugin para possibilitar a comunicação do site web com um componente local instalado na máquina. A nossa plataforma possui um plugin próprio e abstrai toda a comunicação entre esses componentes, tornando indiferente para o processo de assinatura o tipo de certificado e se ele está armazenado em um arquivo, token/smartcard.

Também somos compatíveis com o mais novo padrão da ICP-Brasil, o certificado em nuvem: O certificado em nuvem pode ser um A1/A3 que fica armazenado na infraestrutura de um PSC - (Prestador de serviço de confiança credenciado ICP Brasil). Esse armazenamento em nuvem possui diversas vantagens tanto no uso quanto na segurança, com o uso do certificado em nuvem a nossa plataforma não requer o uso do plugin instalado no computador para realizar as operações criptografícas, podendo ser feitas até mesmo em um smartphone ou tablet.

---

## Instalação

Você pode instalar esta biblioteca diretamente do repositório Git utilizando o Composer.

```bash
composer require gosigner/php-gosigner-sdk:dev-main --prefer-source
```

## Exemplo de Uso (Lib PHP)

Aqui está um exemplo de como usar a biblioteca:

```php
<?php
require_once "../vendor/autoload.php";

use \GoSigner\File;
use \GoSigner\SignerRequest\PayloadComposer;
use \GoSigner\SignerRequest\Ui;
use \GoSigner\SignerRequest\Security;
use \GoSigner\SignerRequest\SignatureSetting;

const ENV = "SANDBOX"; // Para produção, use PROD
const SHARED_USER = "sample"; // Somente para teste, solicite suas credenciais para PoC
const SHARED_KEY = "5daef7d64f955e1d3e61045001036d40"; // Somente para teste

$errorMessage = '';
$errorTrace = '';

try {
    $payloadComposer = new PayloadComposer();
    $payloadComposer->setEnv(ENV);
    $payloadComposer->setCredentials(SHARED_USER, SHARED_KEY);

    $payloadComposer->setSkipCorsFileUrl(true); // Evite em produção
    $payloadComposer->setCallbackUrl("http://localhost:9000/response.php?q=");
    $payloadComposer->setWebhookUrl("https://webhook.site/38c373d8-92bc-41b3-9978-6c67aa89ad3b");

    $payloadUi = new Ui();
    $payloadUi->setUsername("04660457192"); // CPF ou CNPJ
    $payloadUi->setColor("#FFFF00");
    $payloadUi->setScope("signature_session");
    $payloadUi->setLifetime(60 * 24 * 7); // 7 dias
    $payloadComposer->setUi($payloadUi);

    $file1 = new File();
    $file1->setName("Exemplo de Arquivo 1");
    $file1->setSrc("https://www.gemboxsoftware.com/pdf/examples/204/resources/Hello%20World.pdf"); 
    $file1SignatureSettings = new SignatureSetting();
    $file1SignatureSettings->setType("DOC-pdf");
    $file1->setSignatureSetting($file1SignatureSettings);
    $payloadComposer->addFile($file1);

    $payloadData = $payloadComposer->toJson();
    $payloadToken = $payloadComposer->generateToken();

} catch (\Exception $ex) {
    $errorMessage = $ex->getMessage();
    $errorTrace = $ex->getTraceAsString();
}
?>
```

Veja também exemplos de como usar os links para embedar em Redi

---

# GoSigner PHP SDK

A library to integrate your application with GoCrypto services (https://docs.gocrypto.com.br).

---

## Installation

You can install this library directly from the Git repository using Composer.

```bash
composer require gosigner/php-gosigner-sdk:dev-main --prefer-source
```

## Example Usage

Here is an example of how to use the library:

```php
<?php
require_once "../vendor/autoload.php";

use \GoSigner\File;
use \GoSigner\SignerRequest\PayloadComposer;
use \GoSigner\SignerRequest\Ui;
use \GoSigner\SignerRequest\Security;
use \GoSigner\SignerRequest\SignatureSetting;

const ENV = "SANDBOX"; // For production, use PROD
const SHARED_USER = "sample"; // For testing only, request your credentials for PoC
const SHARED_KEY = "5daef7d64f955e1d3e61045001036d40"; // For testing only

$errorMessage = '';
$errorTrace = '';

try {
    $payloadComposer = new PayloadComposer();
    $payloadComposer->setEnv(ENV);
    $payloadComposer->setCredentials(SHARED_USER, SHARED_KEY);

    $payloadComposer->setSkipCorsFileUrl(true); // Avoid in production
    $payloadComposer->setCallbackUrl("http://localhost:9000/response.php?q=");
    $payloadComposer->setWebhookUrl("https://webhook.site/38c373d8-92bc-41b3-9978-6c67aa89ad3b");

    $payloadUi = new Ui();
    $payloadUi->setUsername("04660457192"); // CPF or CNPJ
    $payloadUi->setColor("#FFFF00");
    $payloadUi->setScope("signature_session");
    $payloadUi->setLifetime(60 * 24 * 7); // 7 days
    $payloadComposer->setUi($payloadUi);

    $file1 = new File();
    $file1->setName("Sample File 1");
    $file1->setSrc("https://www.gemboxsoftware.com/pdf/examples/204/resources/Hello%20World.pdf"); 
    $file1SignatureSettings = new SignatureSetting();
    $file1SignatureSettings->setType("DOC-pdf");
    $file1->setSignatureSetting($file1SignatureSettings);
    $payloadComposer->addFile($file1);

    $payloadData = $payloadComposer->toJson();
    $payloadToken = $payloadComposer->generateToken();

} catch (\Exception $ex) {
    $errorMessage = $ex->getMessage();
    $errorTrace = $ex->getTraceAsString();
}
?>
