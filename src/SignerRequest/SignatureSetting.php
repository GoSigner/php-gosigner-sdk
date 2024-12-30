<?php

namespace GoSigner\SignerRequest;

class SignatureSetting
{
    private $type;
    private $visibleSignImg;
    private $visibleSignPage;
    private $visibleSignX;
    private $visibleSignY;
    private $visibleSignWidth;
    private $visibleSignHeight;
    private $policy;

    // List of allowed types
    private const ALLOWED_TYPES = [
        'CFMBR-2.16.76.1.12.1.1', // Medication prescription
        'CFMBR-2.16.76.1.12.1.2', // Medical certificate
        'CFMBR-2.16.76.1.12.1.3', // Request for examination
        'CFMBR-2.16.76.1.12.1.4', // Laboratory report
        'CFMBR-2.16.76.1.12.1.5', // Discharge summary
        'CFMBR-2.16.76.1.12.1.6', // Clinical attendance record
        'CFMBR-2.16.76.1.12.1.7', // Medication dispensing
        'CFMBR-2.16.76.1.12.1.8', // Vaccination
        'CFMBR-2.16.76.1.12.1.11', // Medical report
        'DOC-pdf',                // Other PDF files
        'hash'                    // Hash for CMS signature
    ];

    // List of allowed policies
    private const ALLOWED_POLICIES = [
        'CAdES-AD_RB',  // CAdES policy
        'CAdES-AD_RT',  // CAdES policy with timestamp
        'PAdES-AD_RB',  // PAdES policy
        'PAdES-AD_RT',  // PAdES policy with timestamp
        ''              // Allows empty value
    ];

    // Getter and Setter for 'type'
    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new \InvalidArgumentException("Invalid signature type: $type");
        }
        $this->type = $type;
    }

    // Getter and Setter for 'policy'
    public function getPolicy()
    {
        return $this->policy;
    }

    public function setPolicy(string $policy): void
    {
        if (!in_array($policy, self::ALLOWED_POLICIES)) {
            throw new \InvalidArgumentException("Invalid signature policy: $policy");
        }
        $this->policy = $policy;
    }

    // Getters and Setters for other properties
    public function getVisibleSignImg():string
    {
        return $this->visibleSignImg;
    }

    public function setVisibleSignImg(string $visibleSignImg): void
    {
        $this->visibleSignImg = $visibleSignImg;
    }

    public function getVisibleSignPage(): int
    {
        return $this->visibleSignPage;
    }

    public function setVisibleSignPage(int $visibleSignPage): void
    {
        $this->visibleSignPage = $visibleSignPage;
    }

    public function getVisibleSignX(): int
    {
        return $this->visibleSignX;
    }

    public function setVisibleSignX(int $visibleSignX): void
    {
        $this->visibleSignX = $visibleSignX;
    }

    public function getVisibleSignY(): int
    {
        return $this->visibleSignY;
    }

    public function setVisibleSignY(int $visibleSignY): void
    {
        $this->visibleSignY = $visibleSignY;
    }

    public function getVisibleSignWidth(): int
    {
        return $this->visibleSignWidth;
    }

    public function setVisibleSignWidth(int $visibleSignWidth): void
    {
        $this->visibleSignWidth = $visibleSignWidth;
    }

    public function getVisibleSignHeight(): int
    {
        return $this->visibleSignHeight;
    }

    public function setVisibleSignHeight(int $visibleSignHeight): void
    {
        $this->visibleSignHeight = $visibleSignHeight;
    }

    // Method to configure the appearance of the visible signature
    public function setVisibleSignAppearanceConfig(int $page, int $x, int $y, int $width, int $height)
    {
        $this->visibleSignPage = $page;
        $this->visibleSignX = $x;
        $this->visibleSignY = $y;
        $this->visibleSignWidth = $width;
        $this->visibleSignHeight = $height;
    }

    // Method to convert the SignatureSettings object into an array
    public function toArray(): array
    {
        $data = [];
        $properties = [
            'type',
            'policy',
            'visibleSignImg',
            'visibleSignPage',
            'visibleSignX',
            'visibleSignY',
            'visibleSignWidth',
            'visibleSignHeight'
        ];

        // Check each property and add it to the array if it is not empty
        foreach ($properties as $property) {
            $value = $this->$property;

            // Check if the property is not null before adding it to the array
            if (!is_null($value)) {
                $data[$property] = $value;
            }
        }

        return $data;
    }
}
