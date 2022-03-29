<?php

namespace controllers;

use core\Response;

class Notifications extends \core\baseController
{
    public function __construct($language = false)
    {
        parent::__construct($language);
    }

    public function send()
    {
        $notification_data = $this->request->filter(
            [
                'user_id',
                'title',
                'message'
            ]
        );

        $result = $this->_send_data($notification_data);

        return $result;
    }

    public function mass_send()
    {
        $result = [];

        $notification_data = $this->request->filter(
            [
                'user_ids',
                'title',
                'message'
            ]
        );

        $user_ids = explode(',', $notification_data['user_ids']);

        foreach ($user_ids as $user_id) {
            $tokens = $this->model->getDataSingleCon($user_id, 'user_id', 'tokens');

            foreach ($tokens as $token) {
                $id = $this->model->insertReturnLastId([
                    'user_id'     => $user_id,
                    'title'       => $notification_data['title'],
                    'message'     => $notification_data['message'],
                    'token'       => $token->token,
                    'device_type' => $token->device_type,
                    'status'      => PUSH_STATUS_IN_PROGRESS
                ], 'mass_notifications');

                array_push($result, $id);
            }
        }

        return $result;
    }

    public function get()
    {
        $user_id              = $this->request->get('user_id');
        $result               = [];
        $count_of_in_progress = 0;
        $count_of_sent        = 0;
        $count_of_failed      = 0;

        $notifications = $this->model->getDataSingleCon($user_id, 'user_id', 'mass_notifications');

        foreach ($notifications as $notification) {
            switch ($notification->status) {
                case PUSH_STATUS_IN_PROGRESS:
                    $count_of_in_progress++;
                    break;
                case PUSH_STATUS_SENT:
                    $count_of_sent++;
                    break;
                case PUSH_STATUS_FAILED:
                    $count_of_failed++;
                    break;
            }
        }

        $result['notifications']        = $notifications;
        $result['count_of_in_progress'] = $count_of_in_progress;
        $result['count_of_sent']        = $count_of_sent;
        $result['count_of_failed']      = $count_of_failed;

        return $result;
    }

    public function cron()
    {
        $result        = [];
        $notifications = $this
            ->model
            ->getDataSingleCon(PUSH_STATUS_IN_PROGRESS, 'status', 'mass_notifications');

        foreach ($notifications as $notification) {
            switch ($notification->device_type) {
                case DEVICE_TYPE_ANDROID:
                    $sending_result = $this->_send_data_android(
                        $notification->token,
                        $notification->title,
                        $notification->message
                    );

                    if ($sending_result) {
                        $this
                            ->model
                            ->updateData(
                                ['status' => PUSH_STATUS_SENT],
                                ['id' => $notification->id],
                                'mass_notifications'
                            );
                        array_push($result, 'Android true');
                    } else {
                        $this
                            ->model
                            ->updateData(
                                ['status' => PUSH_STATUS_FAILED],
                                ['id' => $notification->id],
                                'mass_notifications'
                            );
                        array_push($result, 'Android false');
                    }
                    break;
                case DEVICE_TYPE_IOS:
                    $sending_result = $this->_send_data_ios(
                        $notification->token,
                        $notification->title,
                        $notification->message
                    );
                    if ($sending_result) {
                        $this
                            ->model
                            ->updateData(
                                ['status' => PUSH_STATUS_SENT],
                                ['id' => $notification->id],
                                'mass_notifications'
                            );
                        array_push($result, 'IOs true');
                    } else {
                        $this
                            ->model
                            ->updateData(
                                ['status' => PUSH_STATUS_FAILED],
                                ['id' => $notification->id],
                                'mass_notifications'
                            );
                        array_push($result, 'IOs false');
                    }
                    break;
            }
        }

        return $result;
    }

    private function _send_data($notification_data)
    {
        $result = [];
        $tokens = $this->model->getDataSingleCon($notification_data['user_id'], 'user_id', 'tokens');

        foreach ($tokens as $token) {
            switch ($token->device_type) {
                case DEVICE_TYPE_ANDROID:
                    $sending_result = $this->_send_data_android($token->token, $notification_data['title'], $notification_data['message']);
                    if ($sending_result) {
                        array_push($result, 'Android true');
                    } else {
                        array_push($result, 'Android false');
                    }
                    break;
                case DEVICE_TYPE_IOS:
                    $sending_result = $this->_send_data_ios($token->token, $notification_data['title'], $notification_data['message']);
                    if ($sending_result) {
                        array_push($result, 'IOs true');
                    } else {
                        array_push($result, 'IOs false');
                    }
                    break;
            }
        }

        return $result;
    }

    private function _send_data_android($token, $title, $message)
    {
        $SERVER_KEY          = 'Some server key';
        $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';

        $fields = [
            'to'   => $token,
            'data' => ['title' => $title, 'body' => $message] //
        ];

        $headers = [
            'Authorization:key=' . $SERVER_KEY,
            'Content-Type:application/json'
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    private function _send_data_ios($token, $title, $message)
    {
        try {
            $gateway = 'Some gateway';
            $ctx     = stream_context_create();

            stream_context_set_option($ctx, 'ssl', 'local_cert', 'Some sert');
            stream_context_set_option($ctx, 'ssl', 'passphrase', 'Some passphrase');

            $fp = stream_socket_client($gateway, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

            if (!$fp) {
                $result = ("Failed to connect: $err $errstr" . PHP_EOL);
            } else {
                $body['aps'] = [
                    'alert'   => $title,
                    'details' => $message,
                ];
                $payload     = json_encode($body);
                $msg         = chr(0) . pack('n', 32) . pack('H*', $token) . pack('n', strlen($payload)) . $payload;
                $result      = fwrite($fp, $msg, strlen($msg));

                fclose($fp);
            }
        } catch (\Exception $exception) {
            $result = $exception->getMessage();
        }

        return $result;
    }
}