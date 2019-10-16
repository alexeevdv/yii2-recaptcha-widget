<?php

namespace tests\unit;

use alexeevdv\recaptcha\Recaptcha;
use alexeevdv\recaptcha\RecaptchaValidator;
use Codeception\Stub;
use Yii;
use yii\base\InvalidConfigException;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Exception as HttpClientException;
use yii\httpclient\Request as HttpClientRequest;
use yii\httpclient\Response as HttpClientResponse;
use yii\web\ErrorHandler;
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
        Yii::$container->set('errorHandler', function () {
            return Stub::make(ErrorHandler::class);
        });
        Yii::$container->set('request', function () {
            return Stub::make(Request::class, [
                'getUserIP' => '127.0.0.1',
            ]);
        });
        Yii::$container->set(HttpClientResponse::class, function () {
            return Stub::make(HttpClientResponse::class, [
                'getData' => [
                    'success' => false,
                ],
            ]);
        });
        Yii::$container->set(HttpClientRequest::class, function () {
            return Stub::make(HttpClientRequest::class, [
                'send' => function () {
                    return Yii::$container->get(HttpClientResponse::class);
                }
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
        Yii::$container->clear(HttpClientRequest::class);
        Yii::$container->clear(HttpClientResponse::class);
        Yii::$container->clear(HttpClient::class);
        Yii::$container->clear('errorHandler');
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
    public function validateValueSuccess()
    {
        Yii::$container->clear(HttpClientResponse::class);
        Yii::$container->set(HttpClientResponse::class, function () {
            return Stub::make(HttpClientResponse::class, [
                'getData' => [
                    'success' => true,
                ],
            ]);
        });
        $validator = new RecaptchaValidator(['secret' => 'Hurrdurr']);
        $this->tester->assertTrue($validator->validate('recaptcha-response'));
    }

    /**
     * @test
     */
    public function validateEmptyValue()
    {
        $validator = new RecaptchaValidator(['secret' => 'Hurrdurr']);
        $this->tester->assertFalse($validator->validate(''));
    }

    /**
     * @test
     */
    public function validateNonEmptyValue()
    {
        $validator = new RecaptchaValidator(['secret' => 'Hurrdurr']);
        $this->tester->assertFalse($validator->validate('recaptcha-response'));
    }

    /**
     * @test
     */
    public function validateValueHttpClientException()
    {
        Yii::$container->clear(HttpClient::class);
        Yii::$container->set(HttpClient::class, function () {
            return Stub::make(HttpClient::class, [
                'get' => function () {
                    throw new HttpClientException;
                },
            ]);
        });
        $validator = new RecaptchaValidator(['secret' => 'Hurrdurr']);
        $this->tester->assertFalse($validator->validate('recaptcha-response'));
    }

    /**
     * @test
     */
    public function validateValueHttpClientExceptionErrorHandler()
    {
        Yii::$container->clear('errorHandler');
        Yii::$container->clear(HttpClient::class);
        Yii::$container->set(HttpClient::class, function () {
            return Stub::make(HttpClient::class, [
                'get' => function () {
                    throw new HttpClientException;
                },
            ]);
        });
        $validator = new RecaptchaValidator(['secret' => 'Hurrdurr']);
        $this->tester->assertFalse($validator->validate('recaptcha-response'));
    }

    public function testValidateWithMinimalScore()
    {
        Yii::$container->clear(HttpClientResponse::class);
        Yii::$container->set(HttpClientResponse::class, function () {
            return Stub::make(HttpClientResponse::class, [
                'getData' => [
                    'success' => true,
                    'score' => 0.5,
                ],
            ]);
        });

        $receivedScore = false;

        $validator = new RecaptchaValidator([
            'secret' => 'Hurrdurr',
            'minimalScore' => 0.6,
            'onScoreReceived' => function ($score) use (&$receivedScore) {
                $receivedScore = $score;
            }
        ]);
        $this->tester->assertFalse($validator->validate('recaptcha-response'));
        $this->tester->assertEquals(0.5, $receivedScore);
    }
}
