<?php

namespace digidip\RulesApplier;

use digidip\RuleApplier\Entities\ProgramParams;
use digidip\RuleApplier\Entities\Rule;
use Purl\Url;

class RuleApplier
{
    const CPI = '{cpi}';
    const URL = '{url}';
    const RAW_URL = '{raw_url}';
    const AFFCODE = '{affcode}';
    const CLICK_KEY = '{click_key}';
    const URL_ENCODED_0 = '{url_encoded_0}';
    const URL_ENCODED_1 = '{url_encoded_1}';

    public function getLink(ProgramParams $params): string
    {
        $url = $params->getUrl();
        $deeplink = $params->getDeeplink();

        $modKeys = [
            self::CPI => $params->getCpi(),
            self::AFFCODE => $params->getAffcode(),
            self::CLICK_KEY => $params->getClickKey()
        ];

        $rules = $params->getMods()->sortBy(function ($rule) {
            /** @var Rule $rule */
            return $rule->getSort();
        });

        /** @var Rule $rule */
        foreach ($rules as $rule) {
            $rule->setModKeys($modKeys);
            $url = $this->applyRule($url, $rule);
        }

        $modKeys += [self::URL => self::URL_ENCODED_1, self::RAW_URL => self::URL_ENCODED_0];
        $deeplink = str_replace(array_keys($modKeys), $modKeys, $deeplink);

        $deeplink = $this->encodeUrl($deeplink, $url);

        return $deeplink;
    }

    private function applyRule(string $previousUrl, Rule $rule): string
    {
        $url = $previousUrl;

        if (!empty($rule->getValue1())) {
            $rule->setValue1(str_replace(array_keys($rule->getModKeys()), $rule->getModKeys(), $rule->getValue1()));
        }
        if (!empty($rule->getValue2())) {
            $rule->setValue2(str_replace(array_keys($rule->getModKeys()), $rule->getModKeys(), $rule->getValue2()));
        }

        switch ($rule->getType()) {
            case Rule::ADD_PARAM_TYPE:
                return $this->addParam($url, $rule->getValue1(), $rule->getValue2());
            case Rule::RAW_PARAM_TYPE:
                return $this->appendRawParam($url, $rule->getValue1(), $rule->getValue2());
            case Rule::DEL_PARAM_TYPE:
                return $this->delParam($url, $rule->getValue1());
            case Rule::LTRIM_TYPE:
                return $this->lTrim($url, $rule->getValue1());
            case Rule::TRIM2Q_TYPE:
                return $this->trim2q($url);
            case Rule::REGEXP_TYPE:
                return $this->regexp($url, $rule->getValue1(), $rule->getValue2());
            case Rule::GEENAPP_TYPE:
                return $this->geenapp($url, $rule->getValue1());
            case Rule::URL_EMBED_TYPE:

                $value1 = $rule->getValue1();
                $value1 = $this->encodeUrl($value1, $url);
                $rule->setValue1($value1);

                return $rule->getValue1();
            case Rule::BASE64_TYPE:
                return base64_encode($url);
            case Rule::ENCODE_PARAM_TYPE:
                return $this->encodeParamValue($url, $rule->getValue1());
            case Rule::LOMADEE_TYPE:
                return $this->lomadee($url, $rule->getModKeys()[self::AFFCODE]);
            default:
                return $url;
        }
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

        $url = Url::parse($previousUrl);
        $url->query->set($name, $value);
        $url = $url->getUrl();

        return $url;
    }

    private function appendRawParam(string $previousUrl, string $name = null, string $value = null): string
    {
        if ((empty($name)) || (empty($value))) {
            return $previousUrl;
        }

        $url = $previousUrl . '&';

        if (!strpos($url, '?')) {
            $url .= substr($url, 0, -1) . '?';
        }

        $url .= $name . '=' . $value;

        return $url;
    }

    private function delParam(string $previousUrl, string $value = null): string
    {
        if (empty($value)) {
            return $previousUrl;
        }

        $url = Url::parse($previousUrl);
        $url->query->remove($value);
        $url = $url->getUrl();

        return $url;
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
        $url = Url::parse($previousUrl);
        $url = [
            (string)$url->path->getPath(),
            ($url->query->getQuery()) ? ('?' . $url->query->getQuery()) : '',
            ($url->fragment->getFragment()) ? ('#' . $url->fragment->getFragment()) : '',
        ];

        return ltrim(implode($url), '/');
    }

    private function regexp(string $previousUrl, string $search = null, string $replace = null): string
    {
        if ((empty($search)) || (empty($replace))) {
            return $previousUrl;
        }

        return preg_replace($search, $replace, $previousUrl);
    }

    /**
     * Converts a URL with the geenapp myUrl API
     * See https://publisher.geenapp.com/myurl.php
     */
    private function geenapp(string $previousUrl, string $projectId = null): string
    {
        if (empty($projectId)) {
            return $previousUrl;
        }

        $url = trim($previousUrl);

        $urlApiCall = 'http://myurl.geenapptool.com/?' . http_build_query(['p' => $projectId, 'url' => $url]);

        $content = trim(file_get_contents($urlApiCall));

        if (empty($content)) {
            return $url;
        }

        $filteredUrl = filter_var($content, FILTER_VALIDATE_URL);
        if (strcasecmp($content, $filteredUrl) == 0) {
            $url = $content;
        }

        return $url;
    }

    private function encodeParamValue(string $previousUrl, string $name = null): string
    {
        if (empty($name)) {
            return $previousUrl;
        }

        $url = Url::parse($previousUrl);
        $value = $url->query->get($name);

        if (!is_null($value)) {
            $value = urlencode($value);
            $url->query->set($name, $value);
        }

        $url = $url->getUrl();

        return $url;
    }

    private function lomadee(string $previousUrl, int $affiliateCode): string
    {
        $url = $previousUrl;

        $apiToken = '6249536e6a4b55694763773d';

        $urlApiCall = 'http://bws.buscape.com.br/service/createLinks/lomadee/' . $apiToken . '/BR/?'
            . http_build_query(['sourceId' => $affiliateCode, 'format' => 'json', 'link1' => $url]);

        $rawResponse = file_get_contents($urlApiCall);
        $jsonResponse = json_decode($rawResponse);

        if ((empty($jsonResponse)) || (!isset($jsonResponse->lomadeelinks[0]->lomadeelink->redirectlink))) {
            return $url;
        }

        if (!empty($jsonResponse->lomadeelinks[0]->lomadeelink->redirectlink)) {
            return $jsonResponse->lomadeelinks[0]->lomadeelink->redirectlink;
        }

        return $url;
    }
}
