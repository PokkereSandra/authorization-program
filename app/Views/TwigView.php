<?php

namespace App\Views;

class TwigView
{
    private string $templatePath;
    private array $data;

    public function __construct(string $templatePath, array $data)
    {
        $this->templatePath = $templatePath;
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }
}