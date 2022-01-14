<?php namespace app\behaviors;

use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

abstract class BaseBehavior extends Behavior
{
    /**
     * @var BaseActiveRecord
     */
    public $owner;

    public $validate = false;

    protected $detectPk = false;
    protected $allowCompositePk = false;
    protected $ownerClass;
    protected $ownerPkAttribute = 'id';

    /**
     * @param BaseActiveRecord $owner
     * @throws InvalidConfigException
     */
    public function attach($owner)
    {
        parent::attach($owner);

        if($this->validate)
            $this->validate();

        $this->ownerClass = get_class($owner);

        if($this->detectPk) {
            $this->ownerPkAttribute = ($this->ownerClass)::primaryKey()[0] ?? null;
            if(!$this->ownerPkAttribute) {
                throw new InvalidConfigException('Primary key not set');
            }
        }
    }

    protected function validate()
    {
        $ownerClass = get_class($this->owner);
        if(!is_subclass_of($ownerClass, ActiveRecord::class)) {
            throw new InvalidConfigException('Attach allowed only for children of ActiveRecord');
        }

        if(!$this->allowCompositePk) {
            if(count($ownerClass::primaryKey()) > 1) {
                throw new InvalidConfigException('Composite primary keys not allowed');
            }
        }
    }
}
