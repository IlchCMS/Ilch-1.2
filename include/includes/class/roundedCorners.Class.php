<?php

class roundedCorner
{
    /**
     * stores the mime type of picture that gets manipulated
     *
     * @var 
     */
    protected $_source_type;

    /**
     * stores the width of picture that gets manipulated
     *
     * @var 
     */
    protected $_source_width;
    
    /**
     * stores the height of picture that gets manipulated
     *
     * @var 
     */
    protected $_source_height;
    
    /**
     * stores the path / re-created picture that gets manipulated
     *
     * @var 
     */
    protected $_source_image;
    
    /**
     * stores the radius of the rounded corners that needs to be created
     *
     * @var 
     */
    protected $_radius;

    /**
     * stores the color of the rounded corners
     *
     * @var 
     */
    protected $_backgroundcolor;
    
    /**
     * stores the rounded corners
     *
     * @var 
     */
    protected $_corner_image;

    /**
     * stores the transparent mask for the rounded corner
     *
     * @var 
     */
    protected $_clear_color;
    
    /**
     * stores ... uhm ... solid colors you can rely on
     *
     * @var 
     */
    protected $_solid_color;

    /**
     * stores the color that get replaced with transparency
     *
     * @var 
     */    
    protected $_transparencyColor;
    
    public function generateRoundedCornerImage($imageObj, $radius = '10', $backgroundcolor = NULL, $roundcorners = 'all', $savePath = '', $saveName = 'fallback', $transparencyColor = 'ff00f0')
    {
        list( $this->_source_width, $this->_source_height, $this->_source_type ) = getimagesize( $imageObj );
        $this->_radius = $radius;
        $this->_transparencyColor = $transparencyColor;
        $this->_backgroundcolor = $this->getBackgroundcolor($backgroundcolor);
        $this->_roundcorners = $roundcorners;
        $this->_source_image = imagecreatefromjpeg( $imageObj );
        $this->createCornerMask();
        $this->applyRoundedCorners();
        $this->doTransparentCorners();
        imagepng( $this->_source_image, $savePath.$saveName.'.png' );
    }
    
    private function doTransparentCorners()
    {
        if ($this->_backgroundcolor == $this->_transparencyColor) {
            $rgb_bg_color = $this->hex2rgb();
            $indexcolor = imagecolorallocate($this->_source_image, $rgb_bg_color[0], $rgb_bg_color[1], $rgb_bg_color[2]);
            imagefill($this->_source_image, 0, 0, $indexcolor); 
            imagecolortransparent  ($this->_source_image, $indexcolor );
        }        
    }
    
    private function getBackgroundcolor($backgroundcolor)
    {
        if ($backgroundcolor) {
            
            if ($backgroundcolor[0] == '#') { 
                $backgroundcolor = substr($backgroundcolor, 1); 
            }
            
            if (strlen($backgroundcolor) == 3) { 
                $backgroundcolor = $backgroundcolor[0].$backgroundcolor[0].$backgroundcolor[1].$backgroundcolor[1].$backgroundcolor[2].$backgroundcolor[2];
            }
            
            return $backgroundcolor;
        } else {
            return $this->_transparencyColor;
        }
    }
    
    private function hex2rgb()
    {
        $color = $this->_backgroundcolor;
        if ($color[0] == '#')
            $color = substr($color, 1);
    
        if (strlen($color) == 6)
            list($r, $g, $b) = array($color[0].$color[1],
                                     $color[2].$color[3],
                                     $color[4].$color[5]);
        elseif (strlen($color) == 3)
            list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
        else
            return false;
    
        $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
    
        return array($r, $g, $b);
    }
    
    private function createCornerMask()
    {
        $this->_corner_image = imagecreatetruecolor(
            $this->_radius,
            $this->_radius
        );
        
        $this->_clear_color = imagecolorallocate(
            $this->_corner_image,
            0,
            0,
            0
        );
        
        $this->_solid_color = imagecolorallocate(
            $this->_corner_image,
            hexdec( substr( $this->_backgroundcolor, 0, 2 ) ),
            hexdec( substr( $this->_backgroundcolor, 2, 2 ) ),
            hexdec( substr( $this->_backgroundcolor, 4, 2 ) )
        );
        
        imagecolortransparent(
            $this->_corner_image,
            $this->_clear_color
        );
        
        imagefill(
            $this->_corner_image,
            0,
            0,
            $this->_solid_color
        );
        
        imagefilledellipse(
            $this->_corner_image,
            $this->_radius,
            $this->_radius,
            $this->_radius * 2,
            $this->_radius * 2,
            $this->_clear_color
        );
    }
    
    private function applyRoundedCorners()
    {
        if($this->_roundcorners == 'all' || in_array('topleft', $this->_roundcorners)){
            imagecopymerge(
                $this->_source_image,
                $this->_corner_image,
                0,
                0,
                0,
                0,
                $this->_radius,
                $this->_radius,
                100
            );
        }
        
        $this->_corner_image = imagerotate( $this->_corner_image, 90, 0 );
        
        if($this->_roundcorners == 'all' || in_array('bottomleft', $this->_roundcorners)){
            imagecopymerge(
                $this->_source_image,
                $this->_corner_image,
                0,
                $this->_source_height - $this->_radius,
                0,
                0,
                $this->_radius,
                $this->_radius,
                100
            );
        }
        
        $this->_corner_image = imagerotate( $this->_corner_image, 90, 0 );
        
        if($this->_roundcorners == 'all' || in_array('bottomright', $this->_roundcorners)){
            imagecopymerge(
                $this->_source_image,
                $this->_corner_image,
                $this->_source_width - $this->_radius,
                $this->_source_height - $this->_radius,
                0,
                0,
                $this->_radius,
                $this->_radius,
                100
            );
        }
        
        $this->_corner_image = imagerotate( $this->_corner_image, 90, 0 );

        if($this->_roundcorners == 'all' || in_array('topright', $this->_roundcorners)){        
            imagecopymerge(
                $this->_source_image,
                $this->_corner_image,
                $this->_source_width - $this->_radius,
                0,
                0,
                0,
                $this->_radius,
                $this->_radius,
                100
            );
        }
    }
}