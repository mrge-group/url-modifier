<?php

namespace digidip\UrlModifier\Tests;

use digidip\UrlModifier\Entities\ProgramParams;
use digidip\UrlModifier\Entities\Rule;
use digidip\UrlModifier\UrlModifier;
use PHPUnit\Framework\TestCase;

class UrlModifierTest extends TestCase
{
    /**
     * @dataProvider providerParams
     */
    public function testGetLink(ProgramParams $params, string $expectedUrl)
    {
        $ruleApplier = new UrlModifier();

        $affcode = '5798314733';
        $params->setCpi($affcode);
        $params->setAffcode($affcode);
        $params->setClickKey('XXX0031');

        $previewLink = $ruleApplier->getLink($params);

        self::assertSame($expectedUrl, $previewLink);
    }

    public function providerParams(): array
    {
        $testUrl = 'https://www.test.com/';
        $testNetworkUrl = 'https://testnetwork.com/';

        $noRulesDbEncParams = new ProgramParams($testUrl,
            $testNetworkUrl . 'cread.php?awid=31&affid={cpi}&ckref={click_key}&p=%5B%5B{url_encoded_2}%5D%5D');
        $noRulesSingEncParams = new ProgramParams($testUrl,
            $testNetworkUrl . 'click/camref:1100lqou/pubref:{click_key}/destination:{url_encoded_1}');
        $noRulesNoEncParams = new ProgramParams($testUrl,
            $testNetworkUrl . 'click.html?campid={affcode}&wgproid=98&ckref={click_key}&target={url_encoded_0}');

        $addParams = new ProgramParams($testUrl,
            $testNetworkUrl . 'cread.php?awid=57&affid={cpi}&ckref={click_key}&p=%5B%5B{url_encoded_2}%5D%5D');
        $mods = [
            new Rule(2, Rule::ADD_PARAM_TYPE, 'utm_medium', 'afiliacja'),
            new Rule(1, Rule::ADD_PARAM_TYPE, 'utm_source', 'salestube.pl'),
            new Rule(3, Rule::ADD_PARAM_TYPE, 'utm_content', '253367997'),
            new Rule(4, Rule::ADD_PARAM_TYPE, 'Partner_ID', '!!!awc!!')
        ];
        $addParams->setMods($mods);

        $delParams = new ProgramParams($testUrl . 'pp_009432162459.html?wid=1433363',
            $testNetworkUrl . '?c=31&m=98&a={affcode}&r={click_key}&u={url_encoded_1}');
        $mods = [
            new Rule(1, Rule::DEL_PARAM_TYPE, 'vip'),
            new Rule(2, Rule::DEL_PARAM_TYPE, 'lkid'),
            new Rule(3, Rule::REGEXP_TYPE, '/wid_[a-zA-Z0-9_-]+_wid=[a-zA-Z0-9_-]+/', '1=1')
        ];
        $delParams->setMods($mods);

        return [
            [
                $noRulesDbEncParams,
                $testNetworkUrl . 'cread.php?awid=31&affid=5798314733&ckref=XXX0031&p=%5B%5Bhttps%253A%252F%252Fwww.test.com%252F%5D%5D'
            ],
            [
                $noRulesSingEncParams,
                $testNetworkUrl . 'click/camref:1100lqou/pubref:XXX0031/destination:https%3A%2F%2Fwww.test.com%2F'
            ],
            [
                $noRulesNoEncParams,
                $testNetworkUrl . 'click.html?campid=5798314733&wgproid=98&ckref=XXX0031&target=https://www.test.com/'
            ],
            [
                $addParams,
                $testNetworkUrl . 'cread.php?awid=57&affid=5798314733&ckref=XXX0031&p=%5B%5Bhttps%253A%252F%252Fwww.test.com%253Futm_source%253Dsalestube.pl%2526utm_medium%253Dafiliacja%2526utm_content%253D253367997%2526Partner_ID%253D%2521%2521%2521awc%2521%2521%5D%5D'
            ],
            [
                $delParams,
                $testNetworkUrl . '?c=31&m=98&a=5798314733&r=XXX0031&u=https%3A%2F%2Fwww.test.com%2Fpp_009432162459.html%3Fwid%3D1433363'
            ]
        ];
    }

    /**
     * This test checks the results and the execution time of a request for getting an url with rules.
     */
    public function testGetPreviewLinkWithAddRawParam()
    {
        $params = new ProgramParams();

        $url = "https://www.christ.de/";
        $rules = [
            RulesApplier::TYPE => Rule::RAW_PARAM_TYPE,
            RulesApplier::VALUE1 => "subid",
            RulesApplier::VALUE2 => "Startseite"
        ];
        $deeplink = "https://ad.zanox.com/ppc/?39949644C1430615686&zpar0=[[{click_key}]]&ULP=[[{url_encoded_0}]]";

        $params->setClickKey("dsfadgga2412");
        $params->setAffcode("fadva324");
        $params->setCpi("vdsv34");

        $previewLink = RulesApplier::getLink($url, $deeplink, $params, [$rules]);

        self::assertNotEmpty($previewLink);

        echo print_r($previewLink, true);
    }

    /**
     * This test checks the results and the execution time of a request for getting an url with rules.
     */
    public function testGetPreviewLinkWithDelParam()
    {
        $params = new ProgramParams();

        $url = "https://ru.gearbest.com/";
        $rules = [
            RulesApplier::TYPE => Rule::DEL_PARAM_TYPE,
            RulesApplier::VALUE1 => "vip"
        ];
        $deeplink = "http://tc.tradetracker.net/?c=20536&m=12&a={affcode}&r={click_key}&u={url_encoded_1}";

        $params->setClickKey("dsfadgga2412");
        $params->setAffcode("fadva324");
        $params->setCpi("vdsv34");

        $previewLink = RulesApplier::getLink($url, $deeplink, $params, [$rules]);

        self::assertNotEmpty($previewLink);

        echo print_r($previewLink, true);
    }

