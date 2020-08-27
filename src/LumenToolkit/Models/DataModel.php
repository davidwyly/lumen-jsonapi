<?php

declare(strict_types=1);

namespace LumenToolkit\Models;

use LumenToolkit\Helpers\HttpStatus;
use Closure;
use Eloquent;
use Generator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Support\Facades\Cache;
use ReflectionClass;
use ReflectionException;

/**
 * @method static $this        make(array $attributes = [])
 * @method static $this        withGlobalScope($identifier, Closure | Scope $scope)
 * @method static $this        withoutGlobalScope(\Scope | string $scope)
 * @method static $this        withoutGlobalScopes(array | null $scopes = null)
 * @method static array        removedScopes()
 * @method static $this        whereKey($id)
 * @method static $this        where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder      orWhere($column, $operator = null, $value = null)
 * @method static Collection   hydrate(array $items)
 * @method static Collection   fromQuery($query, $bindings = [])
 * @method static Model|Collection|null|DataModel|$this   find($id, $columns = [])
 * @method static Collection   indMany($ids, $columns = [])
 * @method static Model|Collection|$this                                      findOrFail($id, $columns = [])
 * @method static Model|$this  findOrNew($id, $columns = [])
 * @method static Model        firstOrCreate(array $attributes, array $values = [])
 * @method static Model        updateOrCreate(array $attributes, array $values = [])
 * @method static Model|mixed  firstOrFail($columns = [])
 * @method static Model|mixed  firstOr($columns = [], Closure | null $callback = null)
 * @method static value($column)
 * @method static Collection   get($columns = [])
 * @method static Model[]      getModels($columns = [])
 * @method static array        eagerLoadRelations(array $models)
 * @method static Generator    cursor()
 * @method static Collection   pluck($column, $key = null)
 * @method static LengthAwarePaginator  paginate($perPage = null, $columns = [], $pageName = 'page', $page = null)
 * @method static Paginator    simplePaginate($perPage = null, $columns = [], $pageName = 'page', $page = null)
 * @method static Model|$this  create(array $attributes = [])
 * @method static Model|$this  forceCreate(array $attributes)
 * @method static void         onDelete(Closure $callback)
 * @method static mixed        scopes(array $scopes)
 * @method static Builder      applyScopes()
 * @method static $this        without($relations)
 * @method static Model        newModelInstance($attributes = [])
 * @method static Builder      getQuery()
 * @method static $this        setQuery(Builder $query)
 * @method static Builder      toBase()
 * @method static array        getEagerLoads()
 * @method static array        setEagerLoads(array $eagerLoad)
 * @method static Model        getModel()
 * @method static $this        setModel(Model $model)
 * @method static Closure      getMacro($name)
 * @method static bool         chunk(int $count, callable $callback)
 * @method static bool         each(callable $callback, int $count = 1000)
 * @method static Model|null   first($columns = [])
 * @method static mixed        when($value, callable $callback, callable $default = null)
 * @method static Builder      tap(Closure $callback)
 * @method static mixed        unless($value, callable $callback, callable $default = null)
 * @method static Builder has(string $rel, string $op = '>=', int $count = 1, string $bool = 'and', Closure $cb = null)
 * @method static Builder      orHas(string $relation, string $operator = '>=', int $count = 1)
 * @method static Builder      doesntHave(string $relation, string $boolean = 'and', Closure $callback = null)
 * @method static Builder      orDoesntHave(string $relation)
 * @method static Builder      orWhereHas(string $relation, Closure $cb = null, string $operator = '>=', int $count = 1)
 * @method static Builder      whereDoesntHave(string $relation, Closure $callback = null)
 * @method static Builder      orWhereDoesntHave(string $relation, Closure $callback = null)
 * @method static $this        withCount(mixed $relations)
 * @method static Builder      mergeConstraintsFrom(Builder $from)
 * @method static Builder      select($columns = [])
 * @method static Builder      selectRaw($expression, array $bindings = [])
 * @method static Builder      selectSub(Closure | Builder | string $query, string $as)
 * @method static $this        addSelect(array | mixed $column)
 * @method static $this        distinct()
 * @method static $this        from($table)
 * @method static $this        join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
 * @method static Builder      joinWhere($table, $first, $operator, $second, $type = 'inner')
 * @method static Builder      leftJoin($table, $first, $operator = null, $second = null)
 * @method static Builder      leftJoinWhere($table, $first, $operator, $second)
 * @method static Builder      rightJoin($table, $first, $operator = null, $second = null)
 * @method static Builder      rightJoinWhere($table, $first, $operator, $second)
 * @method static Builder      crossJoin($table, $first = null, $operator = null, $second = null)
 * @method static void         mergeWheres($wheres, $bindings)
 * @method static Builder      whereColumn($first, $operator = null, $second = null, $boolean = 'and')
 * @method static Builder      orWhereColumn($first, $operator = null, $second = null)
 * @method static $this        whereRaw($sql, $bindings = [], $boolean = 'and')
 * @method static Builder      orWhereRaw($sql, $bindings = [])
 * @method static $this        whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static Builder      orWhereIn($column, $values)
 * @method static Builder      whereNotIn($column, $values, $boolean = 'and')
 * @method static Builder      orWhereNotIn($column, $values)
 * @method static $this        whereNull($column, $boolean = 'and', $not = false)
 * @method static Builder      orWhereNull($column)
 * @method static Builder      whereNotNull($column, $boolean = 'and')
 * @method static $this        whereBetween($column, array $values, $boolean = 'and', $not = false)
 * @method static Builder      orWhereBetween($column, array $values)
 * @method static Builder      whereNotBetween($column, array $values, $boolean = 'and')
 * @method static Builder      orWhereNotBetween($column, array $values)
 * @method static Builder      orWhereNotNull($column)
 * @method static Builder      whereDate($column, $operator, $value = null, $boolean = 'and')
 * @method static Builder      orWhereDate($column, $operator, $value)
 * @method static Builder      whereTime($column, $operator, $value, $boolean = 'and')
 * @method static Builder      orWhereTime($column, $operator, $value)
 * @method static Builder      whereDay($column, $operator, $value = null, $boolean = 'and')
 * @method static Builder      whereMonth($column, $operator, $value = null, $boolean = 'and')
 * @method static Builder      whereYear($column, $operator, $value = null, $boolean = 'and')
 * @method static Builder      whereNested(Closure $callback, $boolean = 'and')
 * @method static Builder      forNestedWhere()
 * @method static $this        addNestedWhereQuery($query, $boolean = 'and')
 * @method static $this        whereExists(Closure $callback, $boolean = 'and', $not = false)
 * @method static Builder      orWhereExists(Closure $callback, $not = false)
 * @method static Builder      orWhereNotExists(Closure $callback)
 * @method static $this        addWhereExistsQuery(Builder $query, $boolean = 'and', $not = false)
 * @method static $this        dynamicWhere($method, $parameters)
 * @method static $this        groupBy(array $groups = null)
 * @method static $this        having($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder      orHaving($column, $operator = null, $value = null)
 * @method static $this        havingRaw($sql, array $bindings = [], $boolean = 'and')
 * @method static Builder      orHavingRaw($sql, array $bindings = [])
 * @method static $this        orderBy($column, $direction = 'asc')
 * @method static $this        orderByDesc($column)
 * @method static Builder      latest($column = 'created_at')
 * @method static Builder      oldest($column = 'created_at')
 * @method static $this        inRandomOrder($seed = '')
 * @method static $this        orderByRaw($sql, $bindings = [])
 * @method static Builder      skip($value)
 * @method static $this        offset($value)
 * @method static Builder      take($value)
 * @method static $this        limit($value)
 * @method static Builder      forPage($page, $perPage = 15)
 * @method static Builder      forPageAfterId($perPage = 15, $lastId = 0, $column = 'id')
 * @method static Builder      union($query, $all = false)
 * @method static Builder      unionAll($query)
 * @method static $this        lock($value = true)
 * @method static Builder      lockForUpdate()
 * @method static Builder      sharedLock()
 * @method static string       toSql()
 * @method static int          getCountForPagination($columns = [])
 * @method static string       implode($column, $glue = '')
 * @method static bool         exists()
 * @method static int          count($columns = '*')
 * @method static mixed        min($column)
 * @method static mixed        max($column)
 * @method static mixed        sum($column)
 * @method static mixed        avg($column)
 * @method static mixed        average($column)
 * @method static mixed        aggregate($function, $columns = [])
 * @method static float|int    numericAggregate($function, $columns = [])
 * @method static bool         insert(array $values)
 * @method static int          insertGetId(array $values, $sequence = null)
 * @method static bool         updateOrInsert(array $attributes, array $values = [])
 * @method static void         truncate()
 * @method static Expression   raw($value)
 * @method static array        getBindings()
 * @method static array        getRawBindings()
 * @method static $this        setBindings(array $bindings, $type = 'where')
 * @method static $this        addBinding($value, $type = 'where')
 * @method static $this        mergeBindings(Builder $query)
 * @method static Processor    getProcessor()
 * @method static Grammar      getGrammar()
 * @method static $this        useWritePdo()
 * @method static cloneWithout(array $except)
 * @method static cloneWithoutBindings(array $except)
 * @method static void         macro($name, $macro)
 * @method static bool         hasMacro($name)
 * @method static mixed        macroCall($method, $parameters)
 * @method static $this        with($relations)
 *
 * @property $id
 *
 * @mixin Eloquent
 * @mixin Builder
 */
