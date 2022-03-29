<?php
/**
 * Created by PhpStorm.
 * User: Nikolay
 * Date: 23.10.2019
 * Time: 7:40
 */

namespace core;

class Response extends RequestResponse
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAULT = 0;


    public static function set($data = '', $status = 200, $error = false)
    {
        $response = [
            'data'    => [],
            'message' => '',
            'status'  => self::STATUS_FAULT,
        ];

        header('Content-Type: application/json');
        http_response_code($status);

        if (is_array($data) || is_object($data)) {
            $response['data'] = $data;
        } else {
            $response['message'] = $data;
        }

        if ($status >= 200 && $status < 300) {
            $response['status'] = self::STATUS_SUCCESS;
            if (!$response['message']) {
                $response['message'] = 'success';
            }
        }

        if ($error) {
            //TODO write logs
        }

        exit(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    public static function prepare(&$data, $fields = [])
    {
        return $data = self::format($data, $fields);
    }
}