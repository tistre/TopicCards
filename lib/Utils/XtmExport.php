<?php

namespace TopicBank\Utils;


class XtmExport
{
    public static function exportObjects(array $objects)
    {
        $result = 
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<topicMap xmlns="http://www.topicmaps.org/xtm/">' . "\n";
        
        foreach ($objects as $object)
        {
            if ($object instanceOf \TopicBank\Interfaces\iTopic)
            {
                $result .= self::exportTopic($object, 1);
            }
            elseif ($object instanceOf \TopicBank\Interfaces\iAssociation)
            {
                $result .= self::exportAssociation($object, 1);
            }
        }
        
        $result .= "</topicMap>\n";
        
        return $result;
    }
    
    
    protected static function exportTopic(\TopicBank\Interfaces\iTopic $topic, $indent)
    {
        $result = sprintf
        (
            '%s<topic id="%s">' . "\n",
            str_repeat('  ', $indent),
            htmlspecialchars($topic->getId())
        );
        
        $result .= self::exportSubjectIdentifiers($topic->getSubjectIdentifiers(), ($indent + 1));        
        $result .= self::exportSubjectLocators($topic->getSubjectLocators(), ($indent + 1));
        $result .= self::exportTypes($topic->getTypes(), ($indent + 1));
        $result .= self::exportNames($topic->getNames([ ]), ($indent + 1));
        $result .= self::exportOccurrences($topic->getOccurrences([ ]), ($indent + 1));
                    
        $result .= sprintf
        (
            "%s</topic>\n",
            str_repeat('  ', $indent)
        );
        
        return $result;
    }


    protected static function exportAssociation(\TopicBank\Interfaces\iAssociation $association, $indent)
    {
        $result = sprintf
        (
            '%s<association%s>' . "\n",
            str_repeat('  ', $indent),
            self::exportReifier($association->getReifier())
        );

        $result .= self::exportType($association->getType(), ($indent + 1));
        $result .= self::exportScope($association->getScope(), ($indent + 1));
        $result .= self::exportRoles($association->getRoles([ ]), ($indent + 1));

        $result .= sprintf
        (
            "%s</association>\n",
            str_repeat('  ', $indent)
        );
        
        return $result;
    }


    protected static function exportTopicRef($topic_id, $indent)
    {
        return sprintf
        (
            '%s<topicRef href="%s"/>' . "\n", 
            str_repeat('  ', $indent),
            htmlspecialchars($topic_id)
        );
    }


    protected static function exportSubjectLocators(array $subject_locators, $indent)
    {
        return self::exportSubjects('subjectLocator', $subject_locators, $indent);
    }
    

    protected static function exportSubjectIdentifiers(array $subject_identifiers, $indent)
    {
        return self::exportSubjects('subjectIdentifier', $subject_identifiers, $indent);
    }
    

    protected static function exportSubjects($tag, array $urls, $indent)
    {
        $result = '';
        
        foreach ($urls as $url)
        {
            $result .= sprintf
            (
                '%s<%s href="%s"/>' . "\n", 
                str_repeat('  ', $indent),
                $tag,
                htmlspecialchars($url)
            );
        }
            
        return $result;
    }
    
    
    protected static function exportNames(array $names, $indent)
    {
        $result = '';
        
        foreach ($names as $name)
        {
            $result .= sprintf
            (
                "%s<name%s>\n", 
                str_repeat('  ', $indent),
                self::exportReifier($name->getReifier())
            );
            
            $result .= self::exportType($name->getType(), ($indent + 1));
            $result .= self::exportScope($name->getScope(), ($indent + 1));
            
            $result .= sprintf
            (
                "%s<value>%s</value>\n", 
                str_repeat('  ', ($indent + 1)),
                htmlspecialchars($name->getValue())
            );
            
            $result .= sprintf
            (
                "%s</name>\n",
                str_repeat('  ', $indent)
            );
        }
        
        return $result;
    }
    
    
    protected static function exportRoles(array $roles, $indent)
    {
        $result = '';
    
        foreach ($roles as $role)
        {
            $result .= sprintf
            (
                "%s<role%s>\n",
                str_repeat('  ', $indent),
                self::exportReifier($role->getReifier())
            );

            $result .= self::exportType($role->getType(), ($indent + 1));
            $result .= self::exportTopicRef($role->getPlayer(), ($indent + 1));
            
            $result .= sprintf
            (
                "%s</role>\n",
                str_repeat('  ', $indent)
            );
        }
    
        return $result;
    }

    
    protected static function exportOccurrences(array $occurrences, $indent)
    {
        $result = '';
        
        foreach ($occurrences as $occurrence)
        {
            $result .= sprintf
            (
                "%s<occurrence%s>\n", 
                str_repeat('  ', $indent),
                self::exportReifier($occurrence->getReifier())
            );
            
            $result .= self::exportType($occurrence->getType(), ($indent + 1));
            $result .= self::exportScope($occurrence->getScope(), ($indent + 1));
            
            $datatype_topic = $occurrence->getTopicMap()->newTopic();
            $datatype_topic->load($occurrence->getDatatype());
            $datatype_urls = $datatype_topic->getSubjectIdentifiers();

            $result .= sprintf
            (
                '%s<resourceData datatype="%s">%s</resourceData>' . "\n", 
                str_repeat('  ', ($indent + 1)),
                htmlspecialchars($datatype_urls[ 0 ]),
                // XXX support inline XML?
                htmlspecialchars($occurrence->getValue())
            );
            
            $result .= sprintf
            (
                "%s</occurrence>\n",
                str_repeat('  ', $indent)
            );
        }
        
        return $result;
    }
    

    protected static function exportReifier($reifier)
    {
        if (strlen($reifier) === 0)
            return '';
            
        return sprintf(' reifier="%s"', htmlspecialchars($reifier));
    }


    protected static function exportTypes(array $types, $indent)
    {
        if (count($types) === 0)
            return '';

        $result = sprintf("%s<instanceOf>\n", str_repeat('  ', $indent));
            
        foreach ($types as $topic_id)
            $result .= self::exportTopicRef($topic_id, ($indent + 1));
            
        $result .= sprintf("%s</instanceOf>\n", str_repeat('  ', $indent));
        
        return $result;
    }


    protected static function exportType($type, $indent)
    {
        if (strlen($type) === 0)
            return '';
            
        return sprintf
        (
            "%s<type>\n%s%s</type>\n",
            str_repeat('  ', $indent),
            self::exportTopicRef($type, ($indent + 1)),
            str_repeat('  ', $indent)
        );
    }


    protected static function exportScope(array $scope, $indent)
    {
        $result = '';
        
        foreach ($scope as $topic_id)
        {
            $result .= sprintf
            (
                "%s<scope>\n%s%s</scope>\n",
                str_repeat('  ', $indent),
                self::exportTopicRef($topic_id, ($indent + 1)),
                str_repeat('  ', $indent)
            );
        }

        return $result;
    }
}
