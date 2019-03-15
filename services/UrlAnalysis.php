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
        //split url into array
        $arrUrl= explode('/', $_SERVER['REQUEST_URI']);
        // take the last element of the array
        $this->lastElemUrl = array_pop($arrUrl);
    }

    // define in the url in the rest of the (that is, after the last /) index of the entity over which actions will be taken
    // (delete or edit)
    /**
     * @return bool|self
     */
    public function defineIndexinUrl()
    {
        // if the element of a line is an integer positive, and does not start with 0, then return it, else return false
        // (because the indexes in the database are not 0 or 01 or 0001)
        if (preg_match("/^[1-9]\d*$/", $this->lastElemUrl)) {
            return $this->lastElemUrl;
        } else {
            return false;
        }
    }

     // output the last part (that is, after the last "/") url (without checking it is either a number or a text, for example
    // will be used to determine the name of the tag)
    /**
     * @return self
     */
    public function returnLastElementInUrl(): self
    {
        return $this->lastElemUrl;
    }
}
