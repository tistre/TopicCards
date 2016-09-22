<?php

namespace TopicCards\Utils;


class StringUtils
{

    public static function usortByKey(array &$arr, $key)
    {
        return self::sortByKey('usort', $arr, $key);
    }
    
    
    protected static function sortByKey($sort_function, array &$arr, $key)
    {
        $collator = new \Collator('en_US');

        $sort_function
        (
            $arr,
            function ($a, $b) use ($key, $collator)
            {
                $a = $a[ $key ];
                $b = $b[ $key ];

                if ($a === $b)
                    return 0;
 
                if (is_int($a) && is_int($b))
                    return ($a < $b ? -1 : 1);

                return $collator->compare($a, $b);
            }
        );
    }
}
