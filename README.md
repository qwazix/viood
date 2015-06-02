# This is a responsive gallery website

It doesn't do much. It recurses into the filesystem of the *photos* folder and displays a link to a gallery if it finds a folder, or a link to a photo if it finds a photo.

## Features

* Thumbnailing with cache
* Swipe-able galleries
* Keyboard arrows to navigate gallery
* HTML5 history enabled: each photo has it's own unique link for sharing without polluting your history. Browser history back is hierarchy-based
* HTML5 full screen
* Context-aware back button for full-screen operation: history back when navigating the website to avoid polluting the history, hierarchical when coming from external links.

## How to use

* rename photos-sample to photos
* create folders
* create `gallery.json` in each folder (optional) with the following structure
```
     {
         "name": "gallery1",
         "description":"a test gallery",
         "flagship":"_DSC4316.jpg",
         "ignore" : ["_DSC4307.jpg"]
     }
```
(all fields are optional)

## Demo

* A running instance of viood can be found at my personal [website](http://qwazix.com/viood)
