<?php

namespace tests\unit;

use alexeevdv\recaptcha\Recaptcha;
use alexeevdv\recaptcha\RecaptchaValidator;
use Codeception\Stub;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class RecaptchaValidatorTest
 * @package tests\unit
 */
class RecaptchaValidatorTest extends \Codeception\Test\Unit
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
     * @test
     */
    public function initStandalone()
    {
        // `secret` is required
        $this->tester->expectException(InvalidConfigException::class, function () {
            new RecaptchaValidator;
        });

        new RecaptchaValidator([
            'secret' => 'Hurrdurr',
        ]);
    }

    /**
     * @test
     */
    public function initWithComponent()
    {
        // `secret` for component is required
        Yii::$container->set('recaptcha', Stub::make(Recaptcha::class, [
            'secret' => null,
        ]));
        $this->tester->expectException(InvalidConfigException::class, function () {
            new RecaptchaValidator;
        });

        Yii::$container->set('recaptcha', Stub::make(Recaptcha::class, [
            'secret' => 'Hurrdurr',
        ]));
        new RecaptchaValidator;
    }
}
