<?php

namespace GoSigner\SignerRequest;

class Ui
{
    private $name; //Only for eletronic provider
    private $email; //Only for eletronic provider
    private $cellphone; //Only for eletronic provider
    private $username;
    private $scope;
    private $lifetime;
    private $button;
    private $bg;
    private $color;
    private $callback;
    private $preferPreview;

    // List of allowed scopes
    private const ALLOWED_SCOPES = [
        'single_signature',
        'multi_signature',
        'signature_session'
    ];

    // Getter and Setter for 'name'
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    // Getter and Setter for 'email'
    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    // Getter and Setter for 'cellphone'
    public function getCellphone(): string
    {
        return $this->cellphone;
    }

    public function setCellphone(string $cellphone): void
    {
        $this->cellphone = $cellphone;
    }

    // Getter and Setter for 'username'
    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        // Validates that the username is a valid CPF (11 digits) or CNPJ (14 digits)
        if (!preg_match('/^\d{11}$|^\d{14}$/', $username)) {
            throw new \InvalidArgumentException("Username must be a valid CPF (11 digits) or CNPJ (14 digits).");
        }
        $this->username = $username;
    }

    // Getter and Setter for 'scope'
    public function getScope(): string
    {
        return $this->scope;
    }

    public function setScope(string $scope): void
    {
        // Checks if the scope is allowed
        if (!in_array($scope, self::ALLOWED_SCOPES)) {
            throw new \InvalidArgumentException("Invalid scope: $scope. Allowed values are: 'single_signature', 'multi_signature', 'signature_session'.");
        }
        $this->scope = $scope;
    }

    // Getter and Setter for 'lifetime'
    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    public function setLifetime(int $lifetime): void
    {
        // Validates that lifetime is greater than zero
        if ($lifetime <= 0) {
            throw new \InvalidArgumentException("Lifetime must be greater than zero.");
        }
        $this->lifetime = $lifetime;
    }

    // Getter and Setter for 'button'
    public function getButton(): string
    {
        return $this->button;
    }

    public function setButton(string $button): void
    {
        $this->button = $button;
    }

    // Getter and Setter for 'bg'
    public function getBg(): string
    {
        return $this->bg;
    }

    public function setBg(string $bg): void
    {
        // Validates that the background color is in hexadecimal format
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $bg)) {
            throw new \InvalidArgumentException("Background color must be in hexadecimal format.");
        }
        $this->bg = $bg;
    }

    // Getter and Setter for 'color'
    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        // Validates that the color is in hexadecimal format
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            throw new \InvalidArgumentException("Color must be in hexadecimal format.");
        }
        $this->color = $color;
    }

    // Getter and Setter for 'callback'
    public function getCallback(): string
    {
        return $this->callback;
    }

    public function setCallback(string $callback): void
    {
        // Validates that the callback is a valid URL
        if (!filter_var($callback, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("Callback must be a valid URL.");
        }
        $this->callback = $callback;
    }

    // Getter and Setter for 'preferPreview'
    public function getPreferPreview(): string
    {
        return $this->preferPreview;
    }

    public function setPreferPreview(string $preferPreview): void
    {
        // Validates that preferPreview is either 'file' or 'description'
        if (!in_array($preferPreview, ['file', 'description'])) {
            throw new \InvalidArgumentException("preferPreview must be either 'file' or 'description'.");
        }
        $this->preferPreview = $preferPreview;
    }

    // Method to convert the object to an array
    public function toArray(): array
    {
        $data = [];
        $properties = [
            'name',
            'email',
            'cellphone',
            'username',
            'scope',
            'lifetime',
            'button',
            'bg',
            'color',
            'callback',
            'preferPreview'
        ];

        foreach ($properties as $property) {
            $value = $this->$property;

            // Only add non-null properties to the result array
            if (!is_null($value)) {
                $data[$property] = $value;
            }
        }

        return $data;
    }

}
