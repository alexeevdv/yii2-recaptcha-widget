yii2-recaptcha-widget
=====================

[![Build Status](https://travis-ci.org/alexeevdv/yii2-recaptcha-widget.svg?branch=master)](https://travis-ci.org/alexeevdv/yii2-recaptcha-widget) 
[![codecov](https://codecov.io/gh/alexeevdv/yii2-recaptcha-widget/branch/master/graph/badge.svg)](https://codecov.io/gh/alexeevdv/yii2-recaptcha-widget)
![PHP 5.6](https://img.shields.io/badge/PHP-5.6-green.svg)
![PHP 7.0](https://img.shields.io/badge/PHP-7.0-green.svg) 
![PHP 7.1](https://img.shields.io/badge/PHP-7.1-green.svg) 
![PHP 7.2](https://img.shields.io/badge/PHP-7.2-green.svg)


Yii2 wrapper for Google [reCAPTCHA](https://www.google.com/recaptcha).

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ php composer.phar require alexeevdv/yii2-recaptcha-widget "~1.1.1"
```

or add

```
"alexeevdv/yii2-recaptcha-widget": "~1.1.1"
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
public function rules() {
    return [
        //...
        ['recaptcha', RecaptchaValidator::class, 'secret' => 'YOUR_SECRET'],
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
use alexeevdv\recaptcha\RecaptchaWidget;

// Using ActiveForm
echo $form->field($model, 'recaptcha')->widget(RecaptchaWidget::class);

// As standalone field
echo RecaptchaWidget::widget();

```

## Model-less Usage

```php
use alexeevdv\recaptcha\RecaptchaWidget;

// Using ActiveForm
echo  RecaptchaWidget::widget([ "name" => 'recaptcha', ]);

// The submit button
echo Html::submitButton('SUBMIT', ['id' => 'do-submit'])

```
Now on Jquery
```javascript
    $("form").submit(function(event) {
        var recaptcha = $("#g-recaptcha-response").val();
        if (recaptcha === "") {
            event.preventDefault();
            alert("Please use reCaptcha!, Show us that you are not a robot!");
            //swal("reCaptcha!", "Show us that you are not a robot!");  //or swal if you like
        }
    });
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
 * Optional. The name of your callback function to be executed when the user submits a successful CAPTCHA response.
 * The user's response, g-recaptcha-response, will be the input for your callback function.
 * @var string
 */
public $callback;

/**
 * Optional. The name of your callback function to be executed when the recaptcha
 * response expires and the user needs to solve a new CAPTCHA.
 * @var string
 */
public $expiredCallback;

/**
 * Optional. Forces the widget to render in a specific language
 * If not set then language is auto detected from application language
 * If set to false then language is autodetected on client side
 */
public $language;

```
