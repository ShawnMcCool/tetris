<?php

declare(strict_types=1);

namespace Glowy\Macroable;

use BadMethodCallException;
use Closure;
use ReflectionClass;
use ReflectionMethod;

use function sprintf;

trait Macroable
{
    /**
     * The registered string macros.
     *
     * @var array
     */
    protected static array $macros = [];

    /**
     * Register a custom macro.
     *
     * @param  string          $name  Name.
     * @param  object|callable $macro Macro.
     */
    public static function macro(string $name, $macro): void
    {
        static::$macros[$name] = $macro;
    }

    /**
     * Mix another object into the class.
     *
     * @param  object $mixin   Mixin.
     * @param  bool   $replace Replace.
     *
     * @throws ReflectionException
     */
    public static function mixin(object $mixin, bool $replace = true): void
    {
        $methods = (new ReflectionClass($mixin))->getMethods(
            ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
        );

        foreach ($methods as $method) {
            if (! $replace && static::hasMacro($method->name)) {
                continue;
            }

            $method->setAccessible(true);
            static::macro($method->name, $method->invoke($mixin));
        }
    }

    /**
     * Checks if macro is registered.
     *
     * @param  string $name Name
     */
    public static function hasMacro(string $name): bool
    {
        return isset(static::$macros[$name]);
    }

    /**
     * Flush the existing macros.
     *
     * @return void
     */
    public static function flushMacros()
    {
        static::$macros = [];
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string $method     Method.
     * @param  array  $parameters Parameters.
     *
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public static function __callStatic(string $method, array $parameters)
    {
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method
            ));
        }

        $macro = static::$macros[$method];

        if ($macro instanceof Closure) {
            $macro = $macro->bindTo(null, static::class);
        }

        return $macro(...$parameters);
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string $method     Method.
     * @param  array  $parameters Parameters.
     *
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public function __call(string $method, array $parameters)
    {
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method
            ));
        }

        $macro = static::$macros[$method];

        if ($macro instanceof Closure) {
            $macro = $macro->bindTo($this, static::class);
        }

        return $macro(...$parameters);
    }
}
