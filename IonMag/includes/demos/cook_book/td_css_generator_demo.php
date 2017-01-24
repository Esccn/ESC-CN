<?php
/**
 * Created by ra.
 * Date: 9/2/2015
 * CSS generator for this specific demo
 */


function td_css_demo_gen() {
	$td_demo_custom_css = "
	<style>
		/* @theme_color */
      	.td-cook-book .td-scroll-up {
            background-color: @theme_color;
        }
      	.td-cook-book .block-title,
      	.td-cook-book .td-scroll-up {
      	    border-color: @theme_color;
      	}
      	
      	/* @text_header_color */
        div .block-title label,
        div .block-title span,
        div .block-title a {
          color: @text_header_color !important;
        }
	</style>
	";

	$td_demo_css_compiler = new td_css_compiler($td_demo_custom_css);
	$td_demo_css_compiler->load_setting('theme_color');
    $td_demo_css_compiler->load_setting('text_header_color');

	return $td_demo_css_compiler->compile_css();
}
