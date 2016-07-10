<?php

class Console
{
    const CL_BLACK = 'black';
    const CL_DARK_GRAY = 'dark_gray';
    const CL_BLUE = 'blue';
    const CL_LIGHT_BLUE = 'light_blue';
    const CL_GREEN = 'green';
    const CL_LIGHT_GREEN = 'light_green';
    const CL_CYAN = 'cyan';
    const CL_LIGHT_CYAN = 'light_cyan';
    const CL_RED = 'red';
    const CL_LIGHT_RED= 'light_red';
    const CL_PURPLE = 'purple';
    const CL_LIGHT_PURPLE = 'light_purple';
    const CL_BROWN = 'brown';
    const CL_YELLOW = 'yellow';
    const CL_LIGHT_GRAY = 'light_gray';
    const CL_WHITE = 'white';

    const BG_BLACK = 'bg_black';
    const BG_RED = 'bg_red';
    const BG_GREEN = 'bg_green';
    const BG_YELLOW = 'bg_yellow';
    const BG_BLUE = 'bg_blue';
    const BG_MAGENTA = 'bg_magenta';
    const BG_CYAN = 'bg_cyan';
    const BG_LIGHT_GRAY = 'bg_light_gray';

    private $textColors = [
        'black' => '0;30',
        'dark_gray' => '1;30',
        'blue' => '0;34',
        'light_blue' => '1;34',
        'green' => '0;32',
        'light_green' => '1;32',
        'cyan' => '0;36',
        'light_cyan' => '1;36',
        'red' => '0;31',
        'light_red' => '1;31',
        'purple' => '0;35',
        'light_purple' => '1;35',
        'brown' => '0;33',
        'yellow' => '1;33',
        'light_gray' => '0;37',
        'white' => '1;37',
    ];

    private $bgColors = [
        'bg_black' => '40',
        'bg_red' => '41',
        'bg_green' => '42',
        'bg_yellow' => '43',
        'bg_blue' => '44',
        'bg_magenta' => '45',
        'bg_cyan' => '46',
        'bg_light_gray' => '47',
    ];

    const DEF_COLOR = self::CL_LIGHT_GRAY;

    const DEF_BG_COLOR = self::BG_BLACK;
    
    private $color = self::DEF_COLOR;

    private $bgColor = self::DEF_BG_COLOR;

    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    public function setBgColor($bgColor)
    {
        $this->bgColor = $bgColor;
        return $this;
    }

    public function colored($text)
    {
        return "\e[" . $this->textColors[$this->color] . "m" . "\e[" . $this->bgColors[$this->bgColor] . "m" . $text . "\e[0m";
    }

    public function colorizeTaggedText($text)
    {
        $arrPattern = [
            '/<br(\/)?>/',
            '/<\/[a-zA-Z_]+>/',
        ];
        foreach ($this->textColors as $key => $val) {
            $arrPattern[] = '/<' . $key . '>/';
        }
        foreach ($this->bgColors as $key => $val) {
            $arrPattern[] = '/<' . $key . '>/';
        }

        $arrReplacement = [
            "\n",
            "\e[0m",
        ];
        foreach ($this->textColors as $key => $val) {
            $arrReplacement[] = "\e[" . $val . "m";
        }
        foreach ($this->bgColors as $key => $val) {
            $arrReplacement[] = "\e[" . $val . "m";
        }

        return preg_replace($arrPattern, $arrReplacement, $text);
    }
}