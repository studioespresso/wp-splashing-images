<?php

namespace Crew\Unsplash;

/**
 * Class Search
 * @package Crew\Unsplash
 */
class Search extends Endpoint
{
    /**
     * Retrieve all the photos on a specific page depending on search results
     * Returns ArrayObject that contain Photo object.
     *
     * @param  string $search Retrieve photos matching the search term
     * @param  integer $page Page from which the photos need to be retrieve
     * @return ArrayObject of Photos
     */
    public static function photos($search, $page= 1)
    {
        $photos = self::get("search/photos", ['query' => ['query' => $search, 'page' => $page]]);

        $photosArray = self::getArray($photos->getBody(), get_called_class());

        return new ArrayObject($photosArray, $photos->getHeaders());
    }
}