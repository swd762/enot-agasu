<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_news
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$item_heading = $params->get('item_heading', 'h4');

$output = preg_match_all('/<img[^>]+src=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/', $item->introtext, $imgs);
//$output = preg_match_all('/<img[^>]+alt=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/', $item->introtext, $alts);
if($imgs[2][0] == '/files/images/44-redaktor/logo/agasu_logo1.jpg') 
	$imgs[2][0] = str_replace('jpg', 'png', $imgs[2][0]);
elseif(!$imgs[2][0])
	$imgs[2][0] = '/files/images/44-redaktor/logo/agasu_logo1.png'
?>

<div class="imp-news-item">
	<a href="<?php echo $item->link; ?>">
	<div class="imp-news-img-container"><img src="<?php echo $imgs[2][0]; ?>"></div>
    <div class="imp-news-content">
        <span class="imp-news-title"><?php echo $item->title; ?></span>
        <span class="imp-news-date center"><?php echo date('d.m.Y', strtotime($item->created)); ?></span>
    </div>	
	</a>
</div>