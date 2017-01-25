<?php

namespace alexeevdv\recaptcha;

use Yii;
use yii\base\InvalidConfigException;

class Recaptcha extends \yii\base\Component
{
    /**
     * Site key
     * @var string 
     */
    public $siteKey;

    /**
     * Secret key
     * @var string
     */
    public $secret;
}
