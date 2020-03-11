<?php

namespace digidip\UrlModifier;

use digidip\UrlModifier\Entities\ProgramParams;
use digidip\UrlModifier\Entities\Rule;
use Spatie\Url\Url;

class UrlModifier
{
    const AFFCODE = '{affcode}';

    public function getLink(ProgramParams $params): string
    {
        $url = $params->getUrl();
        $deeplink = $params->getDeeplink();

        $modKeys = [
            '{cpi}'          => $params->getCpi(),
            self::AFFCODE    => $params->getAffcode(),
            '{click_key}'    => $params->getClickKey(),
            '{project_name}' => $params->getProjectName()
        ];

        $mods = $params->getMods();
        usort($mods, function ($firstRule, $secondRule) {
            /** @var Rule $firstRule */
            /** @var Rule $secondRule */
            if ($firstRule->getSort() < $secondRule->getSort()) {
                return -1;
            }

            return (($firstRule->getSort() > $secondRule->getSort()) ? 1 : 0);
        });

        /** @var Rule $rule */
        foreach ($mods as $rule) {
            $rule->setModKeys($modKeys);
            $url = $this->applyRule($url, $rule);
        }

        $deeplink = str_replace(array_keys($modKeys), $modKeys, $deeplink);

        return $this->encodeUrl($deeplink, $url);
    }

    private function applyRule(string $previousUrl, Rule $rule): string
    {
        $url = $previousUrl;

        if (empty($rule->getValue1())) {
            return $this->applySimpleRule($previousUrl, $rule);
        }

        $rule->setValue1(str_replace(array_keys($rule->getModKeys()), $rule->getModKeys(), $rule->getValue1()));

        if (!empty($rule->getValue2())) {
            $rule->setValue2(str_replace(array_keys($rule->getModKeys()), $rule->getModKeys(), $rule->getValue2()));
        }

        switch ($rule->getType()) {
            case Rule::ADD_PARAM_TYPE:
                $url = $this->addParam($url, $rule->getValue1(), $rule->getValue2());
                break;
            case Rule::RAW_PARAM_TYPE:
                $url = $this->appendRawParam($url, $rule->getValue1(), $rule->getValue2());
                break;
            case Rule::DEL_PARAM_TYPE:
                $url = $this->delParam($url, $rule->getValue1());
                break;
            case Rule::LTRIM_TYPE:
                $url = $this->lTrim($url, $rule->getValue1());
                break;
            case Rule::REGEXP_TYPE:
                $url = $this->regexp($url, $rule->getValue1(), $rule->getValue2());
                break;
            case Rule::URL_EMBED_TYPE:
                $url = $this->encodeUrl($rule->getValue1(), $url);
                break;
            default:
                return $this->encodeParamValue($url, $rule->getValue1());
        }

        return $url;
    }

    private function applySimpleRule(string $previousUrl, Rule $rule): string
    {
        $url = $previousUrl;

        switch ($rule->getType()) {
            case Rule::TRIM2Q_TYPE:
                $url = $this->trim2q($url);
                break;
            case Rule::BASE64_TYPE:
                $url = base64_encode($url);
                break;
            case Rule::LOMADEE_TYPE:
                $url = $this->lomadee($url, $rule->getModKeys()[self::AFFCODE]);
                break;
            default:
                return $url;
        }

        return $url;
    }

    private function encodeUrl(string $previousDeeplink, string $url): string
    {
        $deeplink = $previousDeeplink;
        $patternFlexEncoding = '/{url_encoded_([0-9]+)}/';

        preg_match($patternFlexEncoding, $deeplink, $times);

        if (empty($times)) {
            return $deeplink;
        }

        $deeplink = str_replace('{url_encoded_0}', $url, $deeplink);

        for ($i = 1; $i <= $times[1]; $i++) {
            $url = urlencode($url);
            $deeplink = str_replace('{url_encoded_' . $i . '}', $url, $deeplink);
        }

        return $deeplink;
    }

    private function addParam(string $previousUrl, string $name = null, string $value = null): string
    {
        if ((empty($name)) || (empty($value))) {
            return $previousUrl;
        }

        return Url::fromString($previousUrl)->withQueryParameter($name, $value);
    }

    private function appendRawParam(string $previousUrl, string $name = null, string $value = null): string
    {
        if ((empty($name)) || (empty($value))) {
            return $previousUrl;
        }

        $url = $previousUrl . '&';

        if (!strpos($url, '?')) {
            $url = substr($url, 0, -1) . '?';
        }

        return $url . $name . '=' . $value;
    }

    private function delParam(string $previousUrl, string $value = null): string
    {
        if (empty($value)) {
            return $previousUrl;
        }

        return Url::fromString($previousUrl)->withoutQueryParameter($value);
    }

    private function lTrim(string $previousUrl, string $value = null): string
    {
        $url = $previousUrl;

        if ((!empty($value)) && (strpos($url, $value) === 0)) {
            $url = substr($url, strlen($value));
        }

        return $url;
    }

    private function trim2q(string $previousUrl): string
    {
        $url = [
            implode('/', Url::fromString($previousUrl)->getSegments()),
            (Url::fromString($previousUrl)->getQuery()) ? ('?' . Url::fromString($previousUrl)->getQuery()) : '',
            (Url::fromString($previousUrl)->getFragment()) ? ('#' . Url::fromString($previousUrl)->getFragment()) : ''
        ];

        return implode($url);
    }

    private function regexp(string $previousUrl, string $search = null, string $replace = null): string
    {
        if ((empty($search)) || (empty($replace))) {
            return $previousUrl;
        }

        return preg_replace($search, $replace, $previousUrl);
    }

    private function encodeParamValue(string $previousUrl, string $name = null): string
    {
        if (empty($name)) {
            return $previousUrl;
        }

        $value = Url::fromString($previousUrl)->getQueryParameter($name);
        if (!is_null($value)) {
            $value = urlencode($value);

            return Url::fromString($previousUrl)->withQueryParameter($name, $value);
        }

        return $previousUrl;
    }

    private function lomadee(string $previousUrl, int $affiliateCode): string
    {
        $url = $previousUrl;

        return sprintf('https://redir.lomadee.com/v2/deeplink?url=%s&sourceId=%s', urlencode($url), $affiliateCode);
    }
}
