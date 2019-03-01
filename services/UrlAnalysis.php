<?php
/**
 * Created by PhpStorm.
 * User: nima
 * Date: 05.01.19
 * Time: 14:28
 */

namespace Services;

/**
 * Class UrlAnalysis
 * @package Services
 */
class UrlAnalysis
{
    /**
     * UrlAnalysis constructor.
     */
    public function __construct()
    {
        //розбиваєм url на масив
        $arrUrl= explode('/', $_SERVER['REQUEST_URI']);
        // берем оствнній елемент масива
        $this->lastElemUrl = array_pop($arrUrl);
    }

    //визначаємо в url в остальній частині( тещо після остльного /) індекс сучності над якою будуть робиться дії
    // (видалення або редагування)
    /**
     * @return bool|self
     */
    public function defineIndexinUrl()
    {
        //якщо елемент строки є цілим числом позитивним, і не начинаєтться з 0, то вертаєм його, інакше вертаєм false
        // (адже індекси в базі не бувають 0 або 01 чи 0001)
        if (preg_match("/^[1-9]\d*$/", $this->lastElemUrl)) {
            return $this->lastElemUrl;
        } else {
            return false;
        }
    }

    //вивід останньої частини(те що після останнього "/") url(без перевірки її чи це число чи текст, наприклад
    // буде використовуватись для визначення імені тегу)
    /**
     * @return self
     */
    public function returnLastElementInUrl(): self
    {
        return $this->lastElemUrl;
    }
}
