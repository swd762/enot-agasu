<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */
defined('_JEXEC') or die;
?>

<?php // template for rendering top menu AGASU site ?>
<ul class="header-nav <?php echo $class_sfx; ?>">
    <?php
    $flagDivider = true;
    $flagNewMenuRoot = true;
    $blocksCounter = 0;

    foreach ($list as $i => $item) {
        $title = $item->title;
        $rootMenuItem = $item->level == 1;
        $flink = $item->flink;
        $isSeparator = $item->type === 'separator';
        $isHeading = $item->type === 'heading';
        $flink = JFilterOutput::ampReplace(htmlspecialchars($flink));

        if ($rootMenuItem && !$flagNewMenuRoot) {
            if ($blocksCounter % 4 != 0) {
                while ($blocksCounter % 4 != 0) {
                    $blocksCounter++;
                    echo '<div class="nav-column"></div>';

                }
            }
            $blocksCounter = 0;
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</li>';
            $flagNewMenuRoot = true;
        }
        if ($rootMenuItem && $flagNewMenuRoot) {
            echo '<li class="header-nav__item">';
            echo '<a href="#">' . $item->title . '</a>';
            echo '<div class="nav-drop">';
            echo '<div class="container header-container">';
            echo '<div class="nav-box">';
            echo '<div class="nav-column">';
            echo '<h4 class="nav-title">' . $item->title . '</h4>';
            echo '</div>';
            $flagNewMenuRoot = false;
            $blocksCounter++;

        }
        if ($flagDivider && !$rootMenuItem) {
            $flagDivider = false;
            echo '<div class="nav-column">';
            echo '<ul>';
            $blocksCounter++;
        }
        if (!$flagDivider && !$rootMenuItem && !$isSeparator) {
            if ($isHeading) {
                echo '<li class="nav-heading">' . $title . '</li>';
            } else {
                echo '<li><a href="' . $flink . '"' . ' class="' . $item->anchor_css . '"' . '>' . $title . '</a></li>';
            }
        }
        if ($isSeparator) {
            $flagDivider = true;
            echo '</ul>';
            echo '</div>';
        }
        if ($blocksCounter == 4 && $isSeparator) {
            echo '<div class="nav-column"></div>';
            $blocksCounter++;
        }
    }

    ?>

</ul>

