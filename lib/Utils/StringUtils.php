<?php

namespace TopicBank\Utils;


class StringUtils
{

    public static function usortByKey(array &$arr, $key)
    {
        return self::sortByKey('usort', $arr, $key);
    }
    
    
    protected static function sortByKey($sort_function, array &$arr, $key)
    {
        // XXX add Collator to PHP installation
        // $collator = new Collator('en_US');
        $collator = false;

        $sort_function
        (
            $arr,
            function ($a, $b) use ($key, $collator)
            {
                $a = $a[ $key ];
                $b = $b[ $key ];

                if ($a === $b)
                    return 0;
            
                // return $collator->compare($a, $b);
                return ($a < $b ? -1 : 1);
            }
        );
    }
}
