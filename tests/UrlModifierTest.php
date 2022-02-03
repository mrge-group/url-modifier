<?php

namespace digidip\UrlModifier\Tests;

use digidip\UrlModifier\Entities\ProgramParams;
use digidip\UrlModifier\Entities\Rule;
use digidip\UrlModifier\UrlModifier;
use PHPUnit\Framework\TestCase;

class UrlModifierTest extends TestCase
{
    private const TEST_URL = 'https://testnetwork.com/';

    /**
     * @dataProvider providerParams
     */
    public function testGetLink(string $deeplink, string $expectedUrl, array $mods = [], string $urlExtension = '')
    {
        $rules = [];
        foreach ($mods as $rule) {
            $rules[] = new Rule($rule[0], $rule[1], $rule[2], $rule[3]);
        }

        $ruleApplier = new UrlModifier();
        $params = $this->getParams($deeplink, $rules, $urlExtension);

        $previewLink = $ruleApplier->getLink($params);

        self::assertSame($expectedUrl, $previewLink);
    }

    public function providerParams(): array
    {
        $rawParams = [
            [1, Rule::RAW_PARAM_TYPE, 'utm_source', 'awin'],
            [2, Rule::RAW_PARAM_TYPE, 'utm_medium', 'afiliado'],
            [3, Rule::RAW_PARAM_TYPE, 'IdParceiro', '00451012']
        ];
        $delParamsMods = [
            [1, Rule::DEL_PARAM_TYPE, 'vip', null],
            [2, Rule::DEL_PARAM_TYPE, 'lkid', null],
            [3, Rule::REGEXP_TYPE, '/wid_[a-zA-Z0-9_-]+_wid=[a-zA-Z0-9_-]+/', '1=1', null]
        ];
        $addParamsMods = [
            [4, Rule::ADD_PARAM_TYPE, 'Partner_ID', '!!!awc!!'],
            [2, Rule::ADD_PARAM_TYPE, 'utm_medium', 'afiliacja'],
            [3, Rule::ADD_PARAM_TYPE, 'utm_content', '253367997'],
            [1, Rule::ADD_PARAM_TYPE, 'utm_source', 'salestube.pl']
        ];
        $regexpEmbedMods = [
            [4, Rule::REGEXP_TYPE, '/%252F/', '/'],
            [1, Rule::ADD_PARAM_TYPE, 'utm_source', 'they.pl'],
            [2, Rule::ADD_PARAM_TYPE, 'utm_medium', 'afiliacja'],
            [3, Rule::ADD_PARAM_TYPE, 'utm_campaign', '21%2F114'],
            [5, Rule::URL_EMBED_TYPE, 'http://go.they.pl/aff_c?offer_id=21&aff_id=114&url={url_encoded_0}', null]
        ];
        $lomadee = [[1, Rule::LOMADEE_TYPE, null, null]];
        $trim2QMods = [[1, Rule::TRIM2Q_TYPE, null, null]];
        $base64encodedMods = [[1, Rule::BASE64_TYPE, null, null]];
        $ltrimMods = [[1, Rule::LTRIM_TYPE, 'https://', null], [2, Rule::LTRIM_TYPE, 'http://', null]];

        return [
            [
                'cread.php?awid=31&affid={cpi}&ckref={click_key}&p=%5B%5B{url_encoded_2}%5D%5D',
                self::TEST_URL . 'cread.php?awid=31&affid=5798314733&ckref=XXX0031&p=%5B%5Bhttps%253A%252F%252Fwww.test'
                . '.com%252F%5D%5D'
            ],
            [
                'ck/cmref:1100lqo/pbref:{click_key}/proj:{project_name}/destination:{url_encoded_1}',
                self::TEST_URL . 'ck/cmref:1100lqo/pbref:XXX0031/proj:5798314733/destination:'
                . 'https%3A%2F%2Fwww.test.com%2F'
            ],
            [
                'click.html?campid={affcode}&wgproid=98&ckref={click_key}&target={url_encoded_0}',
                self::TEST_URL . 'click.html?campid=5798314733&wgproid=98&ckref=XXX0031&target=https://www.test.com/'
            ],
            [
                'deeplink?id={affcode}&u1={click_key}&subId={project_id}&murl={url_encoded_1}',
                self::TEST_URL . 'deeplink?id=5798314733&u1=XXX0031&subId=7&murl=https%3A%2F%2Fwww.test.com%2F'
            ],
            [
                'cread.php?awid=57&affid={cpi}&ckref={click_key}&p=%5B%5B{url_encoded_2}%5D%5D',
                self::TEST_URL . 'cread.php?awid=57&affid=5798314733&ckref=XXX0031&p=%5B%5Bhttps%253A%252F%252Fwww.test'
                . '.com%253Futm_source%253Dsalestube.pl%2526utm_medium%253Dafiliacja%2526utm_content%253D253367997%2526'
                . 'Partner_ID%253D%252521%252521%252521awc%252521%252521%5D%5D',
                $addParamsMods,
            ],
            [
                '?c=31&m=98&a={affcode}&r={click_key}&u={url_encoded_1}',
                self::TEST_URL . '?c=31&m=98&a=5798314733&r=XXX0031&u=https%3A%2F%2Fwww.test.com%2F'
                . 'pp_009432162459.html%3Fwid%3D1433363',
                $delParamsMods,
                'pp_009432162459.html?wid=1433363',
            ],
            [
                'ppc/?27083699C21436051&zpar0=[[{click_key}]]&ULP=[[{url_encoded_1}]]',
                self::TEST_URL . 'ppc/?27083699C21436051&zpar0=[[XXX0031]]&ULP=[[https%3A%2F%2Fwww.test.com%2F%3'
                . 'Futm_source%3Dawin%26utm_medium%3Dafiliado%26IdParceiro%3D00451012]]',
                $rawParams,
            ],
            [
                'r.cfm?u={affcode}&b=31&m=98&track={click_key}&urllink={url_encoded_1}',
                self::TEST_URL . 'r.cfm?u=5798314733&b=31&m=98&track=XXX0031&urllink=www.test.com%2F',
                $ltrimMods
            ],
            [
                'c/?si=31&li=98&wi={affcode}&ws={click_key}&dl={url_encoded_1}',
                self::TEST_URL . 'c/?si=31&li=98&wi=5798314733&ws=XXX0031&dl=thema%2Fveluwe%3Fsize%3D20%26q%3Dveluwe%26'
                . 'sf%3Dpopular%26sd%3Ddesc%26filter%3Atheme%3Dveluwe%26filter%3Ahcity%3Dor%252CNunspeet',
                $trim2QMods,
                'thema/veluwe?size=20&q=veluwe&sf=popular&sd=desc&filter:theme=veluwe&filter:hcity=or%2CNunspeet'
            ],
            [
                'cread.php?amid=31&affid={cpi}&ckref={click_key}&p={url_encoded_1}',
                self::TEST_URL . 'cread.php?amid=31&affid=5798314733&ckref=XXX0031&p=http%3A%2F%2Fgo.they.pl%2Faff_c%3'
                . 'Foffer_id%3D21%26aff_id%3D114%26url%3Dhttps%3A%2F%2Fwww.test.com%3Futm_source%3Dthey.pl%26utm_medium'
                . '%3Dafiliacja%26utm_campaign%3D21%2F114',
                $regexpEmbedMods,
            ],
            [
                'cread.php?amid=10076&affid={cpi}&ckref={click_key}&p=http%3A%2F%2Fa.nonstoppartner.'
                . 'net%2Fa%2F%3Fi%3Dclick%26client%3Ddouglas%26camp%3Dwmgdeep%26nw%3Dfiw1%26l%3Dde%26'
                . 'uri%3D{url_encoded_0}',
                self::TEST_URL . 'cread.php?amid=10076&affid=5798314733&ckref=XXX0031&p=http%3A%2F%2Fa.nonstoppartner.'
                . 'net%2Fa%2F%3Fi%3Dclick%26client%3Ddouglas%26camp%3Dwmgdeep%26nw%3Dfiw1%26l%3Dde%26uri%3'
                . 'DaHR0cHM6Ly93d3cudGVzdC5jb20v',
                $base64encodedMods,
            ],
            [
                '{url_encoded_0}',
                self::TEST_URL . 'https://redir.lomadee.com/v2/deeplink?url=https%3A%2F%2Fwww.test.com%2F&sourceId'
                . '=5798314733',
                $lomadee,
            ]
        ];
    }

    private function getParams(string $deeplink, array $mods, string $urlExtension): ProgramParams
    {
        $params = new ProgramParams('https://www.test.com/' . $urlExtension, self::TEST_URL . $deeplink);

        $affcode = '5798314733';
        $params->setProjectId(7);
        $params->setCpi($affcode);
        $params->setAffcode($affcode);
        $params->setClickKey('XXX0031');
        $params->setProjectName($affcode);

        if (!empty($mods)) {
            $params->setMods($mods);
        }

        return $params;
    }
}
