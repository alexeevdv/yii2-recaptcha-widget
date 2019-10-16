yii2-recaptcha-widget
=====================

[![Build Status](https://travis-ci.org/alexeevdv/yii2-recaptcha-widget.svg?branch=master)](https://travis-ci.org/alexeevdv/yii2-recaptcha-widget) 
[![codecov](https://codecov.io/gh/alexeevdv/yii2-recaptcha-widget/branch/master/graph/badge.svg)](https://codecov.io/gh/alexeevdv/yii2-recaptcha-widget)
![PHP 5.6](https://img.shields.io/badge/PHP-5.6-green.svg)
![PHP 7.0](https://img.shields.io/badge/PHP-7.0-green.svg) 
![PHP 7.1](https://img.shields.io/badge/PHP-7.1-green.svg) 
![PHP 7.2](https://img.shields.io/badge/PHP-7.2-green.svg)
![PHP 7.3](https://img.shields.io/badge/PHP-7.3-green.svg)


Yii2 wrapper for Google [reCAPTCHA](https://www.google.com/recaptcha).

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ php composer.phar require alexeevdv/yii2-recaptcha-widget "^1.0"
```

or add

```
"alexeevdv/yii2-recaptcha-widget": "^1.0"
```

to the ```require``` section of your `composer.json` file.

## Configuration

### Through application component
```php
'components' => [
    //...
    'recaptcha' => [
        'class' => \alexeevdv\recaptcha\Recaptcha::class,
        'siteKey' => 'YOUR_SITE_KEY',
        'secret' => 'YOUR_SECRET',
    ],
    //...
],
```

### Through widget and validator params
```php
use alexeevdv\recaptcha\RecaptchaValidator;
use alexeevdv\recaptcha\RecaptchaWidget;

// Model validation rules
public function rules()
{
    return [
        //...
        ['recaptcha', RecaptchaValidator::class, 'secret' => 'YOUR_SECRET', 'minimalScore' => 0.6],
        //...
    ];
}

// Widget params
echo RecaptchaWidget::widget([
    'siteKey' => 'YOUR_SITE_KEY',
]);
```

## Usage

```php
use alexeevdv\recaptcha\RecaptchaValidator;
use alexeevdv\recaptcha\RecaptchaWidget;

// Using ActiveForm
// In this case model validation rules will be applied
// You'll need to specify RecaptchaValidator for attribute
echo $form->field($model, 'recaptcha')->widget(RecaptchaWidget::class);

// As standalone field
echo RecaptchaWidget::widget(['name' => 'recaptcha']);
// In this case you need to check value manually
$validator = new RecaptchaValidator();
$isValid = $validator->validateValue(Yii::$app->request->get('recaptcha'));
```

## Usage in tests

To turn off recaptcha checking you need to add this in your test config:
```
'container' => [
    'definitions' => [
        \alexeevdv\recaptcha\RecaptchaValidator::class => ['skipOnEmpty' => true],
    ],
],
```


## Additional component and widget params

```php
/**
 * Optional. Color theme of the widget. "dark" or "light"
 * @var string
 */
public $theme;

/**       
 * Optional. The type of CAPTCHA to serve. "image" or "audio"
 * @var string
 */
public $type;

/**
 * Optional. The size of the widget. "compact" or "normal"
 * @var string
 */
public $size;

/**
 * Optional. The tabindex of the widget and challenge.
 * If other elements in your page use tabindex, it should be set to make user navigation easier.
 * @var integer
 */
public $tabindex;

/**
 * Optional. The name of your callback function, executed when the user submits a successful response.
 * The g-recaptcha-response token is passed to your callback.
 * @var string|JsExpression
 */
public $callback;

/**
 * Optional. The name of your callback function, executed when the reCAPTCHA response expires
 * and the user needs to re-verify.
 * @var string|JsExpression
 */
public $expiredCallback;

/**
 * Optional. The name of your callback function, executed when reCAPTCHA encounters an error 
 * (usually network connectivity) and cannot continue until connectivity is restored. If you specify 
 * a function here, you are responsible for informing the user that they should retry.
 * @var string|JsExpression
 */
public $errorCallback;

/**
 * Optional. Forces the widget to render in a specific language
 * If not set then language is auto detected from application language
 * If set to false then language is autodetected on client side
 */
public $language;

```
