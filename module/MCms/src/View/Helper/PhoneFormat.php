<?php
namespace MCms\View\Helper;

use Zend\View\Helper\AbstractHelper;

class PhoneFormat extends AbstractHelper
{
    /**
     * Функция возвращает номер телефона в формате "+0 (000) 000-00-00"
     * @param  $phone string Число на основе которого нужно сформировать окончание
     * @param  $clear bool Если true вернет номер телефона без пробелов и скобочек
     *
     * @return String|null
     */
    public function __invoke($phone, $clear = false)
    {
        if ($clear) {
            $result = preg_replace('/[^0-9]/', '', $phone);
            $result = (substr($result, 0, 1) == 8) ? '+7' . substr($result, 1) : '+' . $result;
        } else {
            if (preg_match('/^(\d)(\d{3})(\d{3})(\d{2})(\d{2})$/', preg_replace('/[^0-9]/', '', $phone), $matches)) {
                $result = '+' . ((substr($matches[1], 0, 1) == 8) ? '7' . substr($matches[1], 1) : $matches[1])
                    . ' (' . $matches[2] . ') ' . $matches[3] . '-' . $matches[4] . '-' . $matches[5];
            } else {
                $result = $phone;
            }
        }

        return $result;
    }
}