<?php

namespace TopicCards\Utils;


class XtmReader implements \Iterator
{
    protected $topicmap;
    protected $filename;
    protected $xmlreader;
    protected $importer;
    protected $cnt;
    
    
    public function __construct($filename, \TopicCards\iTopicMap $topicmap)
    {
        $this->filename = $filename;
        $this->topicmap = $topicmap;
        
        $this->xmlreader = new \XMLReader();
        $this->importer = new XtmImport($topicmap);
        
        $this->cnt = -1;
    }


    public function rewind()
    {
        $ok = $this->xmlreader->open($this->filename);

        // Go to the root node

        if ($ok >= 0)
            $ok = $this->xmlreader->read();

        if (! $ok)
            return;
            
        // Go to the first child node

        while (true)
        {
            $ok = $this->xmlreader->read();
            
            if (! $ok)
                return;
    
            if ($this->xmlreader->nodeType === \XMLReader::ELEMENT)
            {
                $this->cnt = 0;
                return;
            }
        }
    }
    
    
    public function current()
    {
        $node = $this->xmlreader->expand();
        
        if ($node === false)
            return false;
        
        if ($node->tagName === 'topic')
        {
            return $this->importer->importTopic($node);
        }
        elseif ($node->tagName === 'association')
        {
            return $this->importer->importAssociation($node);
        }
        else
        {
            return false;
        }
    }
    
    
    public function key()
    {
        return $this->cnt;
    }
    
    
    public function next()
    {
        while (true)
        {
            $ok = $this->xmlreader->next();
            
            if (! $ok)
            {
                $this->cnt = -1;
                return;
            }
    
            if ($this->xmlreader->nodeType === \XMLReader::ELEMENT)
            {
                $this->cnt++;
                return;
            }
        }
        
        $this->cnt = -1;
    }
    
    
    public function valid()
    {
        return ($this->cnt >= 0);
    }
}
