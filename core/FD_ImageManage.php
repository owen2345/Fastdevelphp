<?php
/**
 * @package FastDevelPHP
 * @author Ing. Florencio Peredo
 * @email owen@sysdecom.com
 * @company Systems Development Company "Sysdecom" srl.
 * @license All rights reservate
 * @version 1.0
 * @copyright 2009
 */
 
Class FD_ImageManage
{
    function returnCorrectFunction($ext)
    {
        $function = "";
        switch($ext){
            case "png":
                $function = "imagecreatefrompng"; 
                break;
            case "jpeg":
                $function = "imagecreatefromjpeg"; 
                break;
            case "jpg":
                $function = "imagecreatefromjpeg";  
                break;
            case "gif":
                $function = "imagecreatefromgif"; 
                break;
        }
        return $function;
    }
    
    function parseImage($ext,$img,$file = null)
    {
        switch($ext)
        {
            case "png":
                imagepng($img,($file != null ? $file : '')); 
                break;
            case "jpeg":
                imagejpeg($img,($file ? $file : ''),90); 
                break;
            case "jpg":
                imagejpeg($img,($file ? $file : ''),90);
                break;
            case "gif":
                imagegif($img,($file ? $file : ''));
                break;
        }
    }
    
    function setTransparency($imgSrc,$imgDest,$ext)
    {
       
        if($ext == "png" || $ext == "gif")
        {
            $trnprt_indx = imagecolortransparent($imgSrc);
            // If we have a specific transparent color
            if ($trnprt_indx >= 0) {
                // Get the original image's transparent color's RGB values
                $trnprt_color    = imagecolorsforindex($imgSrc, $trnprt_indx);
                // Allocate the same color in the new image resource
                $trnprt_indx    = imagecolorallocate($imgDest, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                // Completely fill the background of the new image with allocated color.
                imagefill($imgDest, 0, 0, $trnprt_indx);
                // Set the background color for new image to transparent
                imagecolortransparent($imgDest, $trnprt_indx);
            } 
            // Always make a transparent background color for PNGs that don't have one allocated already
            elseif ($ext == "png") {
               // Turn off transparency blending (temporarily)
               imagealphablending($imgDest, true);
               // Create a new transparent color for image
               $color = imagecolorallocatealpha($imgDest, 0, 0, 0, 127);
               // Completely fill the background of the new image with allocated color.
               imagefill($imgDest, 0, 0, $color);
               // Restore transparency blending
               imagesavealpha($imgDest, true);
            }
            
        }
    }
    
    /**
     * $fileNameSave: for save the image generate in directory $fileNameSave
    */
    function crop_resize($src, $fileNameSave = "", $imgW = 50, $imgH = 50, $imgX = 0, $imgY = 0, $angleRotate = 0, $selectorX = 0, $selectorY = 0, $selectorW = 50, $selectorH = 50, $viewPortW = 0, $viewPortH = 0)
    {
        $pWidth = $imgW;
        $pHeight = $imgH;
        list($width, $height) = getimagesize($src);
        $ext = end(explode(".",$src));
        $function = $this->returnCorrectFunction($ext);  
        $image = $function($src);
        $width = imagesx($image);    
        $height = imagesy($image);
        // Resample
        $image_p = imagecreatetruecolor($pWidth, $pHeight);
        $this->setTransparency($image,$image_p,$ext);
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $pWidth, $pHeight, $width, $height);
        imagedestroy($image); 
        $widthR = imagesx($image_p);
        $hegihtR = imagesy($image_p);
        
        if($angleRotate != "")
        {
            $angle = 360 - $angleRotate;
            $image_p = imagerotate($image_p,$angle,0);
            $pWidth = imagesx($image_p);
            $pHeight = imagesy($image_p);
        }
        if($pWidth > $viewPortW){
            $src_x = abs(abs($imgX) - abs(($imgW - $pWidth) / 2));
            $dst_x = 0;
        }else{
            $src_x = 0;
            $dst_x = $imgX + (($imgW - $pWidth) / 2); 
        }
        if($pHeight > $imgH){
            $src_y = abs($imgY - abs(($imgH - $pHeight) / 2));
            $dst_y = 0;
        }else{
            $src_y = 0;
            $dst_y = $imgY + (($imgH - $pHeight) / 2); 
        }
        $viewport = imagecreatetruecolor($viewPortW,$viewPortH);
        $this->setTransparency($image_p,$viewport,$ext); 
        imagecopy($viewport, $image_p, $dst_x, $dst_y, $src_x, $src_y, $pWidth, $pHeight);
        imagedestroy($image_p);
        
        
        $selector = imagecreatetruecolor($selectorW,$selectorH);
        $this->setTransparency($viewport,$selector,$ext);
        imagecopy($selector, $viewport, 0, 0, $selectorX, $selectorY,$viewPortW,$viewPortH);
        
        $file = $fileNameSave?$fileNameSave:"../tmp/resize-crop-".time().".".$ext;
        $this->parseImage($ext,$selector,$file);
        imagedestroy($viewport);
        //Return value
        return $file;
        /* Functions */
    }
}
?>