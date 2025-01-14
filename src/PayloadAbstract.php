<?php

namespace GoSigner;

abstract class PayloadAbstract
{
    protected $credentials = [
        'user' => null,
        'key' => null,
    ];

    protected $env = "STAGE";

    public function setCredentials($user, $key)
    {
        $this->credentials['user'] = $user;
        $this->credentials['key'] = $key;
    }

    public function getEnv(): string {
        return $this->env;
    }

    public function setEnv($env){
        $this->env = $env;
    }

    public function getBaseUrl(){
        // Configure base URLs based on the environment
        switch ($this->env) {
           case "PROD":
               $baseUrl = "https://api.gosigner.com.br";
               break;
           case "SANDBOX":
           case "STAGE":
               $baseUrl = "https://api-stage.gosigner.com.br";
               break;
           case "DEV":
               $baseUrl = "https://api-dev.gosigner.com.br";
               break;
           case "LOCAL":
               $baseUrl = "http://192.168.0.127:8081"; // Internal Gosigner usage
               break;
           default:
               throw new \InvalidArgumentException("Invalid environment: " . $this->env);
       }

       return $baseUrl;
   }
}
