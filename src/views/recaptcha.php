<?php

use yii\base\Model;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Request;
use yii\web\View;

/**
 * @var Model $model
 * @var string $attribute
 * @var string $id
 * @var string $name
 * @var string $container
 * @var array $containerOptions
 * @var string $language
 * @var string $apiJs
 * @var View $this
 */

if ($model) {
    echo Html::activeHiddenInput($model, $attribute, ['id' => $id . '-input']);
} else {
    echo Html::hiddenInput($name);
}

echo Html::tag($container, '', ArrayHelper::merge($containerOptions, ['id' => $id . '-container']));

$url = $apiJs;
if ($language !== false) {
    $url .= '?hl=' . $language;
}

$jsCallbackName = $id . '_recaptchaOnloadCallback';

$url .= '&onload=' . $jsCallbackName . '&render=explicit';

$this->registerJsFile($url, [
    'position' => View::POS_END,
    'async' => true,
    'defer' => true,
]);

$jsOptions = Json::encode($options);

$this->registerJs(<<<JS
    $jsCallbackName = function () {
        grecaptcha.render('$id-container', $jsOptions);
    }
JS
, View::POS_HEAD);


/** @var Request $request */
$request = Instance::ensure('request', Request::class);
if ($request->isPjax) {
    $this->registerJs($jsCallbackName . '();', View::POS_END);
}

