<?php

namespace GoSigner\SignerResponse;

use GoSigner\PayloadAbstract;

use GoSigner\File;

class PayloadParser extends PayloadAbstract
{
    private $payloadData;

    public function setPayloadData($payloadData){
        $this->payloadData = $payloadData;
    }

    public function getPayloadData(){
        return $this->payloadData;
    }

    public function getFiles(){
        $files = [];
        if(!empty($this->payloadData)){
            foreach($this->payloadData['documents'] as $document){
                $file = new File();
                $file->setId($document['id']);
                $file->setSrc($document['downloadLink']);
                $files[] = $file;
            }
        }

        return $files;
    }

    public function getPayloadCode(){
        if(!empty($this->payloadData['payloadCode'])){
            $this->payloadData['payloadCode'];
        }

        return null;
    }

    public function getDocuments(){
        return $this->getFiles(); //@DEPRECATED
    }

    public function findByToken($token){

        $baseUrl = $this->getBaseUrl();

        // Data for the request
        $url = rtrim($baseUrl, "/") . "/transaction-payload/{$token}";

        // Configure headers, timeout, and context for the GET request
        $options = [
            "http" => [
                "header" => "Accept: application/json\r\n",
                "method" => "GET",
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

        switch($responseData['code']){
            case "SIGNED_OK":
            case "SIGNED_OK_LOCAL":
            case "TRANSACTION_FETCHED":
                $this->payloadData = $responseData;
                return true;
            default:
                throw new \RuntimeException("Failed to retrieve payload by token, error: " . $responseData['code']);
        }
    }
    
}
