<?php
require_once __DIR__ . '/../GDGraphicalObjects.php';

class ChartException extends Exception{
    function __construct($message, $code = 0, Exception $previous = NULL){
        parent::__construct($message, $code = 0, $previous = NULL);
    }
}

abstract class Chart extends GDGraphicalObject{
	protected $colors = [];
	protected $data=[];
	protected $leg=[];
	protected $title;
    protected $values;
	protected $margin=array('top'=>10,'left'=>20,'right'=>10,'bottom'=>10);

    function __construct($bounds){
        $this->bound = Boundary::copyOrDefault($bounds);
        $this->title = ['text' => 'Диаграмма', 'color' => Color::getDefault(), 'font' => Font::getDefault(), 'margin' => ['top' => 10, 'bottom' => 10]];
        $this->values = ['bgcolors' => [Color::getDefault()], 'fgcolors' => [Color::getDefault()], 'font' => Font::getDefault(), 'labels' => TRUE];
        $this->bgcolor = new Color(255,255,255);
    }

    function title(array $t = NULL){
		if ($t !== NULL){
			if (isset($t['text'])) $this->title['text']=$t['text'];
			if (isset($t['color']) && $t['color'] instanceof Color) $this->title['color'] = $t['color'];
			if (isset($t['font']) && $t['font'] instanceof Font) $this->title['font'] = $t['font'];
			if (isset($t['margin']['top'])) $this->title['margin']['top'] = $t['margin']['top'];
			if (isset($t['margin']['bottom'])) $this->title['margin']['bottom'] = $t['margin']['bottom'];
		}
		return $this->title;
    }

    function values(array $v = NULL){ 
		if ($v !== NULL){
			if (isset($v['bgcolors']) && is_array($v['bgcolors'])) $this->values['bgcolors'] = array_filter($v['bgcolors'], function ($a) { return $a instanceof Color; });
			if (isset($v['fgcolors']) && is_array($v['fgcolors'])) $this->values['fgcolors'] = array_filter($v['fgcolors'], function ($a) { return $a instanceof Color; });
			if (isset($v['font']) && $v['font'] instanceof Font) $this->values['font'] = $v['font'];
			if (isset($v['labels'])) $this->values['labels'] = $v['labels'];

			if (count($this->values['bgcolors']) == 0) $this->values['bgcolors'][] = Color::getDefault();
			if (count($this->values['fgcolors']) == 0) $this->values['fgcolors'][] = Color::getDefault();
		}
		return $this->values;
    }

    function margins(array $m = NULL){
		if ($m !== NULL){
			if (isset($m['top'])) $this->margin['top'] = $m['top'];
			if (isset($m['left'])) $this->margin['top'] = $m['left'];
			if (isset($m['right'])) $this->margin['top'] = $m['right'];
			if (isset($m['bottom'])) $this->margin['top'] = $m['bottom'];
		}
		return $this->margin;
    }

	function data(array $d = NULL){
		if ($d !== NULL) {
			$this->data = array_values($d);
			$this->leg = array_keys($d);
			return $d;
		}
		return array_combine($this->leg, $this->data);
	}

    function drawTitle($image, Boundary $b){
		$s=$this->title['font']->getTextExtent($this->title['text']);
		//var_dump(imagettfbbox($this->title['font']->size,$this->title['font']->angle,$this->title['font']->family,$this->title['text']));
		//throw new exception ($s->height);
        if ($s->height > $b->height() || $s->width > $b->width())
            throw new ChartException("Недостаточно места для вывода заголовка.");
        $b->bottom($b->top() + $s->height);
		

	    $title = new Label($b,
                           $this->title['text'],
                           $this->title['font'],
                           $this->title['color'],
                           Label::ALIGN_CENTER_TOP);

		$title->draw($image);
	}

    abstract function drawLegend($image, Boundary $b);
    abstract function drawValues($image, Boundary $b);

    function draw($image){
        (new Rectangle($this->bound, $this->bgcolor, NULL, 0))->draw($image);
    }
}