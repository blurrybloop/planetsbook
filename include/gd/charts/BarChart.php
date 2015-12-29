<?php 
require_once 'Chart.php';

class BarChart extends Chart {
	protected $min = 0, $max = 100, $count_y=10;
	protected $maxleg = 1;
	protected $axis;
	protected $depth = 0;

	function __construct($bound) {
        parent::__construct($bound);
        $this->axis = ['bgcolor' => new Color(255,255,255,0), 'fgcolor' => Color::getDefault(), 'font_x' => Font::getDefault(), 'font_y' => Font::getDefault(), 'visible' => TRUE, 'y_lines' => TRUE];
	}

    function axis(array $l = NULL){
		if ($l !== NULL){
			if (isset($l['bgcolor']) && $l['bgcolor'] instanceof Color) $this->axis['bgcolor'] = $l['bgcolor'];
			if (isset($l['fgcolor']) && $l['fgcolor'] instanceof Color) $this->axis['fgcolor'] = $l['fgcolor'];
			if (isset($l['font_x']) && $l['font_x'] instanceof Font) $this->axis['font_x'] = $l['font_x'];
            if (isset($l['font_y']) && $l['font_y'] instanceof Font) $this->axis['font_y'] = $l['font_y'];
			if (isset($l['visible'])) $this->axis['visible'] = $l['visible'];
			if (isset($l['y_lines'])) $this->axis['y_lines'] = $l['y_lines'];
		}
		return $this->axis;
    }

	function data(array $d = NULL){
        if ($d !== NULL){
            $this->max = empty($d) ? 100 : max($d);
            $this->max += $this->max / 5;
            $this->max = ($this->max / $this->count_y) * $this->count_y;
            $this->min = empty($d) ? 0 : (min($d) / $this->count_y) * $this->count_y;
            if ($this->max == $this->min) $this->max = $this->max == 0 ? 1 : $this->max + $this->max / 5;
            $k = array_keys($d);
            $this->maxleg = '';
            foreach ($k as $key) 
                if (strlen((string)$key) > strlen($this->maxleg)) $this->maxleg = (string)$key;
        }
		return parent::data($d);
	}

	function depth($value = NULL){ 
		if ($value !== NULL) $this->depth = $value;
		return $this->depth;
	}

	function max($value = NULL) { 
		if ($value !== NULL && max($this->data) < $value) $this->max = $value; 
		return $this->max;
	}

	function min($value = NULL) { 
		if ($value !== NULL && min($this->data) > $value) $this->min = $value; 
		return $this->min;
	}

	protected function getD(){
		return new Size($this->axis['font_y']->getTextExtent()->width/2, $this->axis['font_x']->getTextExtent()->height/2);
	}

	protected function getValuesArea(Boundary $b){
		$dy=$this->getD()->width;
		$dx=$this->getD()->height;
		$h = $this->axis['font_x']->getTextExtent($this->maxleg)->height;
		$dbottom = $b->bottom() - ($h < $dx*4 ? $dx * 4 : $h);
		$dleft = $b->left() + $this->axis['font_y']->getTextExtent($this->max)->width+$dy*2;
	    $dtop = $b->top() + $this->depth;
		$dright = $b->right() + $this->depth;
		return new Boundary($dleft, $dtop, $dright, $dbottom);
	}

