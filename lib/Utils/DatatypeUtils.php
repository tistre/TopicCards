<?php

namespace TopicBank\Utils;


class DatatypeUtils
{
    public static function valueToXml($value, $datatype)
    {
        if (self::isXhtml($datatype))
        {
            return sprintf
            (
                '<div xmlns="http://www.w3.org/1999/xhtml">%s</div>',
                $value
            );
        }
        elseif (self::isXml($datatype))
        {
            return $value;
        }
        else
        {
            return htmlspecialchars($value);
        }
    }


    public static function getValueFromDomNode(\DOMElement $context_node, $datatype)
    {
        if (self::isXhtml($datatype))
        {
            // XHTML content is wrapped in a <div>
            
            $xhtml = '';
            
            foreach ($context_node->childNodes as $node)
            {
                if ($node->nodeType != XML_ELEMENT_NODE)
                    continue;
                
                if ($node->tagName !== 'div')
                    continue;
                    
                $xhtml = $node->ownerDocument->saveXML($node);

                // Hack: saveXML() keeps the surrounding <div> tag, remove it
            
                $start  = strpos($xhtml, '>') + 1;
                $length = strrpos($xhtml, '<') - $start;

                $xhtml = substr($xhtml, $start, $length);
            }
            
            return $xhtml;
        }
        elseif (self::isXml($datatype))
        {
            $xml = $context_node->ownerDocument->saveXML($context_node);

            // Hack: saveXML() keeps the surrounding tag, remove it
        
            $start  = strpos($xml, '>') + 1;
            $length = strrpos($xml, '<') - $start;

            $xml = substr($xml, $start, $length);

            return $xml;
        }
        else
        {
            return $context_node->nodeValue;
        }
    }
    

    public static function isXhtml($datatype)
    {
        return ($datatype === 'http://www.w3.org/1999/xhtml');
    }
    
    
    public static function isXml($datatype)
    {
        // XXX What are the datatype URIs for application/xml, something+xml etc.?
        
        $xml_datatypes = [ 'http://www.w3.org/2001/XMLSchema#anyType' ];
        
        return in_array($datatype, $xml_datatypes);
    }
}