abstract class DataModel extends Model implements Authorizable
{
    public const MAX_LIST_RESULTS       = 1000;
    public const RULE_SIGNED_TINYINT    = 'integer|min:0|max:255';
    public const RULE_UNSIGNED_TINYINT  = 'integer|min:-127|max:128';
    public const RULE_SIGNED_SMALLINT   = 'integer|min:-32768|max:32767';
    public const RULE_UNSIGNED_SMALLINT = 'integer|min:0|max:65535';
    public const RULE_SIGNED_INT        = 'integer|min:-2147483648|max:2147483647';
    public const RULE_UNSIGNED_INT      = 'integer|min:0|max:4294967295';
    public const RULE_SIGNED_BIGINT     = 'integer|min:-9223372036854775808|max:9223372036854775807';
    public const RULE_UNSIGNED_BIGINT   = 'integer|min:0|max:18446744073709551615';
    public const RULE_UNSIGNED_DECIMAL  = [
        '2,2'  => 'numeric|min:0.01|max:.99',
        '3,2'  => 'numeric|min:0.01|max:9.99',
        '4,2'  => 'numeric|min:0.01|max:99.99', # e.g., percent off
        '5,2'  => 'numeric|min:0.01|max:999.99',
        '6,2'  => 'numeric|min:0.01|max:9999.99',
        '7,2'  => 'numeric|min:0.01|max:99999.99',
        '8,2'  => 'numeric|min:0.01|max:999999.99',
        '9,2'  => 'numeric|min:0.01|max:9999999.99', # e.g., large divisible quantities
        '4,4'  => 'numeric|min:0.0001|max:.9999',
        '5,4'  => 'numeric|min:0.0001|max:9.9999', # e.g., rate modifiers
        '6,4'  => 'numeric|min:0.0001|max:99.9999',
        '7,4'  => 'numeric|min:0.0001|max:999.9999',
        '8,4'  => 'numeric|min:0.0001|max:9999.9999',
        '9,4'  => 'numeric|min:0.0001|max:99999.9999',
        '13,4' => 'numeric|min:0.0001|max:999999999.9999', # e.g., money
    ];
    public const RULE_SIGNED_DECIMAL    = [
        '11,8' => 'numeric|min:-999.99999999|max:999.99999999', # e.g, latitude, longitude
    ];

