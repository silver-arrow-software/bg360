<?php namespace Sas\Social\Models;

use Model;

class BaseModel extends Model
{
    protected $isSuperType = false; // set true in super-class model

    protected $isSubType = false; // set true in inherited models

    protected $typeField = 'type'; //override as needed, only set on the super-class model

    public function mapData(array $attributes)
    {
        if (!$this->isSuperType) {
            return $this->newInstance();
        }
        else {
            if (!isset($attributes[$this->typeField])) {
                throw new \DomainException($this->typeField . ' not present in the records of a Super Model');
            }
            else {
                $class = $this->getClass($attributes[$this->typeField]);
                return new $class;
            }
        }
    }

    public function newFromBuilder($attributes = [], $connection = null)
    {
        $m = $this->mapData((array)$attributes)->newInstance([], true);
        $m->setRawAttributes((array)$attributes, true);
        return $m;
    }

    public function newRawQuery()
    {
        $builder = $this->newEloquentBuilder($this->newBaseQueryBuilder());
        $builder->setModel($this)->with($this->with);
        return $builder;
    }

    public function newQuery($excludeDeleted = true)
    {
        $builder = parent::newQuery($excludeDeleted);
        if ($this->isSubType()) {
            $builder->where($this->typeField, $this->getClass($this->typeField));
        }
        return $builder;
    }

    protected function isSubType()
    {
        return $this->isSubType;
    }

    protected function getClass($type)
    {
        return get_class($type);
    }

    protected function getType()
    {
        return get_class($this);
    }

    public function save(array $options = [], $sessionKey = null)
    {
        $this->attributes[$this->typeField] = $this->getType();
        return parent::save($options, $sessionKey);
    }
}
