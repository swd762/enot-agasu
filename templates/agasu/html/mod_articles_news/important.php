<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_news
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<script>
	$(document).ready(function(){
	var container = ".important-news-block";
	var item = ".imp-news-item";
	var wrapper = ".imp-news-flash";
	var gap = 55;
	var item_count = $(item).length;
	var left = $(container).find('.left-slide');
	var right = $(container).find('.right-slide');
	var isDown = false;
	var timeout = 50;
	var step = 40;
	var timeoutId;
	var shift = 0;
	
	var container_width = $(container).width();
	var wrapper_width = ($(item).width() + gap) * item_count - gap;
	
	var max_right_shift = wrapper_width - container_width;
	
	//-gap - у последнего элемента отступа справа нет
	$(wrapper).width(wrapper_width);
	
	mousePress(left, leftShift);
	mousePress(right, rightShift);
	
	
	/*parallax*/
	

	
	function mousePress(obj, func){
		obj.on('mousedown', function(){
			isDown = true;
			timeoutId = setTimeout(func, timeout);
		})
          obj.on('mouseup', function() {							
            isDown = false;
            iteration = 1;
            clearTimeout(timeoutId);
          });
          
          obj.on('mouseleave', function() {							
            isDown = false;
            clearTimeout(timeoutId);
          });		
	}
	
	function leftShift(){
		if(isDown){
			var insideTimeoutId;
			if(shift < 0){
				if(shift > -step){
					shift = 0;
				}
				else{
					shift += step;
					$(wrapper).css({'left' : shift + 'px'});
					insideTimeoutId = setTimeout(leftShift, timeout);					
				}
			}

		}
		else
			clearTimeout(insideTimeoutId);
	}
	
	function rightShift(){
		if(isDown){
			var insideTimeoutId;
			if(-shift < max_right_shift){
				if(max_right_shift-shift < step){
					shift = max_right_shift;
				}
				else{
					shift -= step;
					$(wrapper).css({'left' : shift + 'px'});
					insideTimeoutId = setTimeout(rightShift, timeout);					
				}
			}

		}
		else
			clearTimeout(insideTimeoutId);
	}	
	})
</script>
<div class="inw">
	<div class="slides-wrapper">
		<div class="imp-news-flash center clearfix <?php echo $moduleclass_sfx; ?>">
			<?php foreach ($list as $item) : ?>
				<?php require JModuleHelper::getLayoutPath('mod_articles_news', '_important_item'); ?>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="slides-arrow">
		<span class="left-slide"><i class="fa fa-5x fa-angle-left" aria-hidden="true"></i></span>
		<span class="right-slide"><i class="fa fa-5x fa-angle-right" aria-hidden="true"></i></span>
	</div>
</div>
