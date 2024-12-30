<?php

namespace GoSigner;

use GoSigner\SignerRequest\SignatureSetting;

class File
{
    private $id;
    private $name;
    private $description;
    private $src;
    private $signatureSetting;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getSrc(): string
    {
       return $this->src;
    }

    public function setSrc(string $src): void
    {
        $this->src = $src;
    }

    public function getSignatureSetting(): SignatureSetting
    {
        return $this->signatureSetting;
    }

    public function setSignatureSetting(SignatureSetting $signatureSetting): void
    {
        $this->signatureSetting = $signatureSetting;
    }

    public function getBytes(): string
    {
        if (strpos($this->src, 'data:') === 0) {
            // Handle RFC 2397 data URI
            $parts = explode(',', $this->src, 2);

            if (count($parts) !== 2) {
                throw new \InvalidArgumentException('Invalid data URI format');
            }

            $metadata = $parts[0];
            $data = $parts[1];

            // Check if the data is base64 encoded
            if (strpos($metadata, 'base64') !== false) {
                $decodedData = base64_decode($data, true);
                if ($decodedData === false) {
                    throw new \RuntimeException('Failed to decode base64 data');
                }
                return $decodedData;
            }

            // Otherwise, return raw data (percent-decoded)
            return urldecode($data);
        } elseif (filter_var($this->src, FILTER_VALIDATE_URL)) {
            // Validate URL and perform secure HTTP GET request
            $contextOptions = [
                'http' => [
                    'method' => 'GET',
                    'timeout' => 5,
                    'header' => [
                        'User-Agent: GoSignerFileDownloader/1.0'
                    ]
                ]
            ];
            $context = stream_context_create($contextOptions);

            // Suppress errors and validate the response
            $response = @file_get_contents($this->src, false, $context);

            if ($response === false) {
                throw new \RuntimeException('Failed to download file from URL');
            }

            return $response;
        } else {
            throw new \InvalidArgumentException('Invalid src format');
        }
    }

    // Method toArray to convert the File object into an array
    public function toArray(): array
    {
        $data = [];
        $properties = [
            'id',
            'name',
            'description',
            'src',
            'signatureSetting'
        ];

        // Iterate through properties and add to array if not empty
        foreach ($properties as $property) {
            $value = $this->$property;

            // Check if the property is not null before adding to the array
            if (!is_null($value)) {
                // Check if the property is an object (like signatureSetting) and convert to array if so
                $data[$property] = is_object($value) ? $value->toArray() : $value;
            }
        }

        return $data;
    }
}
