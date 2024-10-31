<?php
/*
Plugin Name: Responsive Flicker Widget
Plugin URI:  http://www.freewebmentor.com/2016/09/responsive-flicker-widget.html
Description: A Flickr WordPress plugin to display user's and group's photos in sidebar widgets.
Author: Prem Tiwari
Version: 1.0
Author URI: http://freewebmentor.com/
*/

class FMFW_Widget_Flickr extends WP_Widget {

    /*----------------------------------------
      * The constructor. Sets up the widget.
    ----------------------------------------*/

    function __construct() {

        /* Widget settings. */
        $widget_ops = array( 'classname' => 'widget_fmfw_flickr', 'description' => __( 'This Flickr widget populates photos from your Flickr ID.', 'freewebmentor' ) );

        /* Widget control settings. */
        $control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'fwm_flickr' );

        /* Create the widget. */
        parent::__construct( 'fwm_flickr', __('FWM - Flickr', 'freewebmentor' ), $widget_ops, $control_ops );

    } // End Constructor


    /*----------------------------------------
      * Displays the widget on the frontend.
    ----------------------------------------*/

    function widget( $args, $instance ) {

        $html = '';

        extract( $args, EXTR_SKIP );

        /* Our variables from the widget settings. */
        $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );

        $number = ! empty( $instance['number'] ) ? $instance['number'] : 5;
        $id = ! empty( $instance['id'] ) ? $instance['id'] : '';
        $sorting = ! empty( $instance['sorting'] ) ? $instance['sorting'] : '';
        $type = ! empty( $instance['type'] ) ? $instance['type'] : '';
        $size = ! empty( $instance['size'] ) ? $instance['size'] : '';

        /* Before widget (defined by themes). */
        echo $before_widget;

        /* Use the default title if no title is set. */
        if ( ! $title ) { $title = __( 'Photos on', 'freewebmentor' ) . ' <span>flick<span>r</span></span>'; }

        /* Display the widget title if one was input (before and after defined by themes). */
        if ( $title ) {

            echo $before_title . $title . $after_title;

        } // End IF Statement

        do_action( 'widget_fmfw_flickr_top' );

        $html = '';

        /* Construct the remainder of the query string, using only the non-empty fields. */
        $fields = array(
                        'count'     => $number,
                        'display'   => $sorting,
                        'source'    => $type,
                        $type       => $id,
                        'size'      => $size
                    );

        $query_string = '';

        foreach ( $fields as $k => $v ) {
            if ( $v == '' ) {} else {
                $query_string .= '&amp;' . $k . '=' . $v;
            }
        }

        $html .= '<div class="wrap">' . "\n";
            $html .= '<div class="fix"></div><!--/.fix-->' . "\n";
                $html .= '<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?layout=x' . $query_string . '"></script>' . "\n";
            $html .= '<div class="fix"></div><!--/.fix-->' . "\n";
        $html .= '</div><!--/.wrap-->' . "\n";

        echo $html;

        // Add actions for plugins/themes to hook onto.
        do_action( 'widget_fmfw_flickr_bottom' );

        /* After widget (defined by themes). */
        echo $after_widget;

    } // End widget()

   /*----------------------------------------      
      * Function to update the settings from the form() function.
      * - Array $new_instance
      * - Array $old_instance
    ----------------------------------------*/

    function update ( $new_instance, $old_instance ) {
        $settings = array();

        foreach ( array( 'title', 'id', 'type', 'sorting', 'size' ) as $setting ) {
            if ( isset( $new_instance[$setting] ) ) {
                $settings[$setting] = sanitize_text_field( $new_instance[$setting] );
            }
        }

        foreach ( array( 'number' ) as $setting ) {
            if ( isset( $new_instance[$setting] ) ) {
                $settings[$setting] = absint( $new_instance[$setting] );
            }
        }

        return $settings;
    } // End update()

   /*----------------------------------------
      * The form on the widget control in the widget administration area.
      * - Array $instance
    ----------------------------------------*/

   function form( $instance ) {

       /* Set up some default widget settings. */
        $defaults = array(
                        'title' => '',
                        'id' => '',
                        'number' => '',
                        'type' => 'user',
                        'sorting' => 'latest',
                        'size' => 's'
                    );

        $instance = wp_parse_args( (array) $instance, $defaults );
?>
        <!-- Widget Title: Text Input -->
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'freewebmentor' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
        </p>
        <!-- Widget Flickr ID: Text Input -->
        <p>
            <label for="<?php echo $this->get_field_id( 'id' ); ?>"><?php _e( 'Flickr ID (<a href="http://www.idgettr.com">idGettr</a>):', 'freewebmentor' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'id' ); ?>" value="<?php echo $instance['id']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'id' ); ?>" />
        </p>
        <!-- Widget Number: Select Input -->
        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number:', 'freewebmentor' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'number' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>">
                <?php for ( $i = 1; $i <= 10; $i += 1) { ?>
                <option value="<?php echo $i; ?>"<?php selected( $instance['number'], $i ); ?>><?php echo $i; ?></option>
                <?php } ?>
            </select>
        </p>
        <!-- Widget Type: Select Input -->
        <p>
            <label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Type:', 'freewebmentor' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'type' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'type' ); ?>">
                <option value="user"<?php selected( $instance['type'], 'user' ); ?>><?php _e( 'User', 'freewebmentor' ); ?></option>
                <option value="group"<?php selected( $instance['type'], 'group' ); ?>><?php _e( 'Group', 'freewebmentor' ); ?></option>
            </select>
        </p>
        <!-- Widget Sorting: Select Input -->
        <p>
            <label for="<?php echo $this->get_field_id( 'sorting' ); ?>"><?php _e( 'Sorting:', 'freewebmentor' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'sorting' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'sorting' ); ?>">
                <option value="latest"<?php selected( $instance['sorting'], 'latest' ); ?>><?php _e( 'Latest', 'freewebmentor' ); ?></option>
                <option value="random"<?php selected( $instance['sorting'], 'random' ); ?>><?php _e( 'Random', 'freewebmentor' ); ?></option>
            </select>
        </p>
        <!-- Widget Size: Select Input -->
        <p>
            <label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Size:', 'freewebmentor' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'size' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'size' ); ?>">
                <option value="s"<?php selected( $instance['size'], 's' ); ?>><?php _e( 'Square', 'freewebmentor' ); ?></option>
                <option value="m"<?php selected( $instance['size'], 'm' ); ?>><?php _e( 'Medium', 'freewebmentor' ); ?></option>
                <option value="t"<?php selected( $instance['size'], 't' ); ?>><?php _e( 'Thumbnail', 'freewebmentor' ); ?></option>
            </select>
        </p>
<?php
    } // End form()

} // End Class


/*----------------------------------------
  Register the widget on 'widgets_init'.  
----------------------------------------*/

add_action( 'widgets_init', create_function( '', 'return register_widget("FMFW_Widget_Flickr");' ), 1 );