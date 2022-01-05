<?php namespace app\traits;

trait ModelType
{
    public static function instantiate($row)
    {
        return new static(['scenario' => $row['type']]);
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        foreach($this->getTypeNames() as $typeName) {
            $scenarios[$typeName] = $scenarios[self::SCENARIO_DEFAULT];
        }
        return $scenarios;
    }

    public function getCurrentType()
    {
        $typeNames = $this->getTypeNames();
        if($this->type && in_array($this->type, $typeNames)) {
            return $this->type;
        }
        if($typeNames && in_array($this->scenario, $typeNames)) {
            return $this->scenario;
        }
        return null;
    }

    protected function getTypeRules()
    {
        $rules = $this->getCurrentTypeParam('rules');
        foreach($this->getTypeFileAttributes() as $attribute => $params) {
            $attributeId = $params['attribute_id'] ?? $attribute.'_id';
            $rules[] = [$attributeId, 'integer', 'allowArray' => true];
        }
        return $rules;
    }

    protected function getTypeAttributeLabels()
    {
        return $this->getCurrentTypeParam('attributeLabels');
    }

    protected function getTypeFileAttributes()
    {
        return $this->getCurrentTypeParam('fileAttributes');
    }

    protected function getTypeDynamicAttributes()
    {
        return $this->getCurrentTypeParam('dynamicAttributes');
    }

    protected function getCurrentTypeParam($param, $default = [])
    {
        $types = $this->getTypes();
        $type = $this->getCurrentType();
        if(!isset($types[$type]))
            return $default;
        if(!isset($types[$type][$param]))
            return $default;
        return $types[$type][$param];
    }

    abstract protected function configureTypes();

    protected $_types;

    protected function getTypes()
    {
        if(null === $this->_types) {
            $this->_types = $this->configureTypes();
        }
        return $this->_types;
    }

    protected $_typeNames;

    protected function getTypeNames()
    {
        if(null === $this->_typeNames) {
            $this->_typeNames = array_keys($this->getTypes());
        }
        return $this->_typeNames;
    }
}
