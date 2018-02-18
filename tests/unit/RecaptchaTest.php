<?php

namespace tests\unit;

use alexeevdv\recaptcha\Recaptcha;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class RecaptchaTest
 * @package tests\unit
 */
class RecaptchaTest extends \Codeception\Test\Unit
{
    /**
     * @var \tests\UnitTester
     */
    public $tester;

    /**
     * @inheritdoc
     */
    protected function _before()
    {
        Yii::$container->set('i18n', [
            'class' => \yii\i18n\I18N::class,
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en-US',
                    'basePath' => '@yii/messages',
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function _after()
    {
        Yii::$container->clear('i18n');
        Yii::$container->clear('recaptcha');
    }

    /**
     * @return array
     */
    public function allowedThemesProvider()
    {
        return [
            [null],
            ['dark'],
            ['light'],
        ];
    }

    /**
     * @return array
     */
    public function notAllowedThemesProvider()
    {
        return [
            [false],
            ['blue'],
            [123],
            ['default'],
        ];
    }

    /**
     * @dataProvider allowedThemesProvider
     * @param mixed $theme
     */
    public function testAllowedThemes($theme)
    {
        new Recaptcha(['theme' => $theme]);
    }

    /**
     * @dataProvider notAllowedThemesProvider
     * @param mixed $theme
     */
    public function testNotAllowedThemes($theme)
    {
        $this->tester->expectException(InvalidConfigException::class, function () use ($theme) {
            new Recaptcha([
                'theme' => $theme,
            ]);
        });
    }

    /**
     * @return array
     */
    public function allowedTypesProvider()
    {
        return [
            [null],
            ['image'],
            ['audio'],
        ];
    }

    /**
     * @return array
     */
    public function notAllowedTypesProvider()
    {
        return [
            [false],
            ['code'],
            ['video'],
            [123],
        ];
    }

    /**
     * @dataProvider allowedTypesProvider
     * @param mixed $type
     */
    public function testAllowedTypes($type)
    {
        new Recaptcha(['type' => $type]);
    }

    /**
     * @dataProvider notAllowedTypesProvider
     * @param mixed $type
     */
    public function testNotAllowedTypes($type)
    {
        $this->tester->expectException(InvalidConfigException::class, function () use ($type) {
            new Recaptcha([
                'type' => $type,
            ]);
        });
    }

    /**
     * @return array
     */
    public function allowedSizesProvider()
    {
        return [
            [null],
            ['compact'],
            ['normal'],
        ];
    }

    /**
     * @return array
     */
    public function notAllowedSizesProvider()
    {
        return [
            [false],
            ['hurrdurr'],
            [123],
            ['xxxl'],
        ];
    }


    /**
     * @dataProvider allowedSizesProvider
     * @param mixed $size
     */
    public function testAllowedSizes($size)
    {
        new Recaptcha(['size' => $size]);
    }

    /**
     * @dataProvider notAllowedSizesProvider
     * @param mixed $size
     */
    public function testNotAllowedSizes($size)
    {
        $this->tester->expectException(InvalidConfigException::class, function () use ($size) {
            new Recaptcha(['size' => $size]);
        });
    }
}
