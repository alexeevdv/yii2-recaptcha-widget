yii2-recaptcha-widget
=====================

Yii2 wrapper for Google [reCAPTCHA](https://www.google.com/recaptcha).

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require alexeevdv/yii2-recaptcha-widget "1.0.1"
```

or add

```
"alexeevdv/yii2-recaptcha-widget": "1.0.1"
```

to the ```require``` section of your `composer.json` file.

## Configuration

### Through application component
```php
"components" => [
    //...
    "recaptcha" => [
        "class" => "alexeevdv\recaptcha\Recaptcha",
        "siteKey" => "YOUR_SITE_KEY",
        "secret" => "YOUR_SECRET",
    ],
    //...
],
```
### Through widget and validator params
```php
// Model validation rules
public function rules() {
    return [
        //...
        ["recaptcha", \alexeevdv\recaptcha\RecaptchaWidget::className(), "secret" => "YOUR_SECRET"],
        //...
    ];
}

// Widget params
echo \alexeevdv\recaptcha\RecaptchaValidator::widget([
    "siteKey" => "YOUR_SITE_KEY",
]);
```

## Usage

```php
// Using ActiveForm
echo $form->field($model, 'recaptcha')->widget(\alexeevdv\recaptcha\RecaptchaWidget::className());

// As standalone field
echo \alexeevdv\recaptcha\RecaptchaWidget::widget();

```