	function drawLegend($image, Boundary $b){
        $out = $this->getValuesArea($b);
        if ($out->width() < $this->depth || $out->height() < $this->depth || $out->left() > $out->right() || $out->top() > $out->bottom())
            throw new ChartException("Недостаточно места для вывода легенды.");
        $dy=$this->getD()->width;
        $dx=$this->getD()->height;

        $linedy = new Line(new Boundary(0,0,0,0));
        $l = new Label(new Boundary($b->left(),0,$out->left() - $dx - 3,0), '', $this->axis['font_y'], $this->axis['fgcolor'], Label::ALIGN_RIGHT_TOP);
        $surface = new Rectangle3D(new Boundary($out->left(),$out->bottom(),0,$out->top()), $this->axis['bgcolor'], $this->axis['fgcolor'], Rectangle3D::SURFACE_LEFT | ($this->axis['y_lines'] ? Rectangle3D::SURFACE_BACK :0), $this->depth);

		for ($i=0;$i<=$this->count_y;$i++) {
			$y=$i*($this->max-$this->min)/$this->count_y;
			$ypx=$out->height()*$y/($this->max-$this->min);

            $linedy->bounds()->left($out->left()-$dy);
            $linedy->bounds()->right($out->left());
            $linedy->bounds()->top($out->bottom() - $ypx);
            $linedy->bounds()->bottom($linedy->bounds()->top());

            $surface->bounds()->bottom($surface->bounds()->top());	
            $surface->bounds()->top($linedy->bounds()->top());
            $surface->bounds()->right($out->right() - $this->depth);

            if ($i == 0)
              $surface->surfaces($surface->surfaces() | Rectangle3D::SURFACE_BOTTOM);
            else
              $surface->surfaces($surface->surfaces() & ~Rectangle3D::SURFACE_BOTTOM);

            $surface->draw($image);

			$linedy->draw($image);
			$text= round($y+$this->min, 2);
            $l->bounds()->move(new Size($b->left(), $linedy->bounds()->top() - $this->axis['font_y']->getTextExtent($text)->height/2));
            $l->text($text);

			$l->draw($image);
		}
        $linex = new Line(new Boundary(0,0,0,0), $this->axis['fgcolor']);

        $w=($out->width() - $this->depth)/count($this->data);
        $l->font($this->axis['font_x']);
        $l->align(Label::ALIGN_LEFT_TOP);


        for ($i=0;$i<count($this->
    data);$i++) {
    $linex->bounds()->left($out->left()+$w*$i+$w/2.0);
    $linex->bounds()->top($out->bottom());
    $linex->bounds()->right($linex->bounds()->left());
    $linex->bounds()->bottom($linex->bounds()->top()+$dx);
    $linex->draw($image);

    if (isset($this->leg[$i])){
    $l->bounds()->move(new Size($linex->bounds()->right() - $this->axis['font_x']->getTextExtent($this->leg[$i])->width/2, $linex->bounds()->bottom()));
    $l->text($this->leg[$i]);
    $l->draw($image);
    }
    }
    }

    function drawValues($image, Boundary $b){
    $out = $this->getValuesArea($b);
    if ($out->width() < $this->depth || $out->height() < $this->depth || $out->left() > $out->right() || $out->top() > $out->bottom())
    throw new ChartException("Недостаточно места для вывода значений.");

    $r = new Rectangle3D(new Boundary(0,0,0,0), NULL, Color::getDefault(), NULL, $this->depth);
    $w=($out->width() - $this->depth)/count($this->data);
    $wr=$w/1.5 - $this->depth;
    $l = new Label(new Boundary(0,0,0,0), '', $this->values['font'], Color::getDefault(), Label::ALIGN_CENTER_MIDDLE);

    for ($i=0;$i<count($this->
        data);$i++) {

        $c = $out->left()+$w*$i+$w/2.0;
        $r->bounds()->left($c-$wr/2);
        $r->bounds()->right($c+$wr/2);
        $r->bounds()->top($out->bottom() - ($this->data[$i]-$this->min)*$out->height()/($this->max-$this->min));
        $r->bounds()->bottom($out->bottom() - 1);
        $r->background($this->values['bgcolors'][$i%count($this->values['bgcolors'])]);

        $r->draw($image);
        $l->text($this->data[$i]);

        if (($h = $this->values['font']->getTextExtent()->height) > $r->bounds()->height()){
        $r->bounds()->bottom($r->bounds()->top());
        $r->bounds()->top($r->bounds()->bottom()-$h);
        }

		if ($this->values['labels']) {
        $l->bounds($r->bounds());
        $l->background($this->values['fgcolors'][$i%count($this->values['fgcolors'])]);
        $l->draw($image);
		}
        }
        }

        function draw($image) {
        parent::draw($image);

        $this->margin['right'] += $this->depth;

        try {

        $tb = new Boundary(
        $this->bound->left() + $this->margin['left'],
        $this->bound->top() + $this->margin['top'] + $this->title['margin']['top'],
        $this->bound->right() - $this->margin['right'],
        $this->bound->bottom());

        $this->drawTitle($image, $tb);

        $ob = new Boundary(
        $this->bound->left() + $this->margin['left'],
        $tb->bottom() + $this->title['margin']['bottom'],
        $this->bound->right() - $this->margin['right'],
        $this->bound->bottom()-$this->margin['bottom']);

        $this->drawLegend($image, $ob);
        $this->drawValues($image, $ob);
        }

        catch (ChartException $ex){
        (new Label($this->bound, $ex->getMessage(), $this->font_title, $this->color_title, Label::ALIGN_CENTER_MIDDLE))->draw($image);
        }
        }

        }
