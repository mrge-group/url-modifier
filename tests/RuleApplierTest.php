<?php

namespace digidip\RuleApplier\Tests;

use PHPUnit\Framework\TestCase;

class RuleApplierTest extends TestCase
{
    /**
     * This test checks the results and the execution time of a request for getting an url without rules.
     */
    public function testGetPreviewLinkWithoutRules()
    {
        $params = new ProgramParams();

        $url = "http://argos.co.uk";
        $deeplink = "http://www.anrdoezrs.net/links/1546795/type/dlg/sid/{click_key}/{url_encoded_3}";

        $params->setClickKey("dsfadgga2412");
        $params->setAffcode("fadva324");
        $params->setCpi("vdsv34");

        $previewLink = RulesApplier::getLink($url, $deeplink, $params);

        self::assertNotEmpty($previewLink);

        echo print_r($previewLink, true);
    }

    /**
     * This test checks the results and the execution time of a request for getting an url with rules.
     */
    public function testGetPreviewLinkWithAddParamRule()
    {
        $params = new ProgramParams();

        $url = "https://www.etna.com.br/";
        $rules = [
            RulesApplier::TYPE => Rule::ADD_PARAM_TYPE,
            RulesApplier::VALUE1 => "utm_source",
            RulesApplier::VALUE2 => "zanox"
        ];
        $deeplink = "https://ad.zanox.com/ppc/?37154522C19266018&zpar0=[[{click_key}]]&ULP=[[{url_encoded_1}]]";

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
