<?php
// add editor css
add_filter('mce_css', 'sweden_mce_style');
function sweden_mce_style($url) {
    if (!empty($url)) $url .= ',';
    $url .= THEME_URL . '/app/MKD/editor-style.css';

    return $url;
}
add_filter('mce_css', 'sweden_mce_font');
function sweden_mce_font($url) {
    if (!empty($url)) $url .= ',';
    //Gotham
    $url .= '//cloud.typography.com/6741072/753722/css/fonts.css';

    return $url;
}
add_filter('mce_css', 'sweden_mce_myfont');
function sweden_mce_myfont($url) {
    if (!empty($url)) $url .= ',';
    // $url .= '/wp-content/themes/destinationkors/static/dist/css/MyFontsWebfontsKit.css';

    // Caslon, now from fonts.com rather than MyFonts
    $url .= "//fast.fonts.net/cssapi/5fc9e127-60f5-4dc8-9f5e-179901c7cf31.css";

    return $url;
}
add_filter("mce_external_plugins", "sweden_mce_lb");

function sweden_mce_lb($plugin_array) {
	$plugin_array['sw_lineBreak'] = '/wp-content/themes/destinationkors/app/Framework/assets/js/editor-buttons.js';
	return $plugin_array;
}

// add dropdown
add_filter('mce_buttons', 'sweden_mce_font_buttons');
function sweden_mce_font_buttons($buttons) {
    array_unshift($buttons, 'styleselect');
    array_unshift($buttons, 'sw_lineBreak');

    return $buttons;
}

// http://tinymce.moxiecode.com/wiki.php/Configuration
add_filter('tiny_mce_before_init', 'sweden_mce_config');


