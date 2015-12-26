<?php

require_once 'BarChart.php';

class LineChart extends BarChart{

	function __construct($bound){
		parent::__construct($bound);
        $this->values['thickness'] = 2;
        $this->axis['y_lines'] = FALSE;
		$this->depth = 0;
	}

    function values(array $v = NULL){
        if ($v != NULL){
            if (isset($v['thickness'])) $this->values['thickness'] = $v['thickness'];
        }
        return parent::values($v);
    }

    function depth($value = NULL) {
        return parent::depth($value === NULL ? NULL : 0);
    }

	function drawValues($image, Boundary $b){
		$out = $this->getValuesArea($b);
        if ($out->width() < $this->depth || $out->height() < $this->depth || $out->left() > $out->right() || $out->top() > $out->bottom())
            throw new ChartException("Недостаточно места для вывода значений.");

		$line = new Line(new Boundary(0,0,0,0), $this->values['bgcolors'][0], $this->values['thickness']);
		$w=($out->width() - $this->depth)/count($this->data);
		$l = new Label(new Boundary(0,0,0,0), '', $this->values['font'], Color::getDefault(), Label::ALIGN_CENTER_MIDDLE);
        $e = new Ellipse(new Boundary(0,0, $this->values['thickness'] * 3, $this->values['thickness'] * 3));

		for ($i=0;$i<count($this->data);$i++) {

			$c = $out->left()+$w*$i+$w/2.0;
			$line->bounds()->right($c);
			$line->bounds()->bottom($out->bottom() - ($this->data[$i]-$this->min)*$out->height()/($this->max-$this->min));
            $line->background($this->values['bgcolors'][$i%count($this->values['bgcolors'])]);

			if ($i != 0){
				$line->draw($image);
                $e->bounds()->move(new Size($line->bounds()->left() - $this->values['thickness'] * 3 / 2, $line->bounds()->top() - $this->values['thickness'] * 3 /2));
                $e->background($this->values['bgcolors'][($i-1)%count($this->values['bgcolors'])]);
                $e->draw($image);
            }

            $line->bounds()->left($line->bounds()->right());
            $line->bounds()->top($line->bounds()->bottom());

			if ($this->values['labels']) {
			$l->text($this->data[$i]);

	        $l->bounds()->bottom($line->bounds()->top()-5);
            $l->bounds()->top($l->bounds()->bottom() - $this->values['font']->getTextExtent()->height);
            $l->bounds()->left($line->bounds()->left());
            $l->bounds()->right($line->bounds()->left());
            $l->background($this->values['fgcolors'][$i%count($this->values['fgcolors'])]);

			$l->draw($image);
			}
		}

        $e->bounds()->move(new Size($line->bounds()->left() - $this->values['thickness'] * 3/2, $line->bounds()->top() - $this->values['thickness'] * 3/2));
        $e->background($line->background());
        $e->draw($image);
	}
}