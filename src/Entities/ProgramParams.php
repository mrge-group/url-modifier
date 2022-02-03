<?php

namespace digidip\UrlModifier\Entities;

class ProgramParams
{
    /**
     * @var array<Rule>
     */
    private array $mods;

    private string $url;
    private string $cpi;
    private int $projectId;
    private string $affcode;
    private string $deeplink;
    private string $clickKey;
    private string $projectName;

    public function __construct(string $url, string $deeplink)
    {
        $this->cpi = '';
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

    public function setCpi(string $cpi): void
    {
        $this->cpi = $cpi;
    }

    /**
     * @return array<Rule>
     */
    public function getMods(): array
    {
        return $this->mods;
    }

    /**
     * @param array<Rule> $mods
     */
    public function setMods(array $mods): void
    {
        $this->mods = $mods;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function setProjectId(int $projectId): void
    {
        $this->projectId = $projectId;
    }

    public function getAffcode(): string
    {
        return $this->affcode;
    }

    public function setAffcode(string $affcode): void
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

    public function setClickKey(string $clickKey): void
    {
        $this->clickKey = $clickKey;
    }

    public function getProjectName(): string
    {
        return $this->projectName;
    }

    public function setProjectName(string $projectName): void
    {
        $this->projectName = $projectName;
    }
}