    /**
     * This test checks the results and the execution time of a request for getting an url with rules.
     */
    public function testGetPreviewLinkWithLTrim()
    {
        $params = new ProgramParams();

        $url = "http://www.plutosport.nl/";
        $rules = [
            RulesApplier::TYPE => Rule::LTRIM_TYPE,
            RulesApplier::VALUE1 => "http://www.plutosport.nl"
        ];
        $deeplink = "https://ad.zanox.com/ppc/?35032410C49785802&zpar0=[[{click_key}]]&ULP=[[{url_encoded_1}]]";

        $params->setClickKey("dsfadgga2412");
        $params->setAffcode("fadva324");
        $params->setCpi("vdsv34");

        $previewLink = RulesApplier::getLink($url, $deeplink, $params, [$rules]);

        self::assertNotEmpty($previewLink);

        echo print_r($previewLink, true);
    }

    /**
     * This test checks the results and the execution time of a request for getting an url with rules.
     */
    public function testGetPreviewLinkWithTrim2Q()
    {
        $params = new ProgramParams();

        $url = "https://www.huishoudplein.nl/";
        $rules = [RulesApplier::TYPE => Rule::TRIM2Q_TYPE];
        $deeplink = "https://ds1.nl/c/?si=9592&li=1431408&wi={affcode}&ws={click_key}&dl={url_encoded_1}";

        $params->setClickKey("dsfadgga2412");
        $params->setAffcode("fadva324");
        $params->setCpi("vdsv34");

        $previewLink = RulesApplier::getLink($url, $deeplink, $params, [$rules]);

        self::assertNotEmpty($previewLink);

        echo print_r($previewLink, true);
    }

    /**
     * This test checks the results and the execution time of a request for getting an url with rules.
     */
    public function testGetPreviewLinkWithRegexp()
    {
        $params = new ProgramParams();

        $url = "http://www.dx.com/nl/";
        $rules = [
            RulesApplier::TYPE => Rule::REGEXP_TYPE,
            RulesApplier::VALUE1 => "/#/",
            RulesApplier::VALUE2 => "%23"
        ];
        $deeplink = "http://ad.zanox.com/ppc/?31399001C450197702&zpar0=[[{click_key}]]&ULP=[[{url_encoded_1}]]";

        $params->setClickKey("dsfadgga2412");
        $params->setAffcode("fadva324");
        $params->setCpi("vdsv34");

        $previewLink = RulesApplier::getLink($url, $deeplink, $params, [$rules]);

        self::assertNotEmpty($previewLink);

        echo print_r($previewLink, true);
    }

    /**
     * This test checks the results and the execution time of a request for getting an url with rules.
     */
    public function testGetPreviewLinkWithGeenapp()
    {
        $params = new ProgramParams();

        $url = "https://play.google.com/store";
        $rules = [
            RulesApplier::TYPE => Rule::GEENAPP_TYPE,
            RulesApplier::VALUE1 => "{affcode}"
        ];
        $deeplink = "{url_encoded_0}";

        $params->setClickKey("dsfadgga2412");
        $params->setAffcode("fadva324");
        $params->setCpi("vdsv34");

        $previewLink = RulesApplier::getLink($url, $deeplink, $params, [$rules]);

        self::assertNotEmpty($previewLink);

        echo print_r($previewLink, true);
    }

    /**
     * This test checks the results and the execution time of a request for getting an url with rules.
     */
    public function testGetPreviewLinkWithEmbed()
    {
        $params = new ProgramParams();
        $rule = new RuleBO();

        $url = "https://www.christ.de/";
        $deeplink = "https://ad.zanox.com/ppc/?39949644C1430615686&zpar0=[[{click_key}]]&ULP=[[{url_encoded_0}]]";

        $params->setClickKey("dsfadgga2412");
        $params->setAffcode("fadva324");
        $params->setCpi("vdsv34");

        $rule->setSort(1);
        $rule->setType(Rule::URL_EMBED_TYPE);
        $rule->setValue1("http://t23.intelliad.de/index.php?redirect={url_encoded_2}&cl=4393034313236323131303&bm=100&bmcl=9353835313236323131303&cp=101&ag=241&crid=101&subid=Startseite");

        $previewLink = RulesApplier::getLink($url, $deeplink, $params, [$rule]);

        self::assertNotEmpty($previewLink);

        echo print_r($previewLink, true);
    }

    /**
     * This test checks the results and the execution time of a request for getting an url with rules.
     */
    public function testGetPreviewLinkWith64Encode()
    {
        $params = new ProgramParams();

        $url = "http://www.douglas.de/";
        $rules = [RulesApplier::TYPE => Rule::BASE64_TYPE];
        $deeplink = "http://ad.zanox.com/ppc/?32039959C1434548476T&zpar0=[[{click_key}]]&ULP=[[{url_encoded_1}]]";

        $params->setClickKey("dsfadgga2412");
        $params->setAffcode("fadva324");
        $params->setCpi("vdsv34");

        $previewLink = RulesApplier::getLink($url, $deeplink, $params, [$rules]);

        self::assertNotEmpty($previewLink);

        echo print_r($previewLink, true);
    }
}
