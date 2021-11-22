<?php
namespace app\components;

use yii\base\Component;

class PlaceholderComponent extends Component
{
    protected $_placeholders = [];

    protected $start = '{{';

    protected $end = '}}';

    protected $startQuoted;
    protected $endQuoted;

    public function init()
    {
        $this->startQuoted = preg_quote($this->start);
        $this->endQuoted = preg_quote($this->end);
    }

    public function get($name)
    {
        if(isset($this->_placeholders[$name]))
            return $this->_placeholders[$name];
        return false;
    }

    public function set($name, $value)
    {
        $this->_placeholders[$name] = $value;
        return true;
    }

    public function replaceAll($content)
    {
        if($this->_placeholders) {
            foreach($this->_placeholders as $name =>$value) {
                $content = str_replace($this->start.$name.$this->end, $value, $content);
            }
        }

        return $content;
    }

    public function replace_in_text($text, $placeholders = [])
    {
        foreach($placeholders as $name => $value) {
            $text = str_replace($this->start.$name.$this->end, $value, $text);
//            $text = preg_replace('~'.$this->startQuoted.preg_quote($name).$this->endQuoted.'~ui', $value, $text);
        }

        return $text;
    }

    public function remove_empty($content)
    {
        return preg_replace('~'.$this->startQuoted.'.*?'.$this->endQuoted.'\s?~ui', '', $content);
    }

    public function addPlaceholdersIfNotExists(Array $placeholders)
    {
        foreach($placeholders as $name => $value) {
            if(!isset($this->_placeholders[$name]) || !$this->_placeholders[$name]) {
                $this->_placeholders[$name] = $value;
            }
        }
    }
}
