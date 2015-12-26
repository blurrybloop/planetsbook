<?php
class Boundary{
    protected $left = 0;
    protected $top = 0;
    protected $right = 0;
    protected $bottom = 0;

    const OFFSET_ABSOLUTE = 0;
    const OFFSET_RELATIVE = 1;

    function __construct($l, $t, $r, $b) {
        $this->left = $l;
        $this->top = $t;
        $this->right = $r;
        $this->bottom = $b;
    }

    protected function coordinate($name, $value) {
        if (!in_array($name, ['left', 'top', 'right', 'bottom'])) return NULL;
        if ($value !== NULL && is_numeric($value)) $this->$name = $value;
        return $this->$name;
    }

    function normalize(){
        if ($this->left > $this->right){
            $tmp = $this->left;
            $this->left = $this->right;
            $this->right = $tmp;
        }

        if ($this->top > $this->bottom){
            $tmp = $this->top;
            $this->top = $this->bottom;
            $this->bottom = $tmp;
        }
        return $this;
    }

    function move($size, $type = self::OFFSET_ABSOLUTE){
        if ($size instanceof Size){
            if ($type == self::OFFSET_ABSOLUTE){
                $w = $this->width();
                $h = $this->height();

                $this->left = $size->width;
                $this->right = $this->left + $w;
                $this->top = $size->height;
                $this->bottom = $this->top + $h;
            }
            else if ($type == self::OFFSET_RELATIVE){
                $this->left += $size->width;
                $this->right +=$size->width;
                $this->top += $size->height;
                $this->bottom += $size->height;
            }
        }
        return $this;
    }

    function left($value = NULL) { return $this->coordinate('left', $value); }
    function right($value = NULL) { return $this->coordinate('right', $value); }
    function top($value = NULL) { return $this->coordinate('top', $value); }
    function bottom($value = NULL) { return $this->coordinate('bottom', $value); }
    function width() { return abs($this->right - $this->left); }
    function height() { return abs($this->bottom - $this->top); }

    static function copy($bound){
        if ($bound instanceof Boundary) return new Boundary($bound->left, $bound->top, $bound->right, $bound->bottom);
        return NULL;
    }

    static function copyOrDefault($bound){
        $c = self::copy($bound) or $c = self::defaultBound();
        return $c;
    }

    static function defaultBound(){
        return new Boundary(0,0,0,0);
    }
}

class Size {
	public $width;
	public $height;

    function __construct($w = 0, $h = 0){
        $this->width = $w;
        $this->height = $h;
    }
}

class Color {
    public $red = 0;
    public $green = 0;
    public $blue = 0;
    public $alpha = 0;

    function __construct($red, $green, $blue, $alpha=0){
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
        $this->alpha = $alpha;
    }

    function resolve($image){
        return imagecolorresolvealpha($image, $this->red, $this->green, $this->blue, $this->alpha);
    }

    static function getDefault(){
        return new Color(0,0,0);
    }
}

class Font{
	public $size = NULL;
	public $family = NULL;
	public $angle = NULL;

	function __construct($family, $size=12, $angle=0) {
		$this->family=$family;
		$this->size=$size;
		$this->angle=$angle;
	}

    public function isPHPFont() { return is_numeric($this->family); }

    public function getTextExtent($text = '9'){
		$s = new Size;
        if ($this->isPHPFont()) {
            $s->height = imagefontheight($this->family);
            $s->width = imagefontwidth($this->family)*strlen($text);
        }
        else {
		
			$rect=imagettfbbox($this->size,$this->angle,$this->family,$text);
			$s->height = abs(max($rect[1],$rect[3],$rect[5],$rect[7]) - min($rect[1],$rect[3],$rect[5],$rect[7]));
			$s->width = abs(max($rect[0],$rect[2],$rect[4],$rect[6]) - min($rect[0],$rect[2],$rect[4],$rect[6]));

        }
		return $s;
	}

	public function getTTFBox($text = '9'){
		if (!$this->isPHPFont()){
			return imagettfbbox($this->size,$this->angle,$this->family,$text);
		}
	}

    static function getDefault(){
        return new Font(1);
    }
}


abstract class GDGraphicalObject{
    protected $bound;
    protected $bgcolor = NULL;

    function __construct(Boundary $bound, Color $bgcolor = NULL){
        $this->bound = Boundary::copyOrDefault($bound);
        $this->background($bgcolor) or $this->bgcolor = Color::getDefault();
    }

    function background(Color $c = NULL){
        if ($c !== NULL) $this->bgcolor = $c;
        return $this->bgcolor;
    }

    abstract function draw($image);
    public function bounds(Boundary $value = NULL) {
		if ($value !== NULL) $this->bound = Boundary::copy($value);
		return $this->bound;
	}
}