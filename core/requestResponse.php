<?php
/**
 * Created by PhpStorm.
 * User: Nikolay
 * Date: 26.10.2019
 * Time: 9:00
 */

namespace core;

class RequestResponse
{

    protected static function format($data, $fields = [])
    {
        $result = [];
        $data   = (array)$data;

        if (!$fields) {
            return $data;
        }

        if (self::getDepth($data) == 0) {
            foreach ($data as $key => $item) {
                if (in_array($key, $fields)) {
                    $result[$key] = $item;
                }
            }
        }

        if (self::getDepth($data) == 1) {
            foreach ($data as $key => $items) {
                $items = (array)$items;
                foreach ($items as $field => $value) {
                    if (in_array($field, $fields)) {
                        $result[$key][$field] = $value;
                    }
                }
            }
        }

        return $data = $result;
    }

    private static function getDepth(array $arr, $depth = 0): Int
    {
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr));

        foreach ($it as $tmp) {
            $int = $it->getDepth();
            $depth >= $int ?: $depth = $int;
        }

        return $depth;
    }

}