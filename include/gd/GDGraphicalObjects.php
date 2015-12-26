<?php 
require_once 'GDHelpers.php';

class Label extends GDGraphicalObject{

    private $font = NULL;
    private $text = NULL;
    private $align = NULL;

    const ALIGN_LEFT_TOP = 0;
    const ALIGN_CENTER_TOP = 1;
    const ALIGN_RIGHT_TOP = 2;
    const ALIGN_LEFT_MIDDLE = 3;
    const ALIGN_CENTER_MIDDLE = 4;
    const ALIGN_RIGHT_MIDDLE = 5;
    const ALIGN_LEFT_BOTTOM = 6;
    const ALIGN_CENTER_BOTTOM = 7;
    const ALIGN_RIGHT_BOTTOM = 8;

    function __construct(Boundary $bound, $text = NULL, Font $font = NULL, Color $color = NULL, $align=self::ALIGN_LEFT_TOP){
        parent::__construct($bound, $color);
        $this->text($text) or $this->text = '';
        $this->font($font) or $this->font = Font::getDefault();
        $this->align($align) or $this->align = self::ALIGN_LEFT_TOP;
    }

    function font($value = NULL){
        if ($value instanceof Font) $this->font = $value;
        return $this->font;
    }

    function text($value = NULL){
        if ($value !== NULL) $this->text = (string)$value;
        return $this->text;
    }

    function align($value = NULL){
        if ($value !== NULL && $value >= self::ALIGN_LEFT_TOP && $value <= self::ALIGN_RIGHT_BOTTOM)
            $this->align = $value;
        return $this->align;
    }

    function draw($image) {
        $s = $this->font->getTextExtent($this->text);
        $w = $s->width;
        $h = ($s->height);

        if ($this->align >= self::ALIGN_LEFT_TOP && $this->align <= self::ALIGN_RIGHT_TOP) $y = $this->bound->top();
        else if ($this->align >= self::ALIGN_LEFT_MIDDLE && $this->align <= self::ALIGN_RIGHT_MIDDLE) $y = $this->bound->top() + ($this->bound->height() - $h) / 2;
        else if ($this->align >= self::ALIGN_LEFT_BOTTOM && $this->align <= self::ALIGN_RIGHT_BOTTOM) $y = $this->bound->bottom() - $h;

        if ($this->align % 3 == 0) $x = $this->bound->left();
        else if ($this->align % 3 == 1) $x = $this->bound->left() + ($this->bound->width() - $w) / 2;
        else if ($this->align % 3 == 2) $x = $this->bound->right() - $w;

        if ($this->font->isPHPFont()) {
			imagestring($image, $this->font->family, $x, $y, $this->text, $this->bgcolor->resolve($image));
		}
        else {
			$rect = $this->font->getTTFBox($this->text);
			$dy = min($rect[1],$rect[3],$rect[5],$rect[7]);
			$dx = min($rect[0],$rect[2],$rect[4],$rect[6]);
			imagettftext($image, $this->font->size, $this->font->angle, $x - $dx + $rect[0], $y - $dy + $rect[1], $this->bgcolor->resolve($image), $this->font->family, $this->text);
		}
    }
}

class Line extends GDGraphicalObject
{
	private $linewidth = 1;

    function __construct(Boundary $bound, Color $color = NULL, $linewidth = 1){
        parent::__construct($bound, $color);
		$this->lineWidth($linewidth);
    }

	function lineWidth($value = NULL){
		if (is_numeric($value)) $this->linewidth = $value;
        return $this->linewidth;
	}

	public function draw($image) {
		imagesetthickness($image, $this->linewidth);
		imageline($image, $this->bound->left(), $this->bound->top(), $this->bound->right(), $this->bound->bottom(), $this->bgcolor->resolve($image));
		imagesetthickness($image, 1);
	}
}

class Rectangle extends GDGraphicalObject
{
    protected $fgcolor = NULL;
    protected $sides = 15;

    const SIDE_LEFT = 1;
    const SIDE_RIGHT = 2;
    const SIDE_TOP = 4;
    const SIDE_BOTTOM = 8;

    function __construct($bound, Color $bgcolor = NULL, Color $fgcolor = NULL, $sides = 15){
        parent::__construct($bound, $bgcolor);
        $this->fgcolor($fgcolor);
        $this->sides($sides);
    }

    function fgcolor(Color $value = NULL){
        if ($value !== NULL) $this->fgcolor = $value;
        return $this->fgcolor;
    }

    function sides($value = NULL){
    	if ($value !== NULL && $value >= 0 && $value <=15) $this->sides = $value;
        return $this->sides;
    }

	public function draw($image) {
		imagefilledrectangle($image, $this->bound->left(), $this->bound->top(), $this->bound->right(), $this->bound->bottom(), $this->bgcolor->resolve($image));
        if ($this->fgcolor !== NULL){
            if ($this->sides & self::SIDE_LEFT) imageline($image, $this->bound->left(), $this->bound->top(), $this->bound->left(), $this->bound->bottom(), $this->fgcolor->resolve($image));
            if ($this->sides & self::SIDE_TOP) imageline($image, $this->bound->left(), $this->bound->top(), $this->bound->right(), $this->bound->top(), $this->fgcolor->resolve($image));
            if ($this->sides & self::SIDE_RIGHT) imageline($image, $this->bound->right(), $this->bound->top(), $this->bound->right(), $this->bound->bottom(), $this->fgcolor->resolve($image));
            if ($this->sides & self::SIDE_BOTTOM) imageline($image, $this->bound->left(), $this->bound->bottom(), $this->bound->right(), $this->bound->bottom(), $this->fgcolor->resolve($image));
        }
	}
}

class Rectangle3D extends Rectangle{

    const SURFACE_FRONT = 1;
    const SURFACE_LEFT = 2;
    const SURFACE_RIGHT = 4;
    const SURFACE_BACK = 8;
    const SURFACE_TOP = 16;
    const SURFACE_BOTTOM = 32;

    protected $surfaces = 63;
    protected $depth = 15;

    function __construct($bound, $bgcolor = NULL, $fgcolor = NULL, $surfaces = 63, $depth=15){
        parent::__construct($bound, $bgcolor, $fgcolor);
        $this->surfaces($surfaces);
        $this->depth($depth);
    }

    function surfaces($value = NULL){
    	if ($value !== NULL && $value >= 1 && $value <=63) $this->surfaces = $value;
        return $this->surfaces;
    }

    function depth($value){
        if ($value !== NULL && $value >= 0) $this->depth = $value;
        return $this->surfaces;
    }

    function draw($image){
        $this->bound->normalize();
        $depth = $this->depth;

        if ($this->surfaces & self::SURFACE_BOTTOM){

            $points = [
                    $this->bound->left(), $this->bound->bottom(),
                    $this->bound->left() + $depth, $this->bound->bottom() - $depth,
                    $this->bound->right() + $depth, $this->bound->bottom() - $depth,
                    $this->bound->right(), $this->bound->bottom(),
            ];
            imagefilledpolygon($image, $points, 4, $this->bgcolor->resolve($image));
            if ($this->fgcolor !== NULL) imagepolygon($image, $points, 4, $this->fgcolor->resolve($image));
        }

        if ($this->surfaces & self::SURFACE_BACK){
            $points = [
                    $this->bound->left() + $depth, $this->bound->bottom() - $depth,
                    $this->bound->left() + $depth, $this->bound->top() - $depth,
                    $this->bound->right() + $depth, $this->bound->top() - $depth,
                    $this->bound->right()+ $depth, $this->bound->bottom() - $depth,
            ];
            imagefilledpolygon($image, $points, 4, $this->bgcolor->resolve($image));
            if ($this->fgcolor !== NULL) imagepolygon($image, $points, 4, $this->fgcolor->resolve($image));
        }

        if ($this->surfaces & self::SURFACE_LEFT){
            $points = [
                    $this->bound->left(), $this->bound->bottom(),
                    $this->bound->left(), $this->bound->top(),
                    $this->bound->left() + $depth, $this->bound->top() - $depth,
                    $this->bound->left() + $depth, $this->bound->bottom() - $depth,
            ];
            imagefilledpolygon($image, $points, 4, $this->bgcolor->resolve($image));
            if ($this->fgcolor !== NULL) imagepolygon($image, $points, 4, $this->fgcolor->resolve($image));
        }

        if ($this->surfaces & self::SURFACE_RIGHT){
            $points = [
                    $this->bound->right(), $this->bound->top(),
                   $this->bound->right() + $depth, $this->bound->top() - $depth,
                   $this->bound->right() + $depth, $this->bound->bottom() - $depth,
                   $this->bound->right(), $this->bound->bottom(),
            ];
            imagefilledpolygon($image, $points, 4, $this->bgcolor->resolve($image));
            if ($this->fgcolor !== NULL) imagepolygon($image, $points, 4, $this->fgcolor->resolve($image));
        }

        if ($this->surfaces & self::SURFACE_TOP){
            $points = [
                    $this->bound->left(), $this->bound->top(),
                    $this->bound->left() + $depth, $this->bound->top() - $depth,
                    $this->bound->right() + $depth, $this->bound->top() - $depth,
                    $this->bound->right(), $this->bound->top(),
            ];
            imagefilledpolygon($image, $points, 4, $this->bgcolor->resolve($image));
            if ($this->fgcolor !== NULL) imagepolygon($image, $points, 4, $this->fgcolor->resolve($image));
        }

        if ($this->surfaces & self::SURFACE_FRONT){
            parent::draw($image);
        }


    }
}

class Ellipse extends GDGraphicalObject{
    protected $fgcolor = NULL;

    function __construct($bound, Color $bgcolor = NULL, Color $fgcolor = NULL){
        parent::__construct($bound, $bgcolor);
        $this->fgcolor($fgcolor);
    }

    function fgcolor(Color $value = NULL){
        if ($value !== NULL) $this->fgcolor = $value;
        return $this->fgcolor;
    }


	public function draw($image) {
        $this->bounds()->normalize();
		imagefilledellipse($image, $this->bound->left() + $this->bound->width()/2, $this->bound->top()+$this->bound->height()/2, $this->bound->width(), $this->bound->height(), $this->bgcolor->resolve($image));
		if ($this->fgcolor !== NULL)
            imageellipse($image, $this->bound->left() + $this->bound->width()/2, $this->bound->top()+$this->bound->height()/2, $this->bound->width(), $this->bound->height(), $this->fgcolor->resolve($image));
	}
}