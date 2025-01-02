<?php

namespace GoSigner\SignerRequest;

use GoSigner\PayloadAbstract;

use GoSigner\File;

class PayloadComposer extends PayloadAbstract
{
    private $skipCorsFile = false;
    private $callbackUrl;
    private $webhookUrl;
    private $security;
    private $ui;
    private $extraKeys;
    private $files;
    private $certificates;
    private $session = [];

    public function getSkipCorsFileUrl(){
        return $this->skipCorsFile;
    }

    public function setSkipCorsFileUrl($skipCorsFile){
        $this->skipCorsFile = $skipCorsFile;
    }

    // Getter e Setter para callbackUrl
    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }

    public function setCallbackUrl(string $callbackUrl): void
    {
        $this->callbackUrl = $callbackUrl;
    }

    // Getter e Setter para webhookUrl
    public function getWebhookUrl(): string
    {
        return $this->webhookUrl;
    }

    public function setWebhookUrl(string $webhookUrl): void
    {
        $this->webhookUrl = $webhookUrl;
    }

    // Getter e Setter para security
    public function getSecurity(): Security
    {
        return $this->security;
    }

    public function setSecurity(Security $security): void
    {
        $this->security = $security;
    }

    public function getUi(): Ui
    {
        return $this->ui;
    }

    public function setUi(Ui $ui): void
    {
        $this->ui = $ui;
    }

    public function setExtraKeys($extraKeys)
    {
        $this->extraKeys = $extraKeys;
    }

    public function getExtraKeys()
    {
        return $this->extraKeys;
    }

    public function addExtraKey($name, $value)
    {
        $this->extraKeys[] = [
            'name' => $name,
            'value' => $value
        ];
    }

    // Getter e Setter para files
    public function getFiles(): array
    {
        return $this->files;
    }

    public function setFiles(array $files): void
    {
        $this->files = $files;
    }

    public function addFile(File $file)
    {
        $this->files[] = $file;
    }

    public function setSessionDescription($description){
        $this->session = [
            "description" => $description,
            "request" => true
        ];
    }

    public function setCertificatesFilters($filters){
        
        foreach($filters as $filter){
            foreach($filter as $filterKey => $filterValue){
                switch($filterKey){
                    case "validity":
                        break;
                    case "issuer=>organizationName":
                        break;
                    case "subjectAltName=>otherName=>2.16.76.1.3.1":
                        break;
                    case "subjectAltName=>otherName=>2.16.76.1.3.3":
                        break;
                    case "cn":
                        break;
                    default:
                        throw new \RuntimeException($filterKey . " is not allowed to certificate filter");
                }
            }
        }
        
        $this->certificates = [
            'filters' => $filters
        ];
    }

    public function toArray(): array
    {
        $data = [];

        // Process 'security' object if it's not empty
        if (!empty($this->security)) {
            $data['security'] = $this->security->toArray();
        }

        // Process 'files' array, converting each object to array if not empty
        if (!empty($this->files)) {
            $data['files'] = array_map(function ($file) {
                return method_exists($file, 'toArray') ? $file->toArray() : $file;
            }, $this->files);
        
            foreach($data['files'] as $index => $file){
                if($this->skipCorsFile === true){
                    $addWithoutCorsUrl = rtrim($this->getBaseUrl(), "/") . "/resolve?download-without-cors=";
                    $addWithoutCorsUrl .= urlencode($file['src']);
                    $data['files'][$index]['src'] = $addWithoutCorsUrl;
                }
            }
        
        }

        // Process 'extraKeys' array
        if (!empty($this->extraKeys)) {
            $data['extraKeys'] = $this->extraKeys;
        }

        // Process 'ui' object if it's not empty
        if (!empty($this->ui)) {
            $data['ui'] = $this->ui->toArray();
        }

        if (!empty($this->certificates)) {
            $data['certificates'] = $this->certificates;
        }

        if (!empty($this->session)) {
            $data['session'] = $this->session;
        }

        // Process 'callbackUrl' and 'webhookUrl' if they are not empty
        if (!empty($this->callbackUrl)) {
            $data['callbackUrl'] = $this->callbackUrl;
        }

        if (!empty($this->webhookUrl)) {
            $data['webhookUrl'] = $this->webhookUrl;
        }

        return $data;
    }

    public function toJson(): string
    {
        $data = $this->toArray();
        return json_encode($data);
    }

    public function generateToken(): string
    {
        if (empty($this->credentials['key'])) {
            throw new \InvalidArgumentException("Invalid credentials key");
        }

        $payloadJson = $this->toJson();

        //Payload
        $payloadEncoded = base64_encode($payloadJson);

        //Compute HMAC
        $nonce = time() . rand(0, 9999);
        $token = $nonce . "-" . md5($nonce . $this->credentials['key'] . md5($payloadEncoded));

        return $token;
    }

    public function signForegroundLink( $onlyToken = true)
    {
        $baseUrl = $this->getBaseUrl();

        // Data for the request
        $url = rtrim($baseUrl, "/") . "/transaction-payload";
        $postData = [
            "partner" => $this->credentials['user'],
            "token" => $this->generateToken(),
            "payload" => base64_encode($this->toJson())
        ];

        // Configure headers, timeout, and context for the POST request
        $options = [
            "http" => [
                "header" => "Content-Type: application/json\r\n",
                "method" => "POST",
                "content" => json_encode($postData),
                "timeout" => 60, // Set timeout to 60 seconds
                "ignore_errors" => true, // Capture response even on HTTP error
            ],
        ];
        $context = stream_context_create($options);

        // Execute the request and capture the response
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            throw new \RuntimeException("Failed to make HTTP request to $url");
        }

        // Parse HTTP response code from headers
        $httpCode = null;
        if (isset($http_response_header)) {
            foreach ($http_response_header as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = (int) $matches[1];
                    break;
                }
            }
        }

        // Handle specific HTTP errors
        if (in_array($httpCode, [400, 401, 500])) {
            throw new \RuntimeException("API error: HTTP status code $httpCode, response: " . $response, $httpCode);
        }

        // Decode the JSON response
        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Failed to decode JSON response, status code: $httpCode, response: " . json_last_error_msg());
        }

        // Return only the token or the full link based on the option
        if ($onlyToken && !empty($responseData['payloadCode'])) {
            return $responseData['payloadCode'];
        }

        if (!$onlyToken && !empty($responseData['redirectTo'])) {
            return $responseData['redirectTo'];
        }

        throw new \RuntimeException("Failed to generate token");
    }

    public function signBackground($token)
    {
        $tokenParts = explode(":",$token);
        $username = $tokenParts[0];
        $password = $tokenParts[1];

        $passwordParts = explode("@", $password);
        $bearerToken = $passwordParts[0];
        $providerId = $passwordParts[1];

        $baseUrl = $this->getBaseUrl();

        // Data for the request
        $url = rtrim($baseUrl, "/") . "/sign";

        // Configure headers, timeout, and context for the POST request
        $options = [
            "http" => [
                "header" => "Content-Type: application/json\r\nAuthorization: Bearer " . $bearerToken . "\r\n",
                "method" => "POST",
                "content" => $this->toJson(),
                "timeout" => 60 * 3, // Set timeout to 60 seconds
                "ignore_errors" => true, // Capture response even on HTTP error
            ],
        ];
        $context = stream_context_create($options);

        // Execute the request and capture the response
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            throw new \RuntimeException("Failed to make HTTP request to $url");
        }

        // Parse HTTP response code from headers
        $httpCode = null;
        if (isset($http_response_header)) {
            foreach ($http_response_header as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = (int) $matches[1];
                    break;
                }
            }
        }

        // Handle specific HTTP errors
        if (in_array($httpCode, [400, 401, 500])) {
            throw new \RuntimeException("API error: HTTP status code $httpCode, response: " . $response, $httpCode);
        }

        // Decode the JSON response
        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Failed to decode JSON response, status code: $httpCode, response: " . json_last_error_msg());
        }

        return $responseData;
    }

}
