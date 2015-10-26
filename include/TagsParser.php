<?php

class TagsParser{

    public $text;
    private $matches;
    private $regex = '#\[(b|i|u|s|sub|sup|hr|align|url|img|figure|figcaption|h1|color|list|\*|\d+)(?:=([\'"\w\.:\/\\\\&\?\#\-\+\=\*]*))?(?:\swidth=(\d+))?(?:\sheight=(\d+))?\]';

    function __construct($text = ''){
        $this->text = $text;
        $this->matches = new SplStack();
    }

    function parse(){
        $pos = 0;
        $this->matches->push([['', -1], ['', -1]]);
        while (true){
           
            $regex = $this->regex;
            $current = $this->matches->top();
            if (!$current[1][0]) $this->matches->pop();
            if ($current[1][0]) $regex .= '|\[/' . ($current[1][0] == '*' ? '\\' . $current[1][0] : $current[1][0]) . '\]';
            $regex .= '#';

            if (!preg_match($regex, $this->text, $match, PREG_OFFSET_CAPTURE, $pos)) {
                if ($this->matches->isEmpty()) break;
                $this->matches->pop(); 
                if ($this->matches->isEmpty()) break;
                continue; 
            }
            
            $pos = strlen($match[0][0]) + $match[0][1];
            if ($match[0][0][1] == '/') {
                $this->matches->pop();
                if ($this->matches->isEmpty()) $this->matches->push([['', -1], ['', -1]]);
                $openTag; $closeTag;
                if (is_numeric($current[1][0])){
                    $openTag = 'li value=' . $current[1][0];
                    $closeTag = 'li';
                }
                else{
                    switch ($current[1][0]){
                        case 'b':
                        case 'i':
                        case 'u':
                        case 's':
                        case 'h1':
                        case 'sub':
                        case 'sup':
                            $openTag = $closeTag = $current[1][0];
                            break;
                        case 'hr':
                            $openTag = 'hr/';
                            $closeTag = '';
                            break;
                        case 'color':
                            $openTag = 'span' . ((!empty($current[2][0])) ? ' style="' . 'color:' . $current[2][0] . '"' : '');
                            $closeTag = 'span';
                            break;
                        case 'list':
                            if (empty($current[2][0]) || ($current[2][0] != 'A' && $current[2][0] != 'a' && $current[2][0] != 'i' && $current[2][0] != 'I' && $current[2][0] != '1'))
                                $openTag = $closeTag = 'ul';
                            else{
                                $openTag = 'ol type=' . $current[2][0];
                                $closeTag = 'ol';
                            }
                            break;
                        case '*':
                            $openTag = $closeTag = 'li';
                            break;
                        case 'align':
                            $openTag = 'div' . ((!empty($current[2][0]) && ($current[2][0] == 'left' || $current[2][0] == 'right' || $current[2][0] == 'center' || $current[2][0] == 'justify')) ? ' style="' . 'text-align:' . $current[2][0] . '"' : '');
                            $closeTag = 'div';
                            break;
                        case 'url':
                            $openTag = 'a href=' . $this->makeAbsoluteUri((!empty($current[2][0]) ? $current[2][0] : substr($this->text, $current[0][1] + strlen($current[0][0]), $match[0][1] - $current[0][1] - strlen($current[0][0]))));
                            $closeTag = 'a';
                            break;
                        case 'img':
                            $openTag = 'img src="' . substr($this->text, $current[0][1] + strlen($current[0][0]), $match[0][1] - $current[0][1] - strlen($current[0][0])) . '" style="width: 100%; height: 100%"/';
                            $this->text = substr_replace($this->text, '', $current[0][1] + strlen($current[0][0]), $match[0][1] - $current[0][1] - strlen($current[0][0]));
                            $match[0][1] -= $match[0][1] - $current[0][1] - strlen($current[0][0]);
                            $closeTag = '';
                            break;
                        case 'figure':
                            $openTag = 'figure style="';
                            if (!empty($current[2][0]) && ($current[2][0] == 'left' || $current[2][0] == 'right' || $current[2][0] == 'center')) $openTag .= ' text-align:' . $current[2][0] . ';';
                            else if (!empty($current[2][0]) && $current[2][0] == 'float-left') $openTag .= 'float:left;';
                            else if (!empty($current[2][0]) && $current[2][0] == 'float-right') $openTag .= 'float:right;';
                            if (!empty($current[3][0])) $openTag .= 'width:' . $current[3][0] .'px;';
                            if (!empty($current[4][0])) $openTag .= 'height: ' . $current[4][0] .'px;';
                            $openTag .= '"';
                            $closeTag = 'figure';
                            break;
                        case 'figcaption':
                            $openTag = 'figcaption' . ((!empty($current[2][0]) && ($current[2][0] == 'left' || $current[2][0] == 'right' || $current[2][0] == 'center' || $current[2][0] == 'justify')) ? ' style="' . 'text-align:' . $current[2][0] . '"' : '');
                            $closeTag = 'figcaption';
                            break;
                    }
                }
                $this->text = substr_replace($this->text, '<' . $openTag . '>', $current[0][1], strlen($current[0][0]));
                $match[0][1] += strlen($openTag) - strlen($current[0][0]) + 2;
                $this->text = substr_replace($this->text, $closeTag ? '</' . $closeTag . '>' : '', $match[0][1], strlen($match[0][0]));
                $pos = $match[0][1] + strlen($closeTag) + ($closeTag ? 3 : 0);
            }
            else $this->matches->push($match);
        }
        return $this->text;
    }

    private function makeAbsoluteUri($href){
        return preg_replace_callback('#(mailto\:|(?:news|(?:ht|f)tp(?:s?))\://)?\S+#', function($matches){
            $ret = '';
            if (empty($matches[1])) $ret .= 'http://' . $matches[0];
            else $ret = $matches[0];
            return $ret;
        }, trim($href, '\'"'));
    }
}