<?php

namespace AppBundle\Service;

class ResizeImages
{

    var $image;
    var $image_type;

    function load($filename)
    {
        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];
        if ($this->image_type == IMAGETYPE_JPEG) {
            $this->image = imagecreatefromjpeg($filename);
        } elseif ($this->image_type == IMAGETYPE_GIF) {
            $this->image = imagecreatefromgif($filename);
        } elseif ($this->image_type == IMAGETYPE_PNG) {
            $this->image = imagecreatefrompng($filename);
        }
    }

    function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 100, $permissions = null)
    {
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image, $filename, $compression);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->image, $filename);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($this->image, $filename);
        }
        if ($permissions != null) {
            chmod($filename, $permissions);
        }
    }

    function getWidth()
    {
        return imagesx($this->image);
    }

    function getHeight()
    {
        return imagesy($this->image);
    }

    public function resizeSponsor($newwidth, $newheight, $fileSave)
    {
        $w = $newwidth;
        $h = $newheight;
        $width = $this->getWidth();
        $height = $this->getHeight();
        if ($width <= $newwidth && $height <= $newheight) { // Если картинка меньше чем нужно
            $offset_width = $newwidth / 2;
            $offset_oldwidth = $width / 2;
            $offset_width -= $offset_oldwidth;

            $offset_height = $newheight / 2;
            $offset_oldheight = $height / 2;
            $offset_height -= $offset_oldheight;
            $destination_resource = imagecreatetruecolor($newwidth, $newheight);
            imagealphablending($destination_resource, false);
            imagesavealpha($destination_resource, true);
            $white = imagecolorallocate($destination_resource, 255, 255, 255);
            imagefill($destination_resource, 0, 0, $white);
            imagecopyresampled($destination_resource, $this->image, $offset_width, $offset_height, 0, 0, $width, $height, $width, $height);
            $this->image = $destination_resource;
            $this->save($fileSave, $this->image_type);
        } elseif ($width > $newwidth || $height > $newheight) {
            $ratio_orig = $width / $height;
            if ($newwidth / $newheight > $ratio_orig) {
                $newwidth = $newheight * $ratio_orig;
            } else {
                $newheight = $newwidth / $ratio_orig;
            }
            $destination_resource = imagecreatetruecolor($newwidth, $newheight);
            imagealphablending($destination_resource, false);
            imagesavealpha($destination_resource, true);
            $white = imagecolorallocate($destination_resource, 255, 255, 255);
            imagefill($destination_resource, 0, 0, $white);
            imagecopyresampled($destination_resource, $this->image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            $this->image = $destination_resource;
            $this->insertingPictureStroke($w, $h, $fileSave);
        }

    }

    public function resizeSpeakers($newwidth, $newheight, $fileSave)
    {
        $w = $newwidth;
        $h = $newheight;
        $newwidth_out = $newwidth;
        $newheight_out = $newheight;
        $width = $this->getWidth();
        $height = $this->getHeight();

        $ratio_orig = $width / $height;
        $ratio_new = $newwidth / $newheight;

        if ($ratio_new > $ratio_orig) { // если меньше по горизонтали
            $newwidth = $newheight * $ratio_new;
            $newheight = $newwidth / $ratio_orig;
            $offset_width = 0;
        } else {
            $newwidth = $newheight / $ratio_new;
            $newheight = $newwidth / $ratio_orig;

            $offset_width = $w / 2;
            $offset_oldwidth = $width / 2;
            $offset_width -= $offset_oldwidth;
        }
        $destination_resource = imagecreatetruecolor($newwidth_out, $newheight_out);
        imagealphablending($destination_resource, false);
        imagesavealpha($destination_resource, true);
        $white = imagecolorallocate($destination_resource, 255, 255, 255);
        imagefill($destination_resource, 0, 0, $white);
        imagecopyresampled($destination_resource, $this->image, $offset_width, 0, 0, 0, $newwidth, $newheight, $width, $height);
        $this->image = $destination_resource;
        $this->save($fileSave, $this->image_type);

    }


    public function insertingPictureStroke($newwidth, $newheight, $fileSave)
    {
        $width = $this->getWidth();
        $height = $this->getHeight();
        if ($width <= $newwidth && $height <= $newheight) {
            $offset_width = $newwidth / 2;
            $offset_oldwidth = $width / 2;
            $offset_width -= $offset_oldwidth;

            $offset_height = $newheight / 2;
            $offset_oldheight = $height / 2;
            $offset_height -= $offset_oldheight;
            $destination_resource = imagecreatetruecolor($newwidth, $newheight);
            imagealphablending($destination_resource, false);
            imagesavealpha($destination_resource, true);
            $white = imagecolorallocate($destination_resource, 255, 255, 255);
            imagefill($destination_resource, 0, 0, $white);
            imagecopyresampled($destination_resource, $this->image, $offset_width, $offset_height, 0, 0, $width, $height, $width, $height);
            $this->image = $destination_resource;
            $this->save($fileSave, $this->image_type);
        }
    }

}

?>