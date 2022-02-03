<?php

namespace digidip\UrlModifier\Entities;

class Rule
{
    public const LTRIM_TYPE = 'ltrim';
    public const TRIM2Q_TYPE = 'trim2q';
    public const REGEXP_TYPE = 'regexp';
    public const LOMADEE_TYPE = 'lomadee';
    public const ADD_PARAM_TYPE = 'addparam';
    public const DEL_PARAM_TYPE = 'delparam';
    public const URL_EMBED_TYPE = 'url_embed';
    public const BASE64_TYPE = 'base64encode';
    public const RAW_PARAM_TYPE = 'appendrawparam';
    public const ENCODE_PARAM_TYPE = 'encodeparamvalue';

    /**
     * @var array<string, int|string>
     */
    private array $modKeys;

    private int $sort;
    private string $type;
    private ?string $value1;
    private ?string $value2;

    public function __construct(int $sort, string $type, string $value1 = null, string $value2 = null)
    {
        $this->sort = $sort;
        $this->type = $type;
        $this->value1 = $value1;
        $this->value2 = $value2;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array<string, int|string>
     */
    public function getModKeys(): array
    {
        return $this->modKeys;
    }

    /**
     * @param array<string, int|string> $modKeys
     */
    public function setModKeys(array $modKeys): void
    {
        $this->modKeys = $modKeys;
    }

    public function getValue1(): ?string
    {
        return $this->value1;
    }

    public function setValue1(?string $value1): void
    {
        $this->value1 = $value1;
    }

    public function getValue2(): ?string
    {
        return $this->value2;
    }

    public function setValue2(?string $value2): void
    {
        $this->value2 = $value2;
    }
}
