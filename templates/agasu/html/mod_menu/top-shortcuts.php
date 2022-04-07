<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */
?>

<section class="header-shortcuts <?php echo $class_sfx; ?>">
    <?php
    foreach ($list as $i => $item) {
        $flink = $item->flink;
        $flink = JFilterOutput::ampReplace(htmlspecialchars($flink));
        echo '<a href="' . $flink . '">';
        echo '<i class="' . $item->anchor_css . '"></i>';
        echo $item->title;
        echo '</a>';
    }
    ?>
</section>