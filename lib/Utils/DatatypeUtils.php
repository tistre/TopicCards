<?php

namespace TopicBank\Utils;


class DatatypeUtils
{
    public static function validate(&$value, $datatype, &$error_msg)
    {
        $error_msg = '';
        
        if (self::isXhtml($datatype))
        {
            return self::validateXhtml($value, $datatype, $error_msg);
        }
        elseif (self::isXml($datatype))
        {
            return self::validateXml($value, $datatype, $error_msg);
        }
        
        return 1;
    }
    
    
    protected static function validateXhtml(&$value, $datatype, &$error_msg)
    {
        // XHTML value can be "hello <i>world</i>", need to wrap it in a div
        // to validate
        
        $xml_value = self::valueToXml($value, $datatype);
        
        return self::validateXml($xml_value, $datatype, $error_msg);
    }
    
    
    protected static function validateXml(&$value, $datatype, &$error_msg)
    {
        libxml_use_internal_errors(true);
    
        $dom = new \DOMDocument;
        $dom->loadXML($value);
        
        $errors = libxml_get_errors();

        libxml_clear_errors();
        libxml_use_internal_errors(false);
        
        if (count($errors) === 0)
            return 1;
            
        $msgs = [ ];
        
        foreach ($errors as $error)
        {
            $msgs[ ] = sprintf
            (
                'XML error %s: %s, line %s, column %s', 
                $error->code,
                trim(strtr($error->message, "\n\r", '  ')),
                $error->line,
                $error->column
            );
        }

        $error_msg = implode('. ', $msgs);
    
        return -1;
    }
    
    
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