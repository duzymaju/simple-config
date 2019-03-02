<?php

namespace SimpleConfig\Tools;

use stdClass;

class CollectionTools
{
    /**
     * Deep merge
     *
     * @param mixed $data  data
     * @param mixed $patch patch
     *
     * @return mixed
     */
    public static function deepMerge($data, $patch)
    {
        if (!self::isCollection($patch) || !self::isCollection($data)) {
            return $patch;
        }
        $arrayData = (array) $data;
        foreach ((array) $patch as $key => $value) {
            if (isset($value)) {
                if (is_array($data) && (int) $key > count($arrayData)) {
                    $nulls = array_fill(0, (int) $key - count($arrayData), null);
                    array_push($arrayData, ...$nulls);
                }
                $arrayData[$key] = array_key_exists($key, $arrayData) ? self::deepMerge($arrayData[$key], $value) :
                    $value;
            } else {
                $arrayData[$key] = null;
            }
        }
        if ($data instanceof stdClass) {
            foreach (array_keys($arrayData) as $key) {
                if (!isset($arrayData[$key])) {
                    unset($arrayData[$key]);
                }
            }
            return (object) $arrayData;
        } elseif (is_array($data)) {
            $index = count($arrayData) - 1;
            while ($index >= 0 && !isset($arrayData[$index])) {
                unset($arrayData[$index]);
                $index--;
            }
        }
        return $arrayData;
    }

    /**
     * Is collection
     *
     * @param mixed $value value
     *
     * @return bool
     */
    private static function isCollection($value)
    {
        return is_array($value) || $value instanceof stdClass;
    }
}
