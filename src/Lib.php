<?php

namespace Gzhegow\Router;

class Lib
{
    /**
     * @return object{ errors: array }
     */
    public static function php_errors() : object
    {
        static $stack;

        $stack = $stack
            ?? new class {
                public $errors = [];
            };

        return $stack;
    }

    /**
     * @return object{ list: array }
     */
    public static function php_errors_current() : object
    {
        $stack = static::php_errors();

        $errors = end($stack->errors);

        return $errors;
    }

    /**
     * @return object{ list: array }
     */
    public static function php_errors_new() : object
    {
        $errors = new class {
            public $list = [];
        };

        return $errors;
    }

    /**
     * @return object{ list: array }
     */
    public static function php_errors_start(object &$errors = null) : object
    {
        $stack = static::php_errors();

        $errors = static::php_errors_new();
        $stack->errors[] = $errors;

        return $errors;
    }

    public static function php_errors_end(?object $until) : array
    {
        $stack = static::php_errors();

        $errors = static::php_errors_new();

        while ( count($stack->errors) ) {
            $current = array_pop($stack->errors);

            foreach ( $current->list as $error ) {
                $errors->list[] = $error;
            }

            if ($current === $until) {
                break;
            }
        }

        return $errors->list;
    }

    public static function php_error($error, $result = null) // : mixed
    {
        $current = static::php_errors_current();

        $current->list[] = $error;

        return $result;
    }


    public static function php_throwable_args(...$args) : array
    {
        $len = count($args);

        $messageList = null;
        $codeList = null;
        $previousList = null;
        $messageCodeList = null;
        $messageDataList = null;

        $__unresolved = [];

        for ( $i = 0; $i < $len; $i++ ) {
            $arg = $args[ $i ];

            if (is_a($arg, \Throwable::class)) {
                $previousList[ $i ] = $arg;

                continue;
            }

            if (
                is_array($arg)
                || is_a($arg, \stdClass::class)
            ) {
                $messageData = (array) $arg;

                $messageString = isset($messageData[ 0 ])
                    ? (string) $messageData[ 0 ]
                    : '';

                if ('' !== $messageString) {
                    unset($messageData[ 0 ]);

                    $messageList[ $i ] = $messageString;
                }

                $messageDataList[ $i ] = $messageData;

                continue;
            }

            if (is_int($arg)) {
                $codeList[ $i ] = $arg;

                continue;
            }

            if ('' !== ($vString = (string) $arg)) {
                $messageList[ $i ] = $vString;

                continue;
            }

            $__unresolved[ $i ] = $arg;
        }

        for ( $i = 0; $i < $len; $i++ ) {
            if (isset($messageList[ $i ])) {
                if (preg_match('/^[a-z](?!.*\s)/i', $messageList[ $i ])) {
                    $messageCodeList[ $i ] = strtoupper($messageList[ $i ]);
                }
            }
        }

        $result = [];

        $result[ 'messageList' ] = $messageList;
        $result[ 'codeList' ] = $codeList;
        $result[ 'previousList' ] = $previousList;
        $result[ 'messageCodeList' ] = $messageCodeList;
        $result[ 'messageDataList' ] = $messageDataList;

        $messageDataList = $messageDataList ?? [];

        $message = $messageList ? end($messageList) : null;
        $code = $codeList ? end($codeList) : null;
        $previous = $previousList ? end($previousList) : null;
        $messageCode = $messageCodeList ? end($messageCodeList) : null;

        $messageData = $messageDataList
            ? array_replace(...$messageDataList)
            : [];

        $messageObject = (object) ([ $message ] + $messageData);

        $result[ 'message' ] = $message;
        $result[ 'code' ] = $code;
        $result[ 'previous' ] = $previous;
        $result[ 'messageCode' ] = $messageCode;
        $result[ 'messageData' ] = $messageData;

        $result[ 'messageObject' ] = $messageObject;

        $result[ '__unresolved' ] = $__unresolved;

        return $result;
    }



    /**
     * > gzhegow, строит индекс ключей (int)
     * > [ 0 => 1, 2 => true, 3 => false ] -> [ 1 => true, 2 => true, 3 => false ]
     *
     * @return array<int, bool>
     */
    public static function array_index_int(array $array, array ...$arrays) : array
    {
        array_unshift($arrays, $array);

        $index = array_merge(...$arrays);

        $result = [];

        foreach ( $index as $k => $v ) {
            if (is_int($v)) {
                $key = $v;

                $result[ $key ] = true;

            } elseif (! isset($result[ $k ])) {
                $key = $k;

                $v = (bool) $v;

                if ($v) {
                    $result[ $key ] = true;
                }
            }
        }

        return $result;
    }

    /**
     * > gzhegow, строит индекс ключей (string)
     * > [ 0 => 'key1', 'key2' => true, 'key3' => false ] -> [ 'key1' => true, 'key2' => true, 'key3' => false ]
     *
     * @return array<string, bool>
     */
    public static function array_index_string(array $array, array ...$arrays) : array
    {
        array_unshift($arrays, $array);

        $index = array_merge(...$arrays);

        $result = [];

        foreach ( $index as $k => $v ) {
            if (is_string($k) && ($k !== '')) {
                $key = $k;

                $v = (bool) $v;

                if ($v) {
                    $result[ $key ] = true;
                }

            } elseif (is_string($v) && ! isset($result[ $v ])) {
                $key = $v;

                $result[ $key ] = true;
            }
        }

        return $result;
    }


    /**
     * > gzhegow, встроенная функция всегда требует два массива на вход, вынуждая разруливать ифами то, что не нужно
     */
    public static function array_diff(array ...$arrays) : array
    {
        if (! $arrays) {
            return [];
        }

        if (count($arrays) === 1) {
            return $arrays[ 0 ];
        }

        $result = array_diff(...$arrays);

        return $result;
    }

    /**
     * > gzhegow, встроенная функция всегда требует два массива на вход, вынуждая разруливать ифами то, что не нужно
     */
    public static function array_diff_key(array ...$arrays) : array
    {
        if (! $arrays) {
            return [];
        }

        if (count($arrays) === 1) {
            return $arrays[ 0 ];
        }

        $result = array_diff_key(...$arrays);

        return $result;
    }

    /**
     * > gzhegow, встроенная функция всегда требует два массива на вход, вынуждая разруливать ифами то, что не нужно
     */
    public static function array_intersect(array ...$arrays) : array
    {
        if (! $arrays) {
            return [];
        }

        if (count($arrays) === 1) {
            return $arrays[ 0 ];
        }

        $result = array_intersect(...$arrays);

        return $result;
    }

    /**
     * > gzhegow, встроенная функция всегда требует два массива на вход, вынуждая разруливать ифами то, что не нужно
     */
    public static function array_intersect_key(array ...$arrays) : array
    {
        if (! $arrays) {
            return [];
        }

        if (count($arrays) === 1) {
            return $arrays[ 0 ];
        }

        $result = array_intersect_key(...$arrays);

        return $result;
    }



    /**
     * @param callable                                                          $fn
     * @param object{ microtime?: float, output?: string, result?: mixed }|null $except
     * @param array|null                                                        $error
     * @param resource|null                                                     $stdout
     */
    public static function assert_call(
        array $trace,
        $fn, object $except = null, array &$error = null,
        $stdout = null
    ) : bool
    {
        $error = null;

        $microtime = microtime(true);

        ob_start();
        $result = $fn();
        $output = ob_get_clean();

        if (property_exists($except, 'result')) {
            if ($except->result !== $result) {
                $microtime = round(microtime(true) - $microtime, 6);

                static::debug_diff_var_dump($result, $except->result, $diff);

                $error = [
                    'Test result check failed',
                    [
                        'result'    => $result,
                        'expect'    => $except->result,
                        'diff'      => $diff,
                        'microtime' => $microtime,
                        'file'      => $trace[ 0 ][ 'file' ],
                        'line'      => $trace[ 0 ][ 'line' ],
                    ],
                ];

                if (null !== $stdout) {
                    fwrite($stdout, '------' . PHP_EOL);
                    fwrite($stdout, '[ ERROR ] Test result check failed. ' . $microtime . 's' . PHP_EOL);
                    fwrite($stdout, $trace[ 0 ][ 'file' ] . ' / ' . $trace[ 0 ][ 'line' ] . PHP_EOL);
                    fwrite($stdout, $diff . PHP_EOL);
                    fwrite($stdout, '------' . PHP_EOL);
                }

                return false;
            }
        }

        if (property_exists($except, 'output')) {
            if (static::debug_diff($output, $except->output, $diff)) {
                $microtime = round(microtime(true) - $microtime, 6);

                $error = [
                    'Test result check failed',
                    [
                        'output'    => $output,
                        'expect'    => $except->output,
                        'diff'      => $diff,
                        'microtime' => $microtime,
                        'file'      => $trace[ 0 ][ 'file' ],
                        'line'      => $trace[ 0 ][ 'line' ],
                    ],
                ];

                if (null !== $stdout) {
                    fwrite($stdout, '------' . PHP_EOL);
                    fwrite($stdout, '[ ERROR ] Test output check failed. ' . $microtime . 's' . PHP_EOL);
                    fwrite($stdout, $trace[ 0 ][ 'file' ] . ' / ' . $trace[ 0 ][ 'line' ] . PHP_EOL);
                    fwrite($stdout, $diff . PHP_EOL);
                    fwrite($stdout, '------' . PHP_EOL);
                }

                return false;
            }
        }

        $microtime = round(microtime(true) - $microtime, 6);

        if (property_exists($except, 'microtime')) {
            if ($except->microtime < $microtime) {
                $diff = $microtime - $except->microtime;

                $error = [
                    'Test result check failed',
                    [
                        'microtime' => $microtime,
                        'expect'    => $except->microtime,
                        'diff'      => $diff,
                        'file'      => $trace[ 0 ][ 'file' ],
                        'line'      => $trace[ 0 ][ 'line' ],
                    ],
                ];

                if (null !== $stdout) {
                    fwrite($stdout, '------' . PHP_EOL);
                    fwrite($stdout, '[ ERROR ] Test microtime check failed. ' . $microtime . 's' . PHP_EOL);
                    fwrite($stdout, $trace[ 0 ][ 'file' ] . ' / ' . $trace[ 0 ][ 'line' ] . PHP_EOL);
                    fwrite($stdout, $diff . PHP_EOL);
                    fwrite($stdout, '------' . PHP_EOL);
                }

                return false;
            }
        }

        if (null !== $stdout) {
            fwrite($stdout, '[ OK ] Test success. ' . $microtime . 's' . PHP_EOL);
        }

        return true;
    }



    public static function debug_diff(string $a, string $b, string &$result = null) : bool
    {
        $result = null;

        static::os_eol($a, $aLines);
        static::os_eol($b, $bLines);

        $cnt = max(
            count($aLines),
            count($bLines)
        );

        $lines = [];

        $isDiff = false;

        for ( $i = 0; $i < $cnt; $i++ ) {
            $aLine = ($aLines[ $i ] ?? '{ NULL }') ?: '""';
            $bLine = ($bLines[ $i ] ?? '{ NULL }') ?: '""';

            if ($aLine === $bLine) {
                $lines[] = $aLine;

                continue;
            }

            $lines[] = '--- ' . $aLine;
            $lines[] = '+++ ' . $bLine;

            $isDiff = true;
        }

        $result = implode(PHP_EOL, $lines);

        return $isDiff;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     */
    public static function debug_diff_var_dump($a, $b, string &$result = null) : bool
    {
        ob_start();
        var_dump($a);
        $aString = ob_get_clean();

        ob_start();
        var_dump($b);
        $bString = ob_get_clean();

        $isDiff = static::debug_diff($aString, $bString, $result);

        return $isDiff;
    }

    public static function debug_dump($value, ...$values) : string
    {
        $output = static::debug_line([ 'with_ids' => true, 'with_objects' => false ], $value, ...$values);

        return $output;
    }

    public static function debug_line(array $options, $value, ...$values) : string
    {
        array_unshift($values, $value);

        $valueExports = [];
        foreach ( $values as $i => $v ) {
            $line = static::debug_var_print($v, $options);

            $line = trim($line);
            $line = preg_replace('/\s+/', ' ', $line);

            $valueExports[ $i ] = $line;
        }

        $output = implode(' | ', $valueExports);

        return $output;
    }

    public static function debug_text(array $options, $value, ...$values) : string
    {
        array_unshift($values, $value);

        $valueExports = [];
        foreach ( $values as $i => $v ) {
            $line = static::debug_var_print($v, $options);

            $valueExports[ $i ] = $line;
        }

        $output = implode(PHP_EOL . PHP_EOL, $valueExports);

        return $output;
    }

    public static function debug_var_dump($var, array $options = []) : string
    {
        $maxlen = $options[ 'maxlen' ] ?? null;
        $withArrays = $options[ 'with_arrays' ] ?? true;
        $withIds = $options[ 'with_ids' ] ?? true;

        if ($maxlen < 1) $maxlen = null;

        $type = null;
        $dump = null;

        if (is_iterable($var)) {
            if (is_object($var)) {
                $id = $withIds
                    ? ' # ' . spl_object_id($var)
                    : '';

                $type = 'iterable(' . get_class($var) . $id . ')';

            } else {
                $type = 'array(' . count($var) . ')';

                if ($withArrays) {
                    $dump = [];

                    foreach ( $var as $i => $v ) {
                        // ! recursion
                        $dump[ $i ] = static::debug_var_dump(
                            $v,
                            []
                            + [ 'with_arrays' => false ]
                            + $options
                        );
                    }

                    $dump = var_export($dump, true);
                }
            }

        } else {
            if (is_object($var)) {
                $id = $withIds
                    ? ' # ' . spl_object_id($var)
                    : '';

                $type = 'object(' . get_class($var) . $id . ')';

                if (method_exists($var, '__debugInfo')) {
                    ob_start();
                    var_dump($var);
                    $dump = ob_get_clean();
                }

            } elseif (is_string($var)) {
                $type = 'string(' . strlen($var) . ')';

                $dump = "\"{$var}\"";

            } elseif (is_resource($var)) {
                $id = $withIds
                    ? ' # ' . ((int) $var)
                    : '';

                $type = '{ resource(' . get_resource_type($var) . $id . ') }';

            } else {
                $type = null
                    ?? (($var === null) ? '{ NULL }' : null)
                    ?? (($var === false) ? '{ FALSE }' : null)
                    ?? (($var === true) ? '{ TRUE }' : null)
                    //
                    ?? (is_int($var) ? (var_export($var, 1)) : null) // INF
                    ?? (is_float($var) ? (var_export($var, 1)) : null) // NAN
                    //
                    ?? null;
            }
        }

        $_value = $type;
        if (null !== $dump) {
            if (null !== $maxlen) {
                $dump = explode("\n", $dump);

                foreach ( $dump as $i => $v ) {
                    $_v = $v;

                    $_v = trim($_v);
                    $_v = substr($_v, 0, $maxlen) . '...';

                    $dump[ $i ] = $_v;
                }

                $dump = implode(PHP_EOL, $dump);
            }

            $_value = "{$type} : {$dump}";
        }

        $_value = "{ {$_value} }";

        return $_value;
    }

    public static function debug_var_export($var, array $options = []) : string
    {
        $indent = $options[ 'indent' ] ?? "  ";
        $newline = $options[ 'newline' ] ?? PHP_EOL;

        switch ( gettype($var) ) {
            case "NULL":
                $result = "NULL";
                break;

            case "boolean":
                $result = ($var === true) ? "TRUE" : "FALSE";
                break;

            case "string":
                $result = '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
                break;

            case "array":
                $keys = array_keys($var);

                foreach ( $keys as $key ) {
                    if (is_string($key)) {
                        $isList = false;

                        break;
                    }
                }
                $isList = $isList ?? true;

                $isListIndexed = $isList
                    && ($keys === range(0, count($var) - 1));

                $lines = [];
                foreach ( $var as $key => $value ) {
                    $line = $indent;

                    if (! $isListIndexed) {
                        $line .= is_string($key) ? "\"{$key}\"" : $key;
                        $line .= " => ";
                    }

                    // ! recursion
                    $line .= static::debug_var_export($value, $options);

                    $lines[] = $line;
                }

                $result = "["
                    . $newline
                    . implode("," . $newline, $lines) . $newline
                    . $indent . "]";

                break;

            default:
                $result = var_export($var, true);

                break;
        }

        return $result;
    }

    public static function debug_var_print($var, array $options = []) : string
    {
        $indent = $options[ 'indent' ] ?? "  ";
        $newline = $options[ 'newline' ] ?? PHP_EOL;
        $withArrays = $options[ 'with_arrays' ] ?? true;
        $withIds = $options[ 'with_ids' ] ?? true;
        $withObjects = $options[ 'with_objects' ] ?? true;

        switch ( gettype($var) ) {
            case "string":
                $result = '"' . $var . '"';
                break;

            case "array":
                if (! $withArrays) {
                    $result = 'array(' . count($var) . ')';

                } else {
                    $keys = array_keys($var);

                    foreach ( $keys as $k ) {
                        if (is_string($k)) {
                            $isList = false;

                            break;
                        }
                    }
                    $isList = $isList ?? true;

                    $isListIndexed = $isList
                        && ($keys === range(0, count($var) - 1));

                    $lines = [];
                    foreach ( $var as $k => $v ) {
                        $line = $indent;

                        if (! $isListIndexed) {
                            $line .= is_string($k) ? "\"{$k}\"" : $k;
                            $line .= " => ";
                        }

                        // ! recursion
                        $line .= static::debug_var_print($v, [ 'with_arrays' => false ] + $options);

                        $lines[] = $line;
                    }

                    $result = "["
                        . $newline
                        . implode("," . $newline, $lines) . $newline
                        . $indent . "]";
                }

                break;

            case "object":
                if ($withObjects) {
                    $result = var_export($var, true);

                } else {
                    $id = $withIds
                        ? ' # ' . spl_object_id($var)
                        : null;

                    $result = '{ object(' . get_class($var) . $id . ') }';
                }

                break;

            case "resource":
                if ($withObjects) {
                    $result = var_export($var, true);

                } else {
                    $id = $withIds
                        ? ' # ' . spl_object_id($var)
                        : null;

                    $result = '{ resource(' . get_resource_type($var) . $id . ') }';
                }

                break;

            default:
                $result = static::debug_var_export($var, $options);

                break;
        }

        return $result;
    }



    /**
     * @param string[]|null $lines
     */
    public static function os_eol(string $str, array &$lines = null) : string
    {
        $lines = null;

        $array = explode("\n", $str);

        foreach ( $array as $i => $line ) {
            $array[ $i ] = rtrim($line, "\r");
        }

        $lines = $array;

        $output = implode("\n", $array);

        return $output;
    }



    public static function parse_string($value) : ?string
    {
        if (is_string($value)) {
            return $value;
        }

        if (
            (null === $value)
            || is_array($value)
            || is_resource($value)
        ) {
            return null;
        }

        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                $_value = (string) $value;

                return $_value;
            }

            return null;
        }

        $_value = $value;
        $status = @settype($_value, 'string');

        if ($status) {
            return $_value;
        }

        return null;
    }

    public static function parse_astring($value) : ?string
    {
        if (null === ($_value = static::parse_string($value))) {
            return null;
        }

        if ('' === $_value) {
            return null;
        }

        return $_value;
    }


    public static function parse_path(
        $value, array $optional = [],
        array &$pathinfo = null
    ) : ?string
    {
        $pathinfo = null;

        $optional[ 0 ] = $optional[ 'with_pathinfo' ] ?? $optional[ 0 ] ?? false;

        if (null === ($_value = static::parse_astring($value))) {
            return null;
        }

        if (false !== strpos($_value, "\0")) {
            return null;
        }

        $withPathInfoResult = (bool) $optional[ 0 ];

        if ($withPathInfoResult) {
            try {
                $pathinfo = pathinfo($_value);
            }
            catch ( \Throwable $e ) {
                return null;
            }
        }

        return $_value;
    }

    public static function parse_dirpath(
        $value, array $optional = [],
        array &$pathinfo = null
    ) : ?string
    {
        $_value = static::parse_path(
            $value, $optional,
            $pathinfo
        );

        if (null === $_value) {
            return null;
        }

        if (file_exists($_value) && ! is_dir($_value)) {
            return null;
        }

        return $_value;
    }


    public static function parse_filename($value) : ?string
    {
        if (null === ($_value = static::parse_astring($value))) {
            return null;
        }

        $forbidden = [ "\0", "/", "\\", DIRECTORY_SEPARATOR ];

        foreach ( $forbidden as $f ) {
            if (false !== strpos($_value, $f)) {
                return null;
            }
        }

        return $_value;
    }


    public static function parse_regex($regex) : ?string
    {
        if (null === ($_value = static::parse_astring($regex))) {
            return null;
        }

        $before = error_reporting(0);
        $status = @preg_match($regex, '');
        error_reporting($before);

        if (false === $status) {
            return null;
        }

        return $_value;
    }
}
