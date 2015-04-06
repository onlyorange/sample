<?php
/**
 * Navigation walker. extending native wordress menu for sweden frameworks
 */
class swedenWpNavigationWalker extends Walker_Nav_Menu {

    // adding classes to ul sub-menus
    function start_lvl( &$output, $depth=0, $args = array() ) {
        // depth dependent classes
        $indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); // code indentation :)
        $display_depth = ( $depth + 1); // it counts the first submenu as 0 so +1
        $classes = array(
                'sub-menu',
                ( $display_depth % 2  ? 'menu-odd' : 'menu-even' ),
                ( $display_depth >=2 ? 'sub-sub-menu' : '' ),
                'menu-depth-' . $display_depth
        );
        $class_names = implode( ' ', $classes );

        $output .= "\n" . $indent . '<ul class="' . $class_names . '">' . "\n";
    }

    // adding main/sub classes to li's and links
    function start_el(&$output, $item, $depth = 0, $args = Array(), $id = 0) {
        global $wp_query;
        global $swedenUnlimited;

        $start_tag="<span>";
        $end_tag="</span>";
        $active = "";

        $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // indentation

        $depth_classes = array(
                ( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
                ( $depth >=2 ? 'sub-sub-menu-item' : '' ),
                ( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
                'menu-item-depth-' . $depth
        );
        $depth_class_names = esc_attr( implode( ' ', $depth_classes ) );

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

        if($depth === 0 && $this->is_cat($swedenUnlimited) && $this->cat_match($swedenUnlimited["url_path"], $item->title)){
            $start_tag = "<h1>".$start_tag;
            $end_tag = $end_tag."</h1>";
            $active = " active";
        } else if($depth === 0 && $this->is_parent_cat($swedenUnlimited, $item->title)){
            $active = " active";
        } else if($depth > 0 && $this->is_subcat($swedenUnlimited) && $this->subcat_match($swedenUnlimited, $item->title)){
             $start_tag = "<h1>".$start_tag;
             $end_tag = $end_tag."</h1>";
        }

        $output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';

        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        $attributes .= ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link' : 'main-menu-link' ) . ' '.$active.'"';

        $item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s%6$s%7$s</a>%8$s',
                $args->before,
                $attributes,
                $start_tag,
                $args->link_before,
                apply_filters( 'the_title', $item->title, $item->ID ),
                $args->link_after,
                $end_tag,
                $args->after
        );

        // html!
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    function cat_match($post_type, $title){
        $post_type = trim($post_type);
        switch($post_type){
            case "michaels-edit":
                return ($title === "Michael's Edit");
                break;
            case "jet-set":
                return ($title === "Jet Set");
                break;
            case "runway":
                return ($title === "Runway");
                break;
            case "kors-cares":
                return ($title === "Kors Cares");
                break;

        }
        return false;
    }

    function is_cat($su){
        $count = count( explode( ' ', trim($su["url_path"]) ) );
        return ($count > 1) ? false : true;
    }

    function is_parent_cat($su, $title){
        $post_type = explode(' ', trim($su["url_path"]))[0];
        return $this->cat_match($post_type, $title);
    }

    function is_subcat($su){
        $count = count( explode( ' ', trim($su["url_path"]) ) );
        return ($count === 2) ? true : false;
    }

    function subcat_match($su, $title){
        $post_type = explode( ' ', trim($su["url_path"]) )[1];
        switch($post_type){
            case "travel-diaries":
                return ($title === "Travel Diaries");
                break;
            case "around-the-world":
                return ($title === "Around the World");
                break;
            case "celebrites":
                return ($title === "Celebrities");
                break;
            case "spotlight-on":
                return ($title === "Spotlight On");
                break;
            case "must-haves":
                return ($title === "Michael's Must Haves");
                break;
            case "style-confidential":
                return ($title === "Style Confidential");
                break;
            case "runway-shows":
                return ($title === "Runway Shows");
                break;
            case "lookbooks":
                return ($title === "Lookbooks");
                break;
            case "ad-campaigns":
                return ($title === "Ad Campaigns");
                break;
        }
    }
}

class swedenWpNavigationWalkerMobile extends Walker_Nav_Menu {

    // adding classes to ul sub-menus
    function start_lvl( &$output, $depth=0, $args = array() ) {
        // depth dependent classes
        $indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); // code indentation :)
        $display_depth = ( $depth + 1); // it counts the first submenu as 0 so +1
        $classes = array(
                'sub-menu',
                ( $display_depth % 2  ? 'menu-odd' : 'menu-even' ),
                ( $display_depth >=2 ? 'sub-sub-menu' : '' ),
                'menu-depth-' . $display_depth
        );
        $class_names = implode( ' ', $classes );

        $output .= '<a class="accordian-action" href="#">accordian-action</a>' . "\n";
        $output .= "\n" . $indent . '<ul class="' . $class_names . '">' . "\n";
    }

    // adding main/sub classes to li's and links
    function start_el(&$output, $item, $depth = 0, $args = Array(), $id = 0) {
        global $wp_query;
        $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // indentation

        $depth_classes = array(
                ( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
                ( $depth >=2 ? 'sub-sub-menu-item' : '' ),
                ( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
                'menu-item-depth-' . $depth
        );
        $depth_class_names = esc_attr( implode( ' ', $depth_classes ) );

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

        $output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';

        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        $attributes .= ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link' : 'main-menu-link' ) . '"';

        $item_output = sprintf( '%1$s<a%2$s><span>%3$s%4$s%5$s</span></a>%6$s',
                $args->before,
                $attributes,
                $args->link_before,
                apply_filters( 'the_title', $item->title, $item->ID ),
                $args->link_after,
                $args->after
        );

        // html!
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}
