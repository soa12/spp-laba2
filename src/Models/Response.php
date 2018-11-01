<?php
/**
 * Created by PhpStorm.
 * User: sabyrzhan
 * Date: 01.11.18
 * Time: 23:48
 */

namespace ImageUploadingService\Models;

class Response
{

    public function setResponse($status, $resp)
    {
        $response = array(
            'status' => $status,
            'response' => $resp
        );
        return $response;
    }

    public function checkDocResponse($date, $status)
    {
        $okResp = sprintf('Данный документ был заверен %s ', $date);
        $ethResponse = 'в Ethereum сервисе';
        $btcResponse = 'в Bitcoin сервисе';
        $badResp = ', но что-то пошло не так, и его нет в блокчейне';
        foreach ($status as $st) {
            if ($st == 'eth') {
                $ethResp = $ethResponse;
            }
            if ($st == 'btc') {
                $btcResp = $btcResponse;
            }
        }

        $answer = ($ethResp && $btcResp) ? $okResp . $ethResp . " и " . $btcResp : $okResp . $ethResp . $btcResp;
        if (!$ethResp && !$btcResp) {
            $answer = $okResp . $badResp;
            return $this->setResponse('400', $answer);
        }
        return $this->setResponse('200', $answer);
    }
}