<?php

namespace digidip\RuleApplier\Entities;

use Illuminate\Support\Collection;

class ProgramParams
{
    private $url;
    private $cpi;
    private $mods;
    private $affcode;
    private $deeplink;
    private $clickKey;

    public function __construct(string $url, string $deeplink)
    {
        $this->url = $url;
        $this->mods = collect();
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

    public function getMods(): Collection
    {
        return $this->mods;
    }

    public function setMods(Collection $mods)
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
}