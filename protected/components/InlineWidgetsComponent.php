<?php namespace app\components;

use yii\base\Component;

/**
 * Rewrite to component from howardeagle/yii2-inline-widgets-behavior
 */
class InlineWidgetsComponent extends Component
{
    /**
     * @var string marker of block begin
     */
    public $startBlock = '[*';
    /**
     * @var string marker of block end
     */
    public $endBlock = '*]';
    /**
     * @var array of allowed widgets
     */
    public $widgets = [];

    protected $_widgetToken;

    public $model = null;

    public function init()
    {
        $this->_initToken();
        parent::init();
    }

    /**
     * Content parser
     * @param $text
     * @return mixed
     */
    public function decodeWidgets($text, $model = null, $foolProof = 0)
    {
        if(!$this->hasWidgets($text))
            return $text;

        if($model)
            $this->model = $model;

        $text = $this->_clearAutoParagraphs($text);
        $text = $this->_replaceBlocks($text);
        $text = $this->_processWidgets($text);
        $text = $this->_clearWidgets($text);

        if($foolProof < 10)
            $text = $this->decodeWidgets($text, $model, $foolProof);

        return $text;
    }

    protected function hasWidgets($text)
    {
        return false !== strpos($text, $this->startBlock) || false !== strpos($text, $this->endBlock);
    }

    /**
     * Renders widgets
     */
    protected function _processWidgets($text)
    {
        if (preg_match('|\{' . $this->_widgetToken . ':.+?' . $this->_widgetToken . '\}|is', $text)) {
            foreach ($this->widgets as $alias => $class) {
                while (preg_match('/\{' . $this->_widgetToken . ':' . $alias . '(\|([^}]*)?)?' . $this->_widgetToken . '\}/is', $text, $p)) {
                    $text = str_replace($p[0], $this->_loadWidget($class, isset($p[2]) ? $p[2] : ''), $text);
                }
            }
            return $text;
        }
        return $text;
    }

    protected function _clearWidgets($text)
    {
        return preg_replace('|\{' . $this->_widgetToken . ':.+?' . $this->_widgetToken . '\}|is', '', $text);
    }

    protected function _initToken()
    {
        $this->_widgetToken = md5(microtime());
    }

    protected function _replaceBlocks($text)
    {
        $text = str_replace($this->startBlock, '{' . $this->_widgetToken . ':', $text);
        $text = str_replace($this->endBlock, $this->_widgetToken . '}', $text);
        return $text;
    }

    protected function _clearAutoParagraphs($output)
    {
        $output = str_replace('<p>' . $this->startBlock, $this->startBlock, $output);
        $output = str_replace($this->endBlock . '</p>', $this->endBlock, $output);
        return $output;
    }

    protected function _loadWidget($widgetClass, $attributes = '')
    {
        $attrs = $this->_parseAttributes($attributes);
        $cache = $this->_extractCacheExpireTime($attrs);
        $index = 'widget_' . $widgetClass . '_' . serialize($attrs);
        if ($cache && $cachedHtml = \Yii::$app->cache->get($index)) {
            $html = $cachedHtml;
        } else {
            ob_start();
            $config = $this->_validateWidgetProperties($widgetClass, $attrs);
            $config['class'] = $widgetClass;
            $widget = \Yii::createObject($config);
            if ($this->model !== null && property_exists($widget, 'model'))
                $widget->model = $this->model;
            echo $widget->run();
            $html = trim(ob_get_clean());
            \Yii::$app->cache->set($index, $html, $cache);
        }
        return $html;
    }

    protected function _parseAttributes($attributesString)
    {
        $params = explode(';', $attributesString);
        $attrs = [];
        foreach ($params as $param) {
            if ($param) {
                list($attribute, $value) = explode('=', $param);
                if ($value) $attrs[$attribute] = trim($value);
            }
        }
        ksort($attrs);
        return $attrs;
    }

    protected function _validateWidgetProperties($widgetClass, $attrs = [])
    {
        foreach ($attrs as $property => $value) {
            if (!property_exists($widgetClass, $property))
                unset($attrs[$property]);
        }

        return $attrs;
    }

    protected function _extractCacheExpireTime(&$attrs)
    {
        $cache = 0;
        if (isset($attrs['cache'])) {
            $cache = (int)$attrs['cache'];
            unset($attrs['cache']);
        }
        return $cache;
    }
}