    public const TABLE = null;

    static public $rules               = [];
    static public $calculated          = [];
    static public $api_relations       = [];
    static public $authorization_paths = [];

    protected $table = self::TABLE;

    /**
     * Don't expose these fields
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
        'laravel_through_key',
        'pivot',
    ];

    /**
     * Since we're dealing with BigIntegers, force the keyType to be a string to avoid 32-bit int casting
     *
     * @var string
     */
    protected $keyType = 'string';

    public $protected_properties = [];

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @throws \Exception
     */
    public function __set($key, $value): void
    {
        if (in_array($key, $this->protected_properties)) {
            throw new \Exception ("Property $key is a protected property");
        }
        parent::__set($key, $value);
    }

    protected static function filterAuthorizationPaths(array $authorization, array $filter = null)
    {
        if (is_null($filter)) {
            return $authorization;
        }
        return array_filter($authorization, function ($key) use ($filter) {
            return in_array($key, $filter);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param              $relation
     * @param Closure|null $callback
     * @param string       $operator
     * @param int          $count
     *
     * @return Builder
     */
    public static function whereHas($relation, Closure $callback = null, $operator = '>=', $count = 1)
    {
        if (is_array($relation)) {
            $relation = implode('.', $relation);
        }
        return self::has($relation, $operator, $count, 'and', $callback);
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    public static function getShortName(): string
    {
        $this_class = get_called_class();
        return (new ReflectionClass($this_class))->getShortName();
    }

    /**
     * This method returns all relation methods that have the 'relation' tag
     *
     * @return array
     * @throws ReflectionException
     */
    public static function getTaggedRelations(): array
    {
        $relations  = [];
        $reflection = new ReflectionClass(get_called_class());
        foreach ($reflection->getMethods() as $method) {
            $doc = $method->getDocComment();
            if ($doc && strpos($doc, '@relation') !== false) {
                $relations[] = $method->getName();
            }
        }
        return $relations;
    }

    /**
     * This method updates the cache on model save
     *
     * @param array $options
     *
     * @return Collection|bool
     * @throws ReflectionException
     */
    public function saveAndCache(array $options = []): bool
    {
        $success = $this->save($options);
        if ($success && is_numeric($this->id)) {
            $result = Cache::rememberForever($this->getCacheKey(), function () {
                return $this->whereKey($this->id);
            });
            if (!empty($result)) {
                $success = true;
            }
        }
        return $success;
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    public function getCacheKey(): string
    {
        $reflection = new ReflectionClass(get_called_class());
        return "model:" . $reflection->getShortName() . ":" . $this->id;
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public static function getTypes(): array
    {
        $constants  = [];
        $reflection = new ReflectionClass(get_called_class());
        foreach ($reflection->getConstants() as $constant_name => $constant_value) {
            if (substr($constant_name, 0, 5) == 'TYPE_') {
                $constants[] = $constant_value;
            }
        }
        return $constants;
    }
}