function sweden_mce_config($init) {
    // Don't remove line breaks
    $init['remove_linebreaks'] = false;
    $init['force_p_newlines'] = false;
    $init['convert_fonts_to_spans'] = true;

        $init['block_formats'] = 'Article Head=articleHeader; \
                                  Article Lead=articleLead; \
                                  Article Pull Quote=pullQuote; \
                                  Article Subhead v1=articleSubhead1; \
                                  Article Subhead v2=articleSubhead2; \
                                  Article Subhead v3=articleSubhead3; \
                                  Body Caps=bodycaps; \
                                  Category Header=categoryHeader; \
                                  Category Subhead=categorySubhead; \
                                  Content Header 1/3=contentHeader1; \
                                  Content Header 2/3=contentHeader2; \
                                  Credit=credit; \
                                  Gallery Caption Body Caps=galleryCaptionbodyCaps; \
                                  Gallery Caption Body=galleryCaptionbody; \
                                  Gallery Caption Caption=galleryCaptioncaption; \
                                  Gallery Caption Headline=galleryCaptionneadline; \
                                  Gallery Caption Italic=galleryCaptioncaptionItalic; \
                                  Gallery Caption Name=galleryCaptionname; \
                                  Gallery Caption Time=galleryCaptiontime; \
                                  Half Column Headline=columHeadline; \
                                  Home Carousel Headline=homeCarouselHeadline; \
                                  MK Pull Quote=mkPullQuote; \
                                  MMH Article Headline=mmhHeadline; \
                                  MMH Slide Headline=mmhSlideHeadline; \
                                  MMH Slide Subhead=mmhSlideSubhead; \
                                  Must Haves Body=mhBody; \
                                  Must Haves Quote=mhQuote; \
                                  Must Haves Style Tip=mhStyleTip; \
                                  Paragraph Heading=paragraphHeading; \
                                  Photo Caption Category=captionCat; \
                                  Photo Caption Category=captionCategory; \
                                  Photo Caption Name=captionName; \
                                  Product Name=productName; \
                                  Q&A Answer=qaAnswer; \
                                  Q&A Question=qaQuestion; \
                                  Quote=quote; \
                                  Runway Headline=runwayHeadLine; \
                                  Season Quote=seasonQuote; \
                                  Season Title=seasonTitle; \
                                  Section Title=sectionTitle; \
                                  Slug Small=slugSmall; \
                                  Slug Small Inline=slugSmallInline; \
                                  Slug=slug; \
                                  Spotlight Body Caps=spotlightBodyCaps; \
                                  Spotlight Headline=spotlightHeadline; \
                                  Spotlight Quote=spotlightQuote; \
                                  Travel Diary Carousel Body=travelCarouselBody; \
                                  Travel Diary Carousel Headline=travelCarouselHeadline; \
                                  body=body; \
                                  cta=cta; \
                                  Roman Aside=romanAside; \
                                  WHS Header Dek=whsHeaderDek';

    $init['formats'] = "{
            whsHeaderDek: { attributes : {'class' : 'whs-header-dek'}, block: 'div', remove: 'all', exact: true },
            romanAside: { attributes: {'class' : 'roman-aside'}, block: 'div', remove: 'all', exact: true},
            slug: { attributes : {'class' : 'slug'}, block: 'div', remove: 'all', exact: true },
            slugSmall: { attributes : {'class' : 'slug small'}, block: 'div', remove: 'all', exact: true },
            slugSmallInline: { attributes : {'class' : 'slug-small-inline'}, block: 'div', remove: 'all', exact: true },
            cta: { attributes : {'class' : 'cta'}, block: 'div', remove: 'all', exact: true },
            body: { attributes : {'class' : 'body-copy'}, block: 'div', remove: 'all', exact: true },
            homeCarouselHeadline: { attributes : {'class' : 'image-carousel-headline'}, block: 'div', remove: 'all', exact: true },
            bodycaps: { attributes : {'class' : 'body-caps'}, block: 'div', remove: 'all', exact: true },
            categoryHeader: { attributes : {'class' : 'cat-header-headline'}, block: 'h1', remove: 'all', exact: true },
            categorySubhead: { attributes : {'class' : 'cat-header-subhead'}, block: 'div', remove: 'all', exact: true },
            contentHeader1: { attributes : {'class' : 'cat-landing-content-block headline'}, block: 'div', remove: 'all', exact: true },
            contentHeader2: { attributes : {'class' : 'cat-landing-content-block transparent headline'}, block: 'div', remove: 'all', exact: true },
            sectionTitle: { attributes : {'class' : 'section-title'}, block: 'div', remove: 'all', exact: true },
            productName: { attributes : {'class' : 'product-name'}, block: 'div', remove: 'all', exact: true },
            articleHeader: { attributes : {'class' : 'article-header-headline'}, block: 'h1', remove: 'all', exact: true },
            spotlightHeadline: { attributes : {'class' : 'spotlight-headline'}, block: 'h1', remove: 'all', exact: true  },
            articleSubhead1: { attributes : {'class' : 'article-header-block subhead'}, block: 'div', remove: 'all', exact: true },
            articleSubhead2: { attributes : {'class' : 'article-subhead sans'}, block: 'div', remove: 'all', exact: true },
            articleSubhead3: { attributes : {'class' : 'article-subhead serif'}, block: 'div', remove: 'all', exact: true },
            articleLead: { attributes : {'class' : 'article-lead'}, block: 'div', remove: 'all', exact: true },
            columHeadline: { attributes : {'class' : 'article-header-block headline'}, block: 'div', remove: 'all', exact: true },
            travelCarouselHeadline: { attributes : {'class' : 'travel-carousel travel-headline'}, block: 'div', remove: 'all', exact: true },
            travelCarouselBody: { attributes : {'class' : 'travel-carousel body-copy'}, block: 'div', remove: 'all', exact: true },
            galleryCaptionname: { attributes : {'class' : 'gcb-name'}, block: 'div', remove: 'all', exact: true },
            galleryCaptionbody: { attributes : {'class' : 'gcb-body'}, block: 'div', remove: 'all', exact: true },
            galleryCaptionbodyCaps: { attributes : {'class' : 'gcb-body-caps'}, block: 'div', remove: 'all', exact: true },
            galleryCaptiontime: { attributes : {'class' : 'gcb-time'}, block: 'div', remove: 'all', exact: true },
            galleryCaptionneadline: { attributes : {'class' : 'gcb-headline'}, block: 'div', remove: 'all', exact: true },
            galleryCaptioncaption: { attributes : {'class' : 'gcb-caption'}, block: 'div', remove: 'all', exact: true },
            galleryCaptioncaptionItalic: { attributes : {'class' : 'gcb-caption-italic'}, block: 'div', remove: 'all', exact: true },
            captionCat: { attributes : {'class' : 'photo-caption photo-caption-cat'}, block: 'div', remove: 'all', exact: true },
            captionName: { attributes : {'class' : 'photo-caption photo-caption-name'}, block: 'div', remove: 'all', exact: true },
            paragraphHeading: { attributes: {'class':'paragraph-heading'}, block: 'div', remove: 'all', exact: true},
            mkPullQuote: { attributes: {'class' : 'mk-quote'}, block: 'blockquote', remove: 'all', wrapper: true, exact: true },
            pullQuote: { attributes : {'class' : 'article-pull-quote'}, block: 'div', remove: 'all', exact: true },
            quote: { attributes : {'class' : 'quote'}, block: 'div', remove: 'all', exact: true },
            spotlightQuote: { attributes : {'class' : 'spotlight-quote'}, block: 'div', remove: 'all', exact: true },
            spotlightBodyCaps: { attributes : {'class' : 'spotlight-body'}, block: 'div', remove: 'all', exact: true },
            mmhHeadline: { attributes : {'class' : 'mmh-headline'}, block: 'div', remove: 'all', exact: true },
            mmhSlideHeadline: { attributes : {'class' : 'mmh-slide-headline'}, block: 'div', remove: 'all', exact: true },
            mmhSlideSubhead: { attributes : {'class' : 'mmh-slide-subhead'}, block: 'div', remove: 'all', exact: true },
            mhQuote: { attributes : {'class' : 'must-haves-quote'}, block: 'blockquote', remove: 'all', exact: true },
            mhStyleTip: { attributes : {'class' : 'must-haves-style-tip'}, block: 'div', remove: 'all', exact: true },
            mhBody: { attributes : {'class' : 'must-haves-body'}, block: 'div', remove: 'all', exact: true },
            qaQuestion: { attributes : {'class' : 'article-qa qa-question'}, block: 'div', remove: 'all', exact: true },
            qaAnswer: { attributes : {'class' : 'article-qa qa-answer'}, block: 'div', remove: 'all', exact: true },
            seasonTitle: { attributes : {'class' : 'fullscreen-season-title'}, block: 'div', remove: 'all', exact: true },
            seasonQuote: { attributes : {'class' : 'fullscreen-season-quote'}, block: 'div', remove: 'all', exact: true },
            runwayHeadLine: { attributes : {'class' : 'runway-headline'}, block: 'div', remove: 'all', exact: true },
            credit: { attributes : {'class' : 'credits-block credit'}, block: 'div', remove: 'all', exact: true },
            alignleft: [
                {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: {textAlign:'left'}},
                {selector: 'img,table,dl.wp-caption', classes: 'alignleft'}
            ],
            aligncenter: [
                {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: {textAlign:'center'}},
                {selector: 'img,table,dl.wp-caption', classes: 'aligncenter'}
            ],
            alignright: [
                {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: {textAlign:'right'}},
                {selector: 'img,table,dl.wp-caption', classes: 'alignright'}
            ],
            strikethrough: {inline: 'del'}

    }";

    //add styles/classes to the "Styles" drop-down
    $style_formats = array(
                array(
                    'title' => _('Case'),
                    'items' => array(
                        array(
                            'title'      => 'Uppercase',
                            'inline'     => 'em',
                            'attributes' => array('class' => 'case-uppercase')
                        ),
                        array(
                            'title'      => 'Lowercase',
                            'inline'     => 'em',
                            'attributes' => array('class' => 'case-lowercase')
                        ),
                        array(
                            'title'      => 'no case',
                            'inline'     => 'em',
                            'attributes' => array('class' => 'case-none')
                        ),
                    )
                ),
                array(
                  'title' => ('Mobile Font Color'),
                  'items' => array(
                    array(
                      'title' => 'Black',
                      'inline' => 'dfn',
                      'attributes' => array('class' => 'm-color-black')
                    ),
                    array(
                      'title' => 'White',
                      'inline' => 'dfn',
                      'attributes' => array('class' => 'm-color-white')
                    )
                  )
                ),
                array(
                    'title' => __('Font'),
                    'items' => array(
                        array(
                                'title' => 'Gotham',
                                'inline' => 'span',
                                'attributes' => array('class' => 'font-gotham')
                        ),
                        array(
                                'title' => 'Gotham Bold',
                                'inline' => 'span',
                                'attributes' => array('class' => 'font-gotham-bold')
                        ),
                        array(
                                'title' => 'Caslon Serif',
                                'inline' => 'span',
                                'attributes' => array('class' => 'font-serif')
                        ),
                        array(
                                'title' => 'Caslon Italic',
                                'inline' => 'span',
                                'attributes' => array('class' => 'font-serif-italic')
                        ),
                    )
                ),
                // array(
                //     'title' => 'Font Size',
                //     'items' => array(
                //         array(
                //                 'title' => 'Font Size 10pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('id' => 'size-10')
                //         ),
                //         array(
                //                 'title' => 'Font Size 12pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('id' => 'size-12')
                //         ),
                //         array(
                //                 'title' => 'Font Size 15pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-15')
                //         ),
                //         array(
                //                 'title' => 'Font Size 16pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-16')
                //         ),
                //         array(
                //                 'title' => 'Font Size 18pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-18')
                //         ),
                //         array(
                //                 'title' => 'Font Size 19pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-20')
                //         ),
                //         array(
                //                 'title' => 'Font Size 20pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-20')
                //         ),
                //         array(
                //                 'title' => 'Font Size 22pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-22')
                //         ),
                //         array(
                //                 'title' => 'Font Size 24pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-24')
                //         ),
                //         array(
                //                 'title' => 'Font Size 26pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-26')
                //         ),
                //         array(
                //                 'title' => 'Font Size 27pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-27')
                //         ),
                //         array(
                //                 'title' => 'Font Size 30pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-30')
                //         ),
                //         array(
                //                 'title' => 'Font Size 35pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-35')
                //         ),
                //         array(
                //                 'title' => 'Font Size 40pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-40')
                //         ),
                //         array(
                //                 'title' => 'Font Size 45pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-45')
                //         ),
                //         array(
                //                 'title' => 'Font Size 55pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-50')
                //         ),
                //         array(
                //                 'title' => 'Font Size 55pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-55')
                //         ),
                //         array(
                //                 'title' => 'Font Size 60pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-60')
                //         ),
                //         array(
                //                 'title' => 'Font Size 65pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-65')
                //         ),
                //         array(
                //                 'title' => 'Font Size 100pt',
                //                 'inline' => 'dfn',
                //                 'attributes' => array('class' => 'size-100')
                //         ),
                //     )
                // ),


    );
    //$init['toolbar1']
    $init['style_formats'] = json_encode( $style_formats );

    return $init;
}

