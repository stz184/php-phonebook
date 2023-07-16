<?php
/**
 * Created by PhpStorm.
 * User: dev1
 * Date: 3.6.2015 г.
 * Time: 21:21 ч.
 */

namespace stz184\notifications;


use stz184\session\Session;

class Alerter {
    protected $messageTypes = ['success', 'info', 'warning', 'danger'];

    public function getMessages()
    {
        $output = [];
        foreach ($this->messageTypes as $type) {
            if (Session::flashDataExists($type)) {
                $html =
                    '<div class="alert alert-'.$type.'" role="alert">'
                    . Session::getFlashData($type)
                    . '</div>';
                $output[] = $html;
            }
        }
        return implode("", $output);
    }


    public function __toString()
    {
        return $this->getMessages();
    }
}