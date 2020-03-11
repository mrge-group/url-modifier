<?php

namespace digidip\UrlModifier\Entities;

class ProgramParams
{
    private $url;
    private $cpi;
    private $mods;
    private $affcode;
    private $deeplink;
    private $clickKey;
    private $projectName;

    public function __construct(string $url, string $deeplink)
    {
        $this->mods = [];
        $this->url = $url;
        $this->deeplink = $deeplink;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getCpi(): string
    {
        return $this->cpi;
    }

    public function setCpi(string $cpi)
    {
        $this->cpi = $cpi;
    }

    public function getMods(): array
    {
        return $this->mods;
    }

    public function setMods(array $mods)
    {
        $this->mods = $mods;
    }

    public function getAffcode(): string
    {
        return $this->affcode;
    }

    public function setAffcode(string $affcode)
    {
        $this->affcode = $affcode;
    }

    public function getDeeplink(): string
    {
        return $this->deeplink;
    }

    public function getClickKey(): string
    {
        return $this->clickKey;
    }

    public function setClickKey(string $clickKey)
    {
        $this->clickKey = $clickKey;
    }

    public function getProjectName(): string
    {
        return $this->projectName;
    }

    public function setProjectName(string $projectName)
    {
        $this->projectName = $projectName;
    }
}