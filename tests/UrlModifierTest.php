<?php

namespace digidip\UrlModifier\Tests;

use digidip\UrlModifier\Entities\ProgramParams;
use digidip\UrlModifier\Entities\Rule;
use digidip\UrlModifier\UrlModifier;
use PHPUnit\Framework\TestCase;

class UrlModifierTest extends TestCase
{
    const TEST_URL = 'https://testnetwork.com/';

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
        $rawParams = [
            new Rule(1, Rule::RAW_PARAM_TYPE, 'utm_source', 'awin'),
            new Rule(2, Rule::RAW_PARAM_TYPE, 'utm_medium', 'afiliado'),
            new Rule(3, Rule::RAW_PARAM_TYPE, 'IdParceiro', '00451012')
        ];
        $delParamsMods = [
            new Rule(1, Rule::DEL_PARAM_TYPE, 'vip'),
            new Rule(2, Rule::DEL_PARAM_TYPE, 'lkid'),
            new Rule(3, Rule::REGEXP_TYPE, '/wid_[a-zA-Z0-9_-]+_wid=[a-zA-Z0-9_-]+/', '1=1')
        ];
        $addParamsMods = [
            new Rule(4, Rule::ADD_PARAM_TYPE, 'Partner_ID', '!!!awc!!'),
            new Rule(2, Rule::ADD_PARAM_TYPE, 'utm_medium', 'afiliacja'),
            new Rule(3, Rule::ADD_PARAM_TYPE, 'utm_content', '253367997'),
            new Rule(1, Rule::ADD_PARAM_TYPE, 'utm_source', 'salestube.pl')
        ];
        $regexpEmbedMods = [
            new Rule(4, Rule::REGEXP_TYPE, '/%252F/', '/'),
            new Rule(1, Rule::ADD_PARAM_TYPE, 'utm_source', 'they.pl'),
            new Rule(2, Rule::ADD_PARAM_TYPE, 'utm_medium', 'afiliacja'),
            new Rule(3, Rule::ADD_PARAM_TYPE, 'utm_campaign', '21%2F114'),
            new Rule(5, Rule::URL_EMBED_TYPE, 'http://go.they.pl/aff_c?offer_id=21&aff_id=114&url={url_encoded_0}')
        ];
        $lomadee = [new Rule(1, Rule::LOMADEE_TYPE)];
        $trim2QMods = [new Rule(1, Rule::TRIM2Q_TYPE)];
        $base64encodedMods = [new Rule(1, Rule::BASE64_TYPE)];
        $ltrimMods = [new Rule(1, Rule::LTRIM_TYPE, 'https://'), new Rule(2, Rule::LTRIM_TYPE, 'http://')];

        return [
            [
                $this->getParams('cread.php?awid=31&affid={cpi}&ckref={click_key}&p=%5B%5B{url_encoded_2}%5D%5D'),
                self::TEST_URL . 'cread.php?awid=31&affid=5798314733&ckref=XXX0031&p=%5B%5Bhttps%253A%252F%252Fwww.test'
                . '.com%252F%5D%5D'
            ],
            [
                $this->getParams('click/camref:1100lqou/pubref:{click_key}/destination:{url_encoded_1}'),
                self::TEST_URL . 'click/camref:1100lqou/pubref:XXX0031/destination:https%3A%2F%2Fwww.test.com%2F'
            ],
            [
                $this->getParams('click.html?campid={affcode}&wgproid=98&ckref={click_key}&target={url_encoded_0}'),
                self::TEST_URL . 'click.html?campid=5798314733&wgproid=98&ckref=XXX0031&target=https://www.test.com/'
            ],
            [
                $this->getParams('cread.php?awid=57&affid={cpi}&ckref={click_key}&p=%5B%5B{url_encoded_2}%5D%5D',
                    $addParamsMods),
                self::TEST_URL . 'cread.php?awid=57&affid=5798314733&ckref=XXX0031&p=%5B%5Bhttps%253A%252F%252Fwww.test'
                . '.com%253Futm_source%253Dsalestube.pl%2526utm_medium%253Dafiliacja%2526utm_content%253D253367997%2526'
                . 'Partner_ID%253D%2521%2521%2521awc%2521%2521%5D%5D'
            ],
            [
                $this->getParams('?c=31&m=98&a={affcode}&r={click_key}&u={url_encoded_1}', $delParamsMods,
                    'pp_009432162459.html?wid=1433363'),
                self::TEST_URL . '?c=31&m=98&a=5798314733&r=XXX0031&u=https%3A%2F%2Fwww.test.com%2F'
                . 'pp_009432162459.html%3Fwid%3D1433363'
            ],
            [
                $this->getParams('ppc/?27083699C21436051&zpar0=[[{click_key}]]&ULP=[[{url_encoded_1}]]', $rawParams),
                self::TEST_URL . 'ppc/?27083699C21436051&zpar0=[[XXX0031]]&ULP=[[https%3A%2F%2Fwww.test.com%2F%3'
                . 'Futm_source%3Dawin%26utm_medium%3Dafiliado%26IdParceiro%3D00451012]]'
            ],
            [
                $this->getParams('r.cfm?u={affcode}&b=31&m=98&track={click_key}&urllink={url_encoded_1}', $ltrimMods),
                self::TEST_URL . 'r.cfm?u=5798314733&b=31&m=98&track=XXX0031&urllink=www.test.com%2F'
            ],
            [
                $this->getParams('c/?si=31&li=98&wi={affcode}&ws={click_key}&dl={url_encoded_1}', $trim2QMods,
                    'thema/veluwe?size=20&q=veluwe&sf=popular&sd=desc&filter:theme=veluwe&filter:hcity=or%2CNunspeet'),
                self::TEST_URL . 'c/?si=31&li=98&wi=5798314733&ws=XXX0031&dl=thema%2Fveluwe%3Fsize%3D20%26q%3Dveluwe%26'
                . 'sf%3Dpopular%26sd%3Ddesc%26filter%3Atheme%3Dveluwe%26filter%3Ahcity%3Dor%252CNunspeet'
            ],
            [
                $this->getParams('cread.php?amid=31&affid={cpi}&ckref={click_key}&p={url_encoded_1}', $regexpEmbedMods),
                self::TEST_URL . 'cread.php?amid=31&affid=5798314733&ckref=XXX0031&p=http%3A%2F%2Fgo.they.pl%2Faff_c%3'
                . 'Foffer_id%3D21%26aff_id%3D114%26url%3Dhttps%3A%2F%2Fwww.test.com%3Futm_source%3Dthey.pl%26utm_medium'
                . '%3Dafiliacja%26utm_campaign%3D21%252F114'
            ],
            [
                $this->getParams('cread.php?amid=10076&affid={cpi}&ckref={click_key}&p=http%3A%2F%2Fa.nonstoppartner.'
                                 . 'net%2Fa%2F%3Fi%3Dclick%26client%3Ddouglas%26camp%3Dwmgdeep%26nw%3Dfiw1%26l%3Dde%26'
                                 . 'uri%3D{url_encoded_0}', $base64encodedMods),
                self::TEST_URL . 'cread.php?amid=10076&affid=5798314733&ckref=XXX0031&p=http%3A%2F%2Fa.nonstoppartner.'
                . 'net%2Fa%2F%3Fi%3Dclick%26client%3Ddouglas%26camp%3Dwmgdeep%26nw%3Dfiw1%26l%3Dde%26uri%3'
                . 'DaHR0cHM6Ly93d3cudGVzdC5jb20v'
            ],
            [
                $this->getParams('{url_encoded_0}', $lomadee),
                self::TEST_URL . 'https://redir.lomadee.com/v2/deeplink?url=https%3A%2F%2Fwww.test.com%2F&sourceId'
                . '=5798314733'
            ]
        ];
    }

    private function getParams(string $deeplink, array $mods = null, string $urlExtension = ''): ProgramParams
    {
        $params = new ProgramParams('https://www.test.com/' . $urlExtension, self::TEST_URL . $deeplink);

        if (!empty($mods)) {
            $params->setMods($mods);
        }

        return $params;
    }
}
