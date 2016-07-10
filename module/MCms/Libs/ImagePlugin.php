<?php

class ImagePlugin
{
    /**
     * @param string $fName
     * @param string $fNewName
     * @param integer $size
     * @param bool $maxIsHeight
     *
     * @return bool
     */
    public function imgResize($fName, $fNewName, $size, $maxIsHeight = false)
    {
        list($iWidth, $iHeight, $type) = getimagesize($fName); // Получаем размеры и тип изображения (число)
        $types = array("", "gif", "jpeg", "png"); // Массив с типами изображений
        $ext = $types[$type]; // Зная "числовой" тип изображения, узнаём название типа

        if ($ext) {
            $func = 'imagecreatefrom' . $ext;
            $imgOrig = $func($fName); // Создаём дескриптор для работы с исходным изображением
        } else {
            echo 'Некорректное изображение'; // Выводим ошибку, если формат изображения недопустимый
            return false;
        }

        $woh = (!$maxIsHeight) ? $iWidth : $iHeight;

        if ($woh <= $size) {
            $aw = $iWidth;
            $ah = $iHeight;
        } else {
            if (!$maxIsHeight) {
                $aw = $size;
                $ah = $size * $iHeight / $iWidth;
            } else {
                $aw = $size * $iWidth / $iHeight;
                $ah = $size;
            }
        }
        $imgNew = imagecreatetruecolor($aw, $ah);
        imagecopyresampled($imgNew, $imgOrig, 0, 0, 0, 0, $aw, $ah, $iWidth, $iHeight);
        $func = 'image' . $ext;
        return $func($imgNew, $fNewName);
    }

    /**
     * @param string $fName
     * @param string $fNewName
     * @param integer $size
     *
     * @return bool
     */
    public function imgCropResize($fName, $fNewName, $size)
    {
        list($iWidth, $iHeight, $type) = getimagesize($fName); // Получаем размеры и тип изображения (число)
        $types = array("", "gif", "jpeg", "png"); // Массив с типами изображений
        $ext = $types[$type]; // Зная "числовой" тип изображения, узнаём название типа

        if ($ext) {
            $func = 'imagecreatefrom' . $ext; // Получаем название функции, соответствующую типу, для создания изображения
            $imgOrig = $func($fName); // Создаём дескриптор для работы с исходным изображением
        } else {
            echo 'Некорректное изображение'; // Выводим ошибку, если формат изображения недопустимый
            return false;
        }
        $diff = abs($iWidth - $iHeight);
        if ($diff > 0) {
            $xDiff = 0;
            $yDiff = $diff / 2;
            if ($iWidth > $iHeight) {
                $xDiff = $diff / 2;
                $yDiff = 0;
            }
            $imgNew = imagecreatetruecolor($iWidth - $xDiff * 2, $iHeight - $yDiff * 2); // Создаём дескриптор для выходного изображения
            imagecopy($imgNew, $imgOrig, 0, 0, $xDiff, $yDiff, $iWidth - $xDiff, $iHeight - $yDiff); // Переносим часть изображения из исходного в выходное
            $iWidth = $iWidth - $xDiff * 2;
            $iHeight = $iHeight - $yDiff * 2;
            $imgOrig = $imgNew;
        }

        $imgNew = imagecreatetruecolor($size, $size);
        imagecopyresampled($imgNew, $imgOrig, 0, 0, 0, 0, $size, $size, $iWidth, $iHeight);

        $func = 'image' . $ext; // Получаем функция для сохранения результата
        return $func($imgNew, $fNewName); // Сохраняем изображение в тот же файл, что и исходное, возвращая результат этой операции
    }
}