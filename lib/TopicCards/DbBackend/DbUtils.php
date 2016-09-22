<?php

namespace TopicCards\DbBackend;

use GraphAware\Neo4j\Client\ClientBuilder;


class DbUtils
{
    protected $transaction_level = 0;
    protected $services;
    
    
    public function __construct(\TopicCards\iServices $services)
    {
        $this->services = $services;
    }
    
    
    public function connect()
    {
        if ($this->services->db !== false)
        {
            return 0;
        }
        
        $db_params = $this->services->getDbParams();

        $builder = ClientBuilder::create();

        foreach ($db_params[ 'connections' ] as $key => $url)
        {
            $builder->addConnection($key, $url);
        }

        // XXX ugly to use an interface, but then directly access a
        // property that is not part of the interface!! ->db
        $this->services->db = $builder->build();
        
        return 1;
    }


    public function labelsString(array $labels)
    {
        $result = '';
        
        foreach ($labels as $label)
        {
            $result .= sprintf(':`%s`', $label);
        }
        
        return $result;
    }


    public function propertiesString(array $properties, &$bind)
    {
        $property_strings = [ ];
        
        foreach ($properties as $key => $value)
        {
            if (empty($value))
            {
                continue;
            }

            if (is_array($value))
            {
                $parts = [ ];

                foreach ($value as $i => $v)
                {
                    $k = $key  . $i;
                    $parts[ ] = sprintf('{%s}', $k);
                    $bind[ $k ] = $v;
                }

                $property_strings[ ] = sprintf('%s: [ %s ]', $key, implode(', ', $parts));
            }
            else
            {
                $property_strings[ ] = sprintf('%s: {%s}', $key, $key);
                $bind[ $key ] = $value;
            }
        }

        return implode(', ', $property_strings);
    }


    public function propertiesUpdateString($node, array $properties, &$bind)
    {
        $set_property_strings = [ ];
        $remove_property_strings = [ ];

        foreach ($properties as $key => $value)
        {
            if ((is_array($value) && (count($value) === 0)) || ((! is_array($value)) && (strlen($value) === 0)))
            {
                $remove_property_strings[ ] = sprintf('%s.%s', $node, $key);
                continue;
            }

            if (is_array($value))
            {
                $parts = [ ];

                foreach ($value as $i => $v)
                {
                    $k = $key  . $i;
                    $parts[ ] = sprintf('{%s}', $k);
                    $bind[ $k ] = $v;
                }

                $set_property_strings[ ] = sprintf('%s.%s = [ %s ]', $node, $key, implode(', ', $parts));
            }
            else
            {
                $set_property_strings[ ] = sprintf('%s.%s = {%s}', $node, $key, $key);
                $bind[ $key ] = $value;
            }
        }

        $result = '';
        
        if (count($remove_property_strings) > 0)
        {
            $result .= sprintf(' REMOVE %s', implode(', ', $remove_property_strings));
        }

        if (count($set_property_strings) > 0)
        {
            $result .= sprintf(' SET %s', implode(', ', $set_property_strings));
        }

        return $result;
    }


    // OLD; PROBABLY GOING AWAY SOON
    public function datetimeToDb($date)
    {
        // "2004-02-12T15:19:21+00:00" => "2004-02-12 15:19:21"
        // XXX hacked
        
        return str_replace('T', ' ', substr($date, 0, 19));
    }
}
