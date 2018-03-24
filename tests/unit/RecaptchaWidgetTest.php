<?php

namespace tests\unit;

use alexeevdv\recaptcha\Recaptcha;
use alexeevdv\recaptcha\RecaptchaWidget;
use Codeception\Stub;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Application;
use yii\web\View;

/**
 * Class RecaptchaWidgetTest
 * @package tests\unit
 */
class RecaptchaWidgetTest extends \Codeception\Test\Unit
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
        Yii::$app = Stub::make(Application::class, [
            'getView' => Stub::make(View::class),
            'language' => 'en-GB',
        ]);
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
        Yii::$container->set('recaptcha', function () {
            return Stub::make(Recaptcha::class);
        });
    }

    /**
     * @inheritdoc
     */
    protected function _after()
    {
        Yii::$app = null;
        Yii::$container->clear('i18n');
        Yii::$container->clear('recaptcha');
    }

    /**
     * @test
     */
    public function testSiteKeyIsRequired()
    {
        $this->tester->expectException(InvalidConfigException::class, function () {
            new RecaptchaWidget(['name' => 'input']);
        });

        new RecaptchaWidget([
            'name' => 'input',
            'siteKey' => 'hurrdurr',
        ]);
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
        new RecaptchaWidget([
            'name' => 'input',
            'siteKey' => 'hurrdurr',
            'size' => $size,
        ]);
    }

    /**
     * @dataProvider notAllowedSizesProvider
     * @param mixed $size
     */
    public function testNotAllowedSizes($size)
    {
        $this->tester->expectException(InvalidConfigException::class, function () use ($size) {
            new RecaptchaWidget([
                'name' => 'input',
                'siteKey' => 'hurrdurr',
                'size' => $size,
            ]);
        });
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
        new RecaptchaWidget([
            'name' => 'input',
            'siteKey' => 'hurrdurr',
            'theme' => $theme,
        ]);
    }

    /**
     * @dataProvider notAllowedThemesProvider
     * @param mixed $theme
     */
    public function testNotAllowedThemes($theme)
    {
        $this->tester->expectException(InvalidConfigException::class, function () use ($theme) {
            new RecaptchaWidget([
                'name' => 'input',
                'siteKey' => 'hurrdurr',
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
        new RecaptchaWidget([
            'name' => 'input',
            'siteKey' => 'hurrdurr',
            'type' => $type,
        ]);
    }

    /**
     * @dataProvider notAllowedTypesProvider
     * @param mixed $type
     */
    public function testNotAllowedTypes($type)
    {
        $this->tester->expectException(InvalidConfigException::class, function () use ($type) {
            new RecaptchaWidget([
                'name' => 'input',
                'siteKey' => 'hurrdurr',
                'type' => $type,
            ]);
        });
    }

    public function testRun()
    {
        $widget = new RecaptchaWidget([
            'name' => 'input',
            'siteKey' => 'hurrdurr',
            'expiredCallback' => 'myCallback',
        ]);
        $html = $widget->run();
        $this->tester->assertStringStartsWith('<div', $html);

        Yii::$app->language = 'en';
        $widget = new RecaptchaWidget([
            'name' => 'input',
            'siteKey' => 'hurrdurr',
        ]);
        $widget->run();
        $this->tester->assertStringStartsWith('<div', $html);

        Yii::$app->language = 'ru-RU';
        $widget = new RecaptchaWidget([
            'name' => 'input',
            'siteKey' => 'hurrdurr',
        ]);
        $widget->run();
        $this->tester->assertStringStartsWith('<div', $html);

        Yii::$container->clear('recaptcha');
        $widget = new RecaptchaWidget([
            'name' => 'input',
            'siteKey' => 'hurrdurr',
        ]);
        $widget->run();
        $this->tester->assertStringStartsWith('<div', $html);
    }
}
