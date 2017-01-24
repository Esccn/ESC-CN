<?php

class td_block_instagram extends td_block {

	private $atts = array();

	/**
	 * Disable loop block features. This block does not use a loop and it dosn't need to run a query.
	 */
	function __construct() {
		parent::disable_loop_block_features();
	}


    function render($atts, $content = null) {

	    parent::render($atts); // sets the live atts, $this->atts, $this->block_uid, $this->td_query (it runs the query)

	    $this->atts = shortcode_atts(
            array(
                'instagram_id' => '',
            ), $atts);


        if (empty($td_column_number)) {
            $td_column_number = td_global::vc_get_column_number(); // get the column width of the block from the page builder API
        }

        $buffy = ''; //output buffer
        $buffy .= '<div class="' . $this->get_block_classes() . ' td-column-' . $td_column_number . '" ' . $this->get_block_html_atts() . '>';

		    //get the block css
		    $buffy .= $this->get_block_css();

            // block title wrap
            $buffy .= '<div class="td-block-title-wrap">';
                $buffy .= $this->get_block_title(); //get the block title
            $buffy .= '</div>';

		    // For tagDiv composer add a placeholder element
	        if ((td_util::tdc_is_live_editor_iframe() || td_util::tdc_is_live_editor_ajax()) && '' === $this->atts['instagram_id']) {
		        $buffy .= '<div class="td-block-missing-settings">Instagram block <strong>Instagram ID</strong> is empty. Configure this block and enter a valid Instagram id.</div>';
	        }

            $buffy .= '<div id=' . $this->block_uid . ' class="td-instagram-wrap">';
                $buffy.= td_instagram::render_generic($atts);
            $buffy .= '</div>';
        $buffy .= '</div> <!-- ./block -->';
        return $buffy;
    }
}