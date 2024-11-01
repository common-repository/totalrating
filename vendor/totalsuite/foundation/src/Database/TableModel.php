<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Database;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Select;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;

/**
 * Class Model
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Database
 */
abstract class TableModel extends Model
{
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var bool
     */
    protected $increment = true;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var bool
     */
    protected $exists = false;

    /**
     * TableModel constructor.
     *
     * @param $attributes
     * @throws DatabaseException
     */
    public function __construct($attributes = [])
    {
        $this->query = Query::table($this->table, static::class);
        parent::__construct($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return static|false
     * @throws DatabaseException
     */
    public static function create(array $attributes)
    {
        $model = static::fill($attributes);

        if ($model->save()) {
            return $model;
        }

        return false;
    }

    /**
     * @return mixed
     * @throws DatabaseException
     */
    public function save()
    {
        if ($this->exists) {
            return $this->update();
        }

        return $this->insert();
    }

    /**
     * @return mixed
     * @throws DatabaseException
     */
    public function update()
    {
        $id = $this->getAttribute($this->primaryKey);
        $attributes = $this->getFormattedAttributesForDatabase();
        $result = (bool)$this->query->update($attributes)
                                    ->where($this->primaryKey, $id)
                                    ->execute();

        if ($result) {
            $this->refresh($id);
        }

        return $result;
    }

    /**
     * @param mixed $id
     *
     */
    public function refresh($id)
    {
        $model = static::find($id);
        $this->setRawAttributes($model->getAttributes());
    }

    /**
     * @param $id
     *
     * @return bool|TableModel
     */
    public static function find($id)
    {
        $model = new static();

        return static::query()->where($model->primaryKey, $id)->first();
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return bool
     * @throws DatabaseException
     */
    public function insert()
    {
        $attributes = $this->getFormattedAttributesForDatabase();
        $id = $this->query->insert($attributes)->execute($this->increment);

        if ($id !== false) {
            $this->exists = true;

            if ($this->increment) {
                $this->refresh($id);
            }

            return true;
        }

        return false;
    }

    /**
     * @return Collection
     */
    public static function all()
    {
        return (new static)->query->get();
    }

    /**
     * @return int
     */
    public static function count()
    {
        return static::query()->count();
    }

    /**
     * @return Select|Query
     */
    public static function query()
    {
        return (new static())->getQuery();
    }

    /**
     * @return bool|Query\Delete
     * @throws DatabaseException
     */
    public function delete()
    {
        if ($this->exists) {
            $key = $this->getAttribute($this->primaryKey);

            $deleted = (bool)$this->query->delete()->where($this->primaryKey, $key)->limit(1)->execute();

            if ($deleted) {
                $this->setExists(false);
            }

            return $deleted;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * @return bool
     */
    public function isExists(): bool
    {
        return $this->exists;
    }

    /**
     * @param bool $exists
     *
     * @return TableModel
     */
    public function setExists(bool $exists)
    {
        $this->exists = $exists;

        return $this;
    }
}