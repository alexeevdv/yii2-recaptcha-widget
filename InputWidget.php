<?php

namespace alexeevdv\recaptcha;

use \yii\helpers\Html;

class InputWidget extends \yii\widgets\InputWidget
{
    public $name = "recaptcha";

    public $siteKey;

    public $secret;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        if(empty($this->siteKey))
        {
            if (!\yii::$app->has("recaptcha") || empty(\yii::$app->recaptcha->siteKey))
            {
                throw new \yii\base\InvalidConfigException("`siteKey` param is required");
            }
            $this->siteKey = \yii::$app->recaptcha->siteKey;
        }

        $this->view->registerJsFile("https://www.google.com/recaptcha/api.js");

        return Html::tag("div", "", ["class" => "g-recaptcha", "data-sitekey" => $this->siteKey ]);

    }

}
