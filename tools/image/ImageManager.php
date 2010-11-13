<?php
   /**
    * <!--
    * This file is part of the adventure php framework (APF) published under
    * http://adventure-php-framework.org.
    *
    * The APF is free software: you can redistribute it and/or modify
    * it under the terms of the GNU Lesser General Public License as published
    * by the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
    *
    * The APF is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   /**
    * @package tools::image
    * @class ImageManager
    *
    * Provides methods to manipulate images.
    *
    * @author Christian Sch�fer
    * @version
    * Version 0.1, 08.09.2003<br />
    * Version 0.2, 17.09.2004<br />
    * Version 0.3, 21.01.2006<br />
    * Version 0.4, 06.03.2007 (Several code changes)<br />
    * Version 0.5, 31.03.2007 (Refactoring and added PNG support)<br />
    */
   class ImageManager {

      private function ImageManager(){
         // utility class
      }

      /**
       * @public
       * @static
       *
       * Returns information about an image. The return list contains the following offsets:
       * <ul>
       *   <li>width: the width of the image</li>
       *   <li>height: the height of the image</li>
       *   <li>type: the type of the image</li>
       *   <li>mimetype: the mime type of the image</li>
       *   <li>bitdepth: the bitdepth of the image</li>
       *   <li>colormode: the color mode (RGB or CMYK)</li>
       * </ul>
       * If the second argument contains a image attribute, the value is returned instead of a list!
       *
       * @param string $image a full qualified image path.
       * @param string $attributeName the name of the attribute, that should be returned.
       * @return string[] The attributes of an image.
       * @throws InvalidArgumentException In case the applied image or the applied attribute does not exist.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 22.11.2004<br />
       * Version 0.2, 15.07.2006 (Added the extension in the attributes list; added another algo to guess the extension)<br />
       * Version 0.3, 31.01.2009 (Refactoring of the method. Now only the relevant image indicators are returned)<br />
       * Version 0.4, 01.02.2009 (Added a check, if the channel attribute is returned by getimagesize())<br />
       */
      static function getImageAttributes($image,$attributeName = null){

         // check if the image is present on disk
         if(!file_exists($image)){
            throw new InvalidArgumentException('[ImageManager::showImageAttributes()] The given '
                    .'image ("'.$image.'") does not exist! Hence, no attributes can be analyzed.',
                    E_USER_ERROR);
         }

         // declare image flags
         $flags[1] = 'gif';
         $flags[2] = 'jpg';
         $flags[3] = 'png';
         $flags[4] = 'swf';

         // initialize the return list
         $imageAttributes = array();

         // analyze the image attributes
         $attributes = getimagesize($image);

         // image define the image dimensions
         $imageAttributes['width'] = $attributes[0];
         $imageAttributes['height'] = $attributes[1];

         // define the image type
         $imageAttributes['type'] = $flags[$attributes[2]];

         // define the mime type
         if(isset($attributes['mime'])){
            $imageAttributes['mimetype'] = $attributes['mime'];
          // end if
         }

         // define the bit depth
         if(isset($attributes['bits'])){
            $imageAttributes['bitdepth'] = $attributes['bits'];
          // end if
         }

         // define the color mode
         if(isset($attributes['channels'])){

            if($attributes['channels'] == '3'){
               $imageAttributes['colormode'] = 'RGB';
             // end if
            }
            else{
               $imageAttributes['colormode'] = 'CMYK';
             // end else
            }

          // end if
         }

         // return attribute
         if($attributeName !== null){

            if(isset($imageAttributes[$attributeName])){
               return $imageAttributes[$attributeName];
            } else {
               throw new InvalidArgumentException('[ImageManager::getImageAttributes()] The desired image attribute ("'.$attributeName.'") does not exist!');
            }

         }

         // return the complete list
         return $imageAttributes;

       // end function
      }

      /**
       * @public
       * @static
       *
       * Resizes an image to the given dimensions. If a target image is given, the file is saved to
       * the desired file.
       *
       * @param string $sourceImage full qualified path to the image file.
       * @param int $width width of the resized image.
       * @param int $height height of the resized image.
       * @param string $targetImage full qualified path to the target image.
       * @param int $jpgQuality the jpg quality (0-100).
       * @throws InvalidArgumentException In case the applied image does not exist.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 31.01.2009<br />
       */
      static function resizeImage($sourceImage,$width,$height,$targetImage = null,$jpgQuality = 80){

         // check if the image is present on disk
         if(!file_exists($sourceImage)){
            throw new InvalidArgumentException('[ImageManager::resizeImage()] The given image ("'
                    .$sourceImage.'") does not exist! Hence, it cannot be resized.',E_USER_ERROR);
         }

         // gather the current dimensions of the image
         $attributes = ImageManager::getImageAttributes($sourceImage);
         $sourceImageWidth = $attributes['width'];
         $sourceImageHeight = $attributes['height'];
         $sourceImageType = $attributes['type'];

         // create the current and the target image stream
         if($sourceImageType == 'jpg'){
            $sourceImageStream = imagecreatefromjpeg($sourceImage);
            $targetImageStream = imagecreatetruecolor($width,$height);
          // end if
         }
         elseif($sourceImageType == 'gif'){
            $sourceImageStream = imagecreatefromgif($sourceImage);
            $targetImageStream = imagecreate($width,$height);
          // end
         }
         else{
            $sourceImageStream = imagecreatefrompng($sourceImage);
            $targetImageStream = imagecreate($width,$height);
          // end else
         }

         // copy transparency if we resize a gif image
         if($sourceImageType == 'gif'){

            // query the transparency color
            $transparentColor = imagecolortransparent($sourceImageStream);

            // copy parlette
            imagepalettecopy($targetImageStream,$sourceImageStream);

            // fill with transparent color
            imagefill($targetImageStream,0,0,$transparentColor);

            // declare the transparent color as transparent :)
            imagecolortransparent($targetImageStream,$transparentColor);

          // end if
         }

         // copy source image stream to target image stream and resize it
         imagecopyresized($targetImageStream,$sourceImageStream,0,0,0,0,$width,$height,$sourceImageWidth,$sourceImageHeight);

         // save image (if desired) or flush it to stdout
         if($sourceImageType == 'jpg'){

            if($targetImage === null){
               imagejpeg($targetImageStream,'',$jpgQuality);
             //end if
            }
            else{
               imagejpeg($targetImageStream,$targetImage,$jpgQuality);
             // end else
            }

          // end if
         }
         elseif($sourceImageType == 'gif'){

            if($targetImage === null){
               imagegif($targetImageStream);
             //end if
            }
            else{
               imagegif($targetImageStream,$targetImage);
             // end else
            }

          // end
         }
         else{

            if($targetImage === null){
               imagepng($targetImageStream);
             //end if
            }
            else{
               imagepng($targetImageStream,$targetImage);
             // end else
            }

          // end else
         }

         // cleam memory
         imagedestroy($targetImageStream);
         imagedestroy($sourceImageStream);

       // end function
      }

    // end class
   }
?>