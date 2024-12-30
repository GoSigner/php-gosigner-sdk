<?php

namespace GoSigner\SignerRequest;

class Security
{
    // Properties to store the flags
    private $allowAddNewDocument;
    private $allowChangeUsername;
    private $allowChangeName; //Only for eletronic provider
    private $allowChangeEmail; //Only for eletronic provider
    private $allowChangeCellphone; //Only for eletronic provider
    private $allowDocumentType;
    private $payloadCallbackUrl;
    private $payloadCodeCallbackUrl; 
    private $allowEditScope;
    private $allowEditLifetime;
    private $providerType = [];
    private $providerMfa = [];

    // List of allowed provider type
    private const ALLOWED_PROVIDER_TYPE = [
        'CLOUD',
        'LOCAL',
        'ELETRONIC'
    ];

    // List of allowed provider mfa
    private const ALLOWED_PROVIDER_MFA = [
        'geoLocation',
        'email',
        'cellphone'
    ];

    public function getAllowAddNewDocument()
    {
        return $this->allowAddNewDocument;
    }

    public function setAllowAddNewDocument(bool $allowAddNewDocument)
    {
        $this->allowAddNewDocument = $allowAddNewDocument;
    }

    public function getAllowChangeUsername()
    {
        return $this->allowChangeUsername;
    }

    public function setAllowChangeUsername(bool $allowChangeUsername)
    {
        $this->allowChangeUsername = $allowChangeUsername;
    }

    public function getAllowChangeName()
    {
        return $this->allowChangeName;
    }

    public function setAllowChangeName(bool $allowChangeName)
    {
        $this->allowChangeName = $allowChangeName;
    }

    public function getAllowChangeEmail()
    {
        return $this->allowChangeEmail;
    }

    public function setAllowChangeEmail(bool $allowChangeEmail)
    {
        $this->allowChangeEmail = $allowChangeEmail;
    }   
    
    public function getAllowChangeCellphone()
    {
        return $this->allowChangeCellphone;
    }

    public function setAllowChangeCellphone(bool $allowChangeCellphone)
    {
        $this->allowChangeCellphone = $allowChangeCellphone;
    }   

    public function getAllowDocumentType()
    {
        return $this->allowDocumentType;
    }

    public function setAllowDocumentType(bool $allowDocumentType)
    {
        $this->allowDocumentType = $allowDocumentType;
    }

    public function getPayloadCallbackUrl()
    {
        return $this->payloadCallbackUrl;
    }

    public function setPayloadCallbackUrl(bool $payloadCallbackUrl)
    {
        $this->payloadCallbackUrl = $payloadCallbackUrl;
    }

    public function getPayloadCodeCallbackUrl()
    {
        return $this->payloadCodeCallbackUrl;
    }

    public function setPayloadCodeCallbackUrl(bool $payloadCodeCallbackUrl)
    {
        $this->payloadCodeCallbackUrl = $payloadCodeCallbackUrl;
    }

    public function getAllowEditScope()
    {
        return $this->allowEditScope;
    }

    public function setAllowEditScope(bool $allowEditScope)
    {
        $this->allowEditScope = $allowEditScope;
    }

    public function getAllowEditLifetime()
    {
        return $this->allowEditLifetime;
    }

    public function setAllowEditLifetime(bool $allowEditLifetime)
    {
        $this->allowEditLifetime = $allowEditLifetime;
    }

    public function getProviderType()
    {
        return $this->providerType;
    }

    public function setProviderType(array $providerType)
    {
        foreach($providerType as $item){
            // Checks if the providerType is allowed
            if (!in_array($item, self::ALLOWED_PROVIDER_TYPE)) {
                throw new \InvalidArgumentException("Invalid providerType: $item. Allowed values are: '" . implode(self::ALLOWED_PROVIDER_TYPE,"','") . "'.");
            }
        }
        $this->providerType = $providerType;
    }

    public function addProviderType(string $providerType)
    {
        // Checks if the providerType is allowed
        if (!in_array($providerType, self::ALLOWED_PROVIDER_TYPE)) {
            throw new \InvalidArgumentException("Invalid providerType: $providerType. Allowed values are: '" . implode(self::ALLOWED_PROVIDER_TYPE,"','") . "'.");
        }

        $this->providerType[] = $providerType;
    }

    public function getProviderMfa()
    {
        return $this->providerMfa;
    }

    public function setProviderMfa(array $providerMfa)
    {
        foreach($providerMfa as $item){
            // Checks if the providerType is allowed
            if (!in_array($item, self::ALLOWED_PROVIDER_MFA)) {
                throw new \InvalidArgumentException("Invalid providerMfa: $item. Allowed values are: '" . implode(self::ALLOWED_PROVIDER_MFA,"','") . "'.");
            }
        }
        $this->providerMfa = $providerMfa;
    }

    public function addProviderMfa(string $providerMfa)
    {
        // Checks if the providerType is allowed
        if (!in_array($providerMfa, self::ALLOWED_PROVIDER_MFA)) {
            throw new \InvalidArgumentException("Invalid providerMfa: $providerMfa. Allowed values are: '" . implode(self::ALLOWED_PROVIDER_MFA,"','") . "'.");
        }

        $this->providerMfa[] = $providerMfa;
    }

    // Method to convert the Security object into an array
    public function toArray(): array
    {
        $data = [];
        $properties = [
            'allowAddNewDocument',
            'allowChangeUsername',
            'allowChangeName',
            'allowChangeEmail',
            'allowChangeCellphone',
            'allowDocumentType',
            'payloadCallbackUrl',
            'payloadCodeCallbackUrl',
            'allowEditScope',
            'allowEditLifetime',
            'providerType',
            'providerMfa'
        ];

        // Loop through the properties and add them to the array if they are not empty
        foreach ($properties as $property) {
            $value = $this->$property;

            // Check if the property is not null before adding it to the array
            if (!is_null($value)) {
                $data[$property] = $value;
            }
        }

        foreach(['providerType', 'providerMfa'] as $property){
            if(empty($data[$property])){
                unset($data[$property]);
            }
        }

        return $data;
    }

}
