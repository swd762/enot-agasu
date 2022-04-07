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


// creating date stamp for marking news
$day = date('d', strtotime($item->created));
$arr = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
$month = $arr[date('m', strtotime($item->created)) - 1];

?>


<div class="news-item col-lg-3 col-md-4 col-sm-6">
    <a href="<?php echo $item->link; ?>">
        <section class="news-item__img">
            <div class="o-mask">
                <div class="shadow"></div>
                <div class="date-mark">
                    <span class="day"><?php echo $day ?></span>
                    <span class="month"><?php echo $month ?></span>
                </div>

                <?php $images = json_decode($item->images); ?>
                <?php if (isset($images->image_intro) && !empty($images->image_intro)) { ?>
                    <img src="<?php echo htmlspecialchars($images->image_intro); ?>" alt="<?php echo htmlspecialchars($images->image_intro_alt); ?>"/>
                <?php } else { ?>
                    <img src="<?php echo htmlspecialchars($imgs[2][0]); ?>" alt="<?php echo htmlspecialchars($images->image_intro_alt); ?>"/>
                <?php } ?>
            </div>
        </section>
        <div class="news-item__title">
            <span class=""><?php echo $item->title; ?></span>
        </div>
    </a>
</div>