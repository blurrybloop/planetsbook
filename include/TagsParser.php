<?php

class TagsParser{

    public $text;
    private $matches;
    private $tags = [
            'i'         =>      ['i', 'i'], 
            'u'         =>      ['u', 'u'], 
            'b'         =>      ['b', 'b'], 
            'p'         =>      ['p', 'p'], 
            'left'      =>      ['p style="text-align: left"', 'p'],
            'center'    =>      ['p style="text-align: center"', 'p'],
            'right'     =>      ['p style="text-align: right"', 'p'],
            'justify'   =>      ['p style="text-align: justify"', 'p']
            ];

    private $alt;

    function __construct($text = ''){
        $this->text = $text;
        $this->matches = new SplStack();
        foreach ($this->tags as $k => $v)
            $this->alt .= $k . '|';
    }

    function parse(){
        $pos = 0;
        $this->matches->push([['', 0]]);
        while (true){
           

            $current = $this->matches->top();
            if (!$current[0][0]) $this->matches->pop();
            $regex = '#(?<=\[)(?:' . $this->alt;
            if ($current[0][0]) $regex .= '|\/' . $current[0][0];
            $regex .= ')(?=\])#';

            if (!preg_match($regex, $this->text, $match, PREG_OFFSET_CAPTURE, $pos)) {
                if ($this->matches->isEmpty()) break;
                $this->matches->pop(); 
                if ($this->matches->isEmpty()) break;
                continue; 
            }
            
            $pos = strlen($match[0][0]) + $match[0][1] + 1;
            if ($match[0][0][0] == '/') {
                $this->matches->pop();
                if ($this->matches->isEmpty()) $this->matches->push([['', 0]]);
                $tag = $this->tags[$current[0][0]];
                $this->text = substr_replace($this->text, '<' . $tag[0] . '>', $current[0][1]-1, strlen($current[0][0]) + 2);
                $match[0][1] += abs(strlen($current[0][0]) - strlen($tag[0]));
                $this->text = substr_replace($this->text, '</' . $tag[1] . '>', $match[0][1]-1, strlen($match[0][0]) + 2);
                $pos = $match[0][1] + strlen($tag[1]) + 2;
            }
            else $this->matches->push($match);
        }
        return $this->text;
    }
}