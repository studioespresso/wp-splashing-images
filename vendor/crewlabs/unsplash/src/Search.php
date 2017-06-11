<?php

namespace Crew\Unsplash;

class Search extends SearchEndpoint
{
    /**
     * Retrieve all the photos on a specific page depending on search results
     * Returns ArrayObject that contains Photo objects.
     *
     * @param  string $search Retrieve photos matching the search term
     * @param  integer $page Page from which the photos need to be retrieve
     * @return ArrayObject of Photos
     */
    public static function photos($search, $page = 1)
    {
        $photos = self::get(
            "search/photos", [
                'query' => [
                    'query' => $search,
                    'page' => $page
                ]
            ]
        );
	    $photosArray = json_decode($photos->getBody()->getContents());
	    return $photosArray;



    }

}