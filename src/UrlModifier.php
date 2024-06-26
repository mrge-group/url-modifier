<?php

namespace digidip\UrlModifier;

use digidip\UrlModifier\Entities\ProgramParams;
use digidip\UrlModifier\Entities\Rule;
use Spatie\Url\Url;

class UrlModifier
{
    private const AFFCODE = '{affcode}';

    public function getLink(ProgramParams $params): string
    {
        $url = $params->getUrl();
        $deeplink = $params->getDeeplink();

        $modKeys = [
            '{cpi}'          => $params->getCpi(),
            self::AFFCODE    => $params->getAffcode(),
            '{click_key}'    => $params->getClickKey(),
            '{project_id}'   => $params->getProjectId(),
            '{project_name}' => $params->getProjectName()
        ];

        $mods = $params->getMods();
        usort($mods, function (Rule $firstRule, Rule $secondRule) {
            if ($firstRule->getSort() < $secondRule->getSort()) {
                return -1;
            }

            return ($firstRule->getSort() > $secondRule->getSort()) ? 1 : 0;
        });

        foreach ($mods as $rule) {
            $rule->setModKeys($modKeys);
            $url = $this->applyRule($rule, $url);
        }

        $deeplink = str_replace(array_keys($modKeys), $modKeys, $deeplink);

        return $this->encodeUrl($url, $deeplink);
    }

    private function applyRule(Rule $rule, string $previousUrl): string
    {
        $url = $previousUrl;

        if (empty($rule->getValue1())) {
            return $this->applySimpleRule($previousUrl, $rule);
        }

        $rule->setValue1(str_replace(array_keys($rule->getModKeys()), $rule->getModKeys(), $rule->getValue1()));

        if (!empty($rule->getValue2())) {
            $rule->setValue2(str_replace(array_keys($rule->getModKeys()), $rule->getModKeys(), $rule->getValue2()));
        }

        return match ($rule->getType()) {
            Rule::ADD_PARAM_TYPE => $this->addParam($url, $rule->getValue1(), $rule->getValue2()),
            Rule::RAW_PARAM_TYPE => $this->appendRawParam($url, $rule->getValue1(), $rule->getValue2()),
            Rule::DEL_PARAM_TYPE => $this->delParam($url, $rule->getValue1()),
            Rule::LTRIM_TYPE => $this->lTrim($url, $rule->getValue1()),
            Rule::REGEXP_TYPE => $this->regexp($url, $rule->getValue1(), $rule->getValue2()),
            Rule::URL_EMBED_TYPE => $this->encodeUrl($url, $rule->getValue1()),
            default => $this->encodeParamValue($url, $rule->getValue1()),
        };
    }

    private function applySimpleRule(string $previousUrl, Rule $rule): string
    {
        $url = $previousUrl;

        return match ($rule->getType()) {
            Rule::TRIM2Q_TYPE => $this->trim2q($url),
            Rule::BASE64_TYPE => base64_encode($url),
            Rule::LOMADEE_TYPE => $this->lomadee($url, strval($rule->getModKeys()[self::AFFCODE])),
            default => $url,
        };
    }

    private function encodeUrl(string $url, ?string $previousDeeplink): string
    {
        if (empty($previousDeeplink)) {
            return $url;
        }

        $deeplink = $previousDeeplink;
        $patternFlexEncoding = '/{url_encoded_(\d+)}/';

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

    private function addParam(string $previousUrl, ?string $name, ?string $value): string
    {
        if ((empty($name)) || (empty($value))) {
            return $previousUrl;
        }

        return Url::fromString($previousUrl)->withQueryParameter($name, $value);
    }

    private function appendRawParam(string $previousUrl, ?string $name, ?string $value): string
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

    private function delParam(string $previousUrl, ?string $value): string
    {
        if (empty($value)) {
            return $previousUrl;
        }

        return Url::fromString($previousUrl)->withoutQueryParameter($value);
    }

    private function lTrim(string $previousUrl, ?string $value): string
    {
        $url = $previousUrl;

        if ((!empty($value)) && (str_starts_with($url, $value))) {
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

    private function regexp(string $previousUrl, ?string $search, ?string $replace): string
    {
        if ((empty($search)) || (empty($replace))) {
            return $previousUrl;
        }

        $url = preg_replace($search, $replace, $previousUrl);

        if (empty($url)) {
            $url = '';
        }

        return $url;
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

    private function lomadee(string $previousUrl, string $affiliateCode): string
    {
        $url = $previousUrl;

        return sprintf('https://redir.lomadee.com/v2/deeplink?url=%s&sourceId=%s', urlencode($url), $affiliateCode);
    }
}
