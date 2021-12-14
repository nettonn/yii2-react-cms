<?php
namespace app\components;

use Yii;
use yii\base\Component;

class PlaceholderComponent extends Component
{
    public $widgets = [];

    public $placeholders = [];

    public $start = '{{{';

    public $end = '}}}';

    protected $startInternal = '{placeholder=';
    protected $endInternal = '=placeholder}';

    /**
     * <p>{{{widgetPlaceholder}}}</p>
     * @var bool
     */
    public $clearAutoParagraph = true;

    protected $startInternalQuoted;
    protected $endInternalQuoted;

    public function init()
    {
        $this->startInternalQuoted = preg_quote($this->startInternal);
        $this->endInternalQuoted = preg_quote($this->endInternal);
    }

    public function get($name)
    {
        return $this->placeholders[$name] ?? false;
    }

    public function set($name, $value)
    {
        $this->placeholders[$name] = $value;
        return true;
    }

    public function addPlaceholdersIfNotExists(Array $placeholders)
    {
        foreach($placeholders as $name => $value) {
            if(!isset($this->_placeholders[$name]) || !$this->_placeholders[$name]) {
                $this->placeholders[$name] = $value;
            }
        }
    }

    public function replaceInContent($content, $placeholders = [])
    {
        foreach($placeholders as $name => $value) {
            $content = str_replace($this->start.$name.$this->end, $value, $content);
        }

        return $content;
    }

    public function replaceAll($content, $foolProof = 0)
    {
        if(!$this->_hasPlaceholders($content))
            return $content;

        $content = $this->_replaceBlocks($content);
        $content = $this->_replaceWidgets($content);
        $content = $this->_replacePlaceholders($content);
        $content = $this->_removeEmpty($content);

        if($foolProof < 10)
            $content = $this->replaceAll($content, $foolProof + 1);

        return $content;
    }

    public function removeAll($content)
    {
        if(!$this->_hasPlaceholders($content))
            return $content;

        return preg_replace('~'.preg_quote($this->start).'.*?'.preg_quote($this->end).'\s?~ui', '', $content);
    }

    protected function _replaceBlocks($content)
    {
        $content = str_replace($this->start, $this->startInternal, $content);
        $content = str_replace($this->end, $this->endInternal, $content);
        return $content;
    }

    protected function _replaceWidgets($content)
    {
        foreach ($this->widgets as $alias => $class) {
            preg_match_all('~' . $this->startInternalQuoted . $alias . '(:([^}]*)?)?' . $this->endInternalQuoted .'~is', $content, $matches);
            if(!isset($matches[0]) || !$matches[0])
                continue;

            $replaces = [];
            foreach($matches[0] as $key => $match) {
                $replaces[$match] = $matches[2][$key] ?? '';
            }

            foreach($replaces as $widgetPlaceholder => $attributes) {
                $widget = $this->_loadWidget($class, $attributes);
                if($this->clearAutoParagraph && false !== strpos($content, '<p>'.$widgetPlaceholder.'</p>')) {
                    $content = str_replace('<p>'.$widgetPlaceholder.'</p>', $widget, $content);
                } else {
                    $content = str_replace($widgetPlaceholder, $widget, $content);
                }
            }
        }
        return $content;
    }

    protected function _replacePlaceholders($content)
    {
        if($this->placeholders) {
            foreach($this->placeholders as $name =>$value) {
                $content = str_replace($this->startInternal.$name.$this->endInternal, $value, $content);
            }
        }

        return $content;
    }

    protected function _removeEmpty($content)
    {
        return preg_replace('~'.$this->startInternalQuoted.'.*?'.$this->endInternalQuoted.'\s?~ui', '', $content);
    }

    protected function _hasPlaceholders($content): bool
    {
        return false !== strpos($content, $this->start) || false !== strpos($content, $this->end);
    }

    protected function _loadWidget($widgetClass, $attributes = '')
    {
        $attrs = $this->_parseWidgetAttributes($attributes);
        ob_start();
        $config = $this->_validateWidgetProperties($widgetClass, $attrs);
        $config['class'] = $widgetClass;
        $widget = Yii::createObject($config);
        echo $widget->run();
        return trim(ob_get_clean());
    }

    protected function _parseWidgetAttributes($attributesString)
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
}
