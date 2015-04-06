# CONTENT ELEMENTS

[Image With Text](#image_w_text)  
[Text Block](#textblock)  
[Fashion All Access](#fashion_all_access)  
[Fashion Backstage](#fashion_backstage)  
[Fashion Slideshow](#fashion_slideshow)  
[Fashion Video](#fashion_video)  
[Image Carousel](#image_carousel)  
[Image Carousel with Thumb Nav](#image_thumb_carousel)  
[Product Carousel](#product_carousel)  
[Celebrity Carousel](#celebrity_carousel)  
[Celebrity Slideshow](#celebrity_slideshow)  
[Section Divider](#divider)  
[Related Articles](#related)  
[Trend](#trend)  


<a name="image_w_text"/>
## Image With Text - imageWithText.php
Most used content element, on landing pages and article pages with gridded content.

### Image Size
This content element is aware of the layout element it is dropped into, using the
attribute "layout_size". When the "attachement" attribute is available, the content element
will determine which image size crop to serve depending on the layout_size.

### Content Card
The style of the content card can be customized using the "card_type" attribute.
Currently there are six styles:
- White Block
- Thin Stroke Outline
- Text Overlay
- Article Hero Text Overlay - used for text overlay in hero images of Spotlight On Kors Collaborator articles
- Article Collaborator White Block - used for text overlay in images of Spotlight On Kors Collaborator articles (pg 73 in DK_ALL_DESIGNS_012414.pdf)
- Get The Look - used for text caption in bottom of image in Spotlight On Kors Collaborator articles (pg 74 in DK_ALL_DESIGNS_012414.pdf)

The attribute "parent_type" is also used for additional style of the content card.
There are distinct style attributed to card styles for home page, landing pages, regarding the layout of the category heading, font size and font style. A white block card type is different on the home page than on the landing page.

Currently there are six styles:
- home - used for any time the content element is to be used on home page.
- full-width-hero - used when the image is used as a full width hero of an article
- category - used when a content element is used on the main landing page
- subcategory - this is currenty not used and can be removed
- shopnow - used when a content element is used to display a shop now cta, seen in Michaels Must Have (pg 47 in DK_ALL_DESIGNS_012414.pdf)
- none

<a name="textblock"/>
## Text Block - textblock.php
Used for article content, and general text content

<a name="fashion_all_access"/>
## Fashion All Access - sw_fashion_all_access.php
Used in the Fashion Runway articles. It is similar to Image with Text, except links will open articles in modal window.
There is an associated backbone app with this content element

<a name="fashion_backstage"/>
## Fashion Backstage - sw_fashion_backstage_slideshow.php
Used in the Fashion Runway articles. It will display an image with title and a link over it. A slideshow is associated with it and will open upon clicking the link.
Currently shares the same javascript as Celebrity Slideshow (found in main.js). May need to distinguish the javascript for this content element's slideshow.

<a name="fashion_slideshow"/>
## Fashion Slideshow - sw_fashion_slideshow.php
Used in the Fashion and Lookbook articles for the lookbook slideshow.
It will display the 7 images in a row layout on the article page, and upon click, will display the full slideshow.
There is an associated backbone app with this content element.

<a name="fashion_video"/>
## Fashion Video - sw_fashion_video.php
Used in Fashion Runway articles. Currently embeds youtube videos.

<a name="image_carousel"/>
## Image Carousel - sw_image_carousel.php
Used in the home page hero carousel and article carousels

<a name="image_thumb_carousel"/>
## Image Carousel with Thumb Nav - sw_thumb_nav_carousel.php
Used in places where carousels have a thumb navigation, primarily Ad Campaigns and All Access articles with slideshows

<a name="product_carousel"/>
## Product Carousel - sw_product_carousel.php
Used in articles with product carousels, such as pg 45 and pg103 in DK_ALL_DESIGNS_012414.pdf

<a name="celebrity_carousel"/>
## Celebrity Carousel - sw_celebrity_carousel.php
Used in on home page bottom Celebrity carousel.

<a name="celebrity_slideshow"/>
## Celebrity Slideshow - sw_celebrity_slideshow.php
Used for celebrity slideshows articles.

<a name="divider"/>
## Section Divider - sw_section_divider.php
Used as section divider (gradient bar) with optional title

<a name="related"/>
## Related Articles - relatedArticles.php
Used in any articles.




<a name="trend"/>
## Trend Carousel - sw_trend.php

