# Joomla module WsaCarousel
Slider/Carousel based on  dj-imageslider and bootstrap carousel
DJ-imageslider of DJ-Extensions is fine slider module, but in horizontal transitions with more than one slide visible the transition from end to start is not so nice, the description says also that a continuous loop not possible is. The bootstrap carousel does with some extra CSS exactly what I  want, so I have made the output of this module more like the bootstrap carousel.
Copied from DJ-imageslider to give it a continuous loop

## Module Features

* 
*

## Installation
* Do the usual setup procedure… you know… downloading… unpacking… uploading… activating. 
* ..
* Fill out all the necessary configuration fields.
* You’re done!

## Documentation
* ...
*

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
