<?php

namespace tests\unit;

use alexeevdv\recaptcha\Recaptcha;
use alexeevdv\recaptcha\RecaptchaValidator;
use Codeception\Stub;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Request;

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
        Yii::$container->set('request', function () {
            return Stub::make(Request::class, [
                'getUserIP' => '127.0.0.1',
                'post' => 'random-post-value',
                'get' => 'random-get-value',
            ]);
        });
    }

    /**
     * @inheritdoc
     */
    protected function _after()
    {
        Yii::$container->clear('i18n');
        Yii::$container->clear('recaptcha');
        Yii::$container->clear('request');
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

    /**
     * @test
     */
    public function validateValue()
    {
        $validator = new RecaptchaValidator(['secret' => 'Hurrdurr']);
        $this->tester->assertTrue($validator->validate('some-random-code'));
    }

    /**
     * @test
     */
    public function validateEmptyValuePost()
    {
        Yii::$container->set('request', function () {
            return Stub::make(Request::class, [
                'getUserIP' => '127.0.0.1',
                'post' => 'random-post-value',
                'getIsPost' => true,
            ]);
        });
        $validator = new RecaptchaValidator(['secret' => 'Hurrdurr']);
        $this->tester->assertTrue($validator->validate(''));
    }

    /**
     * @test
     */
    public function validateEmptyValueGet()
    {
        Yii::$container->set('request', function () {
            return Stub::make(Request::class, [
                'getUserIP' => '127.0.0.1',
                'get' => 'random-get-value',
                'getIsGet' => true,
            ]);
        });
        $validator = new RecaptchaValidator(['secret' => 'Hurrdurr']);
        $this->tester->assertTrue($validator->validate(''));
    }


    /**
     * @test
     */
    public function validateEmptyValue()
    {
        Yii::$container->set('request', function () {
            return Stub::make(Request::class, [
                'getUserIP' => '127.0.0.1',
            ]);
        });
        $validator = new RecaptchaValidator(['secret' => 'Hurrdurr']);
        $this->tester->assertFalse($validator->validate(''));
    }
}
