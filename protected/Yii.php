<?php
/**
 * Yii bootstrap file.
 * Used for enhanced IDE code autocompletion.
 */
class Yii extends \yii\BaseYii
{
    /**
     * @var BaseApplication|WebApplication|ConsoleApplication the application instance
     */
    public static $app;
}

/**
 * Class BaseApplication
 * Used for properties that are identical for both WebApplication and ConsoleApplication
 *
 */
abstract class BaseApplication extends yii\base\Application
{
}

/**
 * Class WebApplication
 * Include only Web application related components here
 *
 * @property \app\components\AdminComponent $admin
 * @property \app\components\SeoComponent $seo
 * @property \app\components\ChunkComponent $chunks
 * @property \app\components\SettingComponent $settings
 * @property \app\components\InlineWidgetsComponent $inlineWidgets
 * @property \app\components\PlaceholderComponent $placeholders
 * @property \app\components\AjaxFileUploadComponent $ajaxFileUpload
 * @property \app\components\MicrodataComponent $microdata
 * @property \yii\queue\Queue $queue
 * @property \sizeg\jwt\Jwt $jwt
 */
class WebApplication extends yii\web\Application
{
}

/**
 * Class ConsoleApplication
 * Include only Console application related components here
 *
 * @property \yii\queue\Queue $queue
 */
class ConsoleApplication extends yii\console\Application
{
}