/* TinyMCE style format options at http://www.tinymce.com/wiki.php/Configuration:formats */

// Add custom stylesheet to the website front-end with hook 'wp_enqueue_scripts'
// just using default editor style
/*
add_action('wp_enqueue_scripts', 'sweden_mce_enqueue');
function sweden_mce_enqueue() {
    $StyleUrl = plugin_dir_url(__FILE__).'editor-styles.css';
    wp_enqueue_style( 'swedenMCEStyles', $StyleUrl );
}
*/

// add new widget
add_action( 'widgets_init', create_function( '', "register_widget( 'SW_Widget' );" ) );
// Widget
class SW_Widget extends WP_Widget {

    /**
     * Constructor
     *
     * @return void
     **/
    function SW_Widget() {
        $widget_ops = array( 'classname' => 'extending', 'description' => 'exteding' );
        $this->WP_Widget( 'something', 'blah', $widget_ops );
    }

    /**
     * Outputs the HTML for this widget.
     * WP default
     *
     * @param array
     * @return void echoing output
     **/
    function widget( $args, $instance ) {
        extract( $args, EXTR_SKIP );

        $title = apply_filters('widget_title', empty($instance['title']) ? $parent_title : do_shortcode($instance['title']), $instance, $this->id_base);

        echo $before_widget;
        echo $before_title;
        echo $title;
        echo $after_title;
        ?>
        widget!!!!!!

        <?php
        echo $after_widget;
    }

    /**
     * Deals with the settings when they are saved by the admin.
     * do any validation here.
     *
     * @param array arrays
     * @return array
     **/
    function update( $new_instance, $old_instance ) {

        // update logic goes here
        $updated_instance = $new_instance;

        return $updated_instance;
    }

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array
     * @return void
     **/
    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
        ?>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title', 'sw' ); ?>:</label>
        <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>"class="widefat" style="width:100%;" />
        <?php
    }
}
