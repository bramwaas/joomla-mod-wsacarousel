# Joomla module WsaCarousel
Slider/Carousel based on  dj-imageslider and bootstrap carousel
DJ-imageslider of DJ-Extensions is fine slider module, but in horizontal transitions with more than one slide visible the transition from end to start is not so nice, the description says also that a continuous loop not possible is.
The bootstrap carousel does with some extra CSS exactly what I  want, so I have made the output of this module more like the bootstrap carousel.
Copied from module DJ-imageslider than pasted the bootstrap carousel functionallity with continuous loop in it.
Added support for more than one visual slide in bootstrap carousel by adding Javescript and Css.

## Module Features

* Get images from a directory or from WsaCarousel component as source.
* Loop continuously automatic or manually. Optional buttons for play/pause or navigation by next/previous arrows or indicators.
* Set transition time and delay between transitions. 
* When using slides from component.Set caption with title and short description. Set interval (= transition time + delay) for individual slide (works only in bootstrap 4 and 5).   
* Stretch slider to full width (of parent container) wile keeping aspect ratio's from width and height, or fix width and height.
* Set number of visual slides. (fall back to 1 on small browser windows)
* Choose bootstrap version 3, 4 or 5. Add module javascript and CSS or use bootstrap javascript and css that is already available.
* Fit size of image in available width or height or both (contain)

## Installation
* Do the usual setup procedure you know downloading unpacking uploading activating.
* or install from url using the download source code url of the release in github. 
* ..
* Fill out all the necessary configuration fields.
* You're done!

## Documentation

**Slider/Carousel based on bootstrap carousel.**  
To work with more than one visual slide we group the slides in frames that 100 / (#visual slides) percentage.  
Each frame contains the starting slide of its position and so many folowing slides as necesary to fill the number of visual images.  
So if we have 5 slides and 3 visual slides:  
Frame 1 has slide 1,2,3  
Frame 2 ... 2,3,4  
Frame 3 ... 3,4,5  
Frame 4 ... 4,5,1  
Frame 5 ... 5,1,2  
First is only Frame 1 displayed other frames are hidden.  
At transition start Frame 2 is displayed over Frame 1 translated 33% to the right.  
During transition Both frames are translated 33% to the left.  
At transition end Frame 1 is hidden.  
And so forth

In earlier versions the frame were created by inline javascript in a jQuery(document).ready(function(). From version 1.1.0 the frames are created in php while building the html.
Inline css is used in all version to override the default 100% translation in the transition to the percentage needed for the number of visual slides.
There is also inline css used for other functionalities.

**Browser compatibility issues.**  
Bootstrap 5 is not compatible with IE10 and IE11 and maybe some other older browsers due to the use of more modern javascript.
From version 1.1.0 we use the aspect-ratio css property to limit the overflow height of pictures that are too high (have a smaller aspect-ratio than the slide) when fit size to width or auto in full width slider. This is not supporterd in IE and some older (mobile phone) browsers. So for support of those you should not use pictures that are too high in full width slider. (In older versions we did not limit that overflow at all in full width).
In IE and bootstrap 3 the old frame seems to be removed to fast I have not found the reason. So if you want maximum quality also on IE I would suggest using bootstrap 4.  

**Other issues.**  
A user mentioned big distortions of slides when fast clicking on navigation arrows when using chrome. I could not reproduce that bug entirely so I am afraid that it will still be present on some configurations.   
When a slide is partially transparent you can see the old frame through the new frame during the transition. In places where the opacity is less than 100%, the opacity increases. (caption) texts can also fade because the frames do not shift completely synchronously. To prevent this, you can set a color for the slide background. For example the color of the carousel background.

**Dependencies.**    
Bootstrap carousel 3 uses javascript and jquery.js  
Bootstrap carousel 4 uses javascript and jquery.js and popper.js   
Bootstrap carousel 5 only uses javascript but not the version IE provides.   
Magnific popup uses javascript and jquery.js   

(This module is a Fork of version 3.2.3 of DJ-ImageSlider by DJ-Extensions,
https://extensions.joomla.org/extension/dj-imageslider/)

## Copyright and License

This project is licensed under the [GNU GPL], version 3 or later.
2018&thinsp;&ndash;&thinsp;2022 &copy; [Bram Waasdorp](http://www.waasdorpsoekhan.nl).

## Changelog

* 0.0 20180131 imported V3.2.3 of DJ-imageslider (https://extensions.joomla.org/extension/dj-imageslider/) by DJ-Extensions,
and renamed some files and reference.
* 0.0.9 For bootstrap 4 carousel changed in wsacarousel to make it independent and to remove conflict in duration.  
* 0.2.0 For bootstrap 4 now using 4.3.1. In this version possibillity to change transition-duration is in standard so there is no need for own code to change this duration anymore. In 4.3.1 is also an option for individiual intervals per slide. I will implement this in one of the next versions of this module.
see: https://getbootstrap.com/docs/4.3/components/carousel/  
* 1.0.0 first version that is correct working with IE10 and IE11
* 1.0.6 tested with Joomla 4
* 1.0.7 24-28 -2-2022 solved some bugs with dimensions and navigation and added an extra option (showBoth on mouse over) added standard indicators and  play/pauze buttons.
* 1.0.8 6-3-2022 small patch to enable autoplay off
* 1.1.0 31-03-2022 added support for bootstrap 5. Reviewed and tested options. Removed unused options and made the other options work. Made adjustments in file-structure and used functions to comply better with Joomla 4 namespaced classes and new functions to add Css and Javascript. Added script to delete files that are become unnecessary.

