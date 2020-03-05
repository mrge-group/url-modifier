<?php

namespace digidip\RuleApplier\Entities;

class Rule
{
    const LTRIM_TYPE = 'ltrim';
    const TRIM2Q_TYPE = 'trim2q';
    const REGEXP_TYPE = 'regexp';
    const GEENAPP_TYPE = 'geenapp';
    const LOMADEE_TYPE = 'lomadee';
    const ADD_PARAM_TYPE = 'addparam';
    const DEL_PARAM_TYPE = 'delparam';
    const URL_EMBED_TYPE = 'url_embed';
    const BASE64_TYPE = 'base64encode';
    const RAW_PARAM_TYPE = 'appendrawparam';
    const ENCODE_PARAM_TYPE = 'encodeparamvalue';

    private $sort;
    private $type;
    private $value1;
    private $value2;
    private $modKeys;

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort)
    {
        $this->sort = $sort;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function getValue1(): ?string
    {
        return $this->value1;
    }

    public function setValue1(?string $value1)
    {
        $this->value1 = $value1;
    }

    public function getValue2(): ?string
    {
        return $this->value2;
    }

    public function setValue2(?string $value2)
    {
        $this->value2 = $value2;
    }

    public function getModKeys(): array
    {
        return $this->modKeys;
    }

    public function setModKeys(array $modKeys)
    {
        $this->modKeys = $modKeys;
    }
}
