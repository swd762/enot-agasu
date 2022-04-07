<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.agasu
 *
 * @copyright   Copyright (C) 2021 AGASU by Ilya Kazalin. All rights reserved.
 * @license
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$user = JFactory::getUser();
$doc = JFactory::getDocument();
$menu = $app->getMenu();
$params = $app->getTemplate(true)->params;


// Output as HTML5
$this->setHtml5(true);

// load lang
$lang = JFactory::getLanguage();
$lang->load('ru-RU');

// get item id for page
$jInput = $app->input;
$itemId = $jInput->get('Itemid', null, 'int');

//echo '<pre>';
//var_dump($itemID);
//echo '</pre>';


//add bootstrap script
//jQuery.noConflict();
//JHtml::_('bootstrap.framework');
//$scr = '/media/jui/js/jquery.min.js';
//$repl_scr = '//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js';
//$key = array_keys($doc->_scripts);
//$value = array_values($doc->_scripts);
//$key = str_replace($scr, $repl_scr, $key);
//$doc->_scripts = array_combine($key, $value);
//
//unset($doc->_scripts[JURI::root(true) . 'media/jui/js/jquery.min.js']);

// add jquery
//JHtml::_('jquery.framework');
//unset($this->_scripts[$this->baseurl.'/media/system/js/mootools-core.js'],
//    $this->_scripts[$this->baseurl.'/media/system/js/mootools-more.js'],
////    $this->_scripts[$this->baseurl.'/media/system/js/core.js'],
////    $this->_scripts[$this->baseurl.'/media/system/js/caption.js']
//);


$templatePath = $app->getTemplate();

//mobile detect lib
require_once(join(DIRECTORY_SEPARATOR, array(JPATH_THEMES, $templatePath, 'libs', 'Mobile_Detect.php')));
// my utility class
$utilClassPath = join(DIRECTORY_SEPARATOR, array(JPATH_THEMES, $templatePath, 'libs', 'util.php'));
require_once($utilClassPath);

// Add Stylesheets
JHtml::_('stylesheet', 'template.css', array('version' => 'auto', 'relative' => true));

$detect = new Mobile_Detect();
?>

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>templates/<?php echo $this->template ?>/addons/awesome/css/font-awesome.min.css">
    <script type="text/javascript">

    </script>
    <!--script for regin carousel-->
    <!--            <script src="https://xn--80apaohbc3aw9e.xn--p1ai/region-widget.js"></script>-->
    <!--***-->

    <script src="<?php echo $this->baseurl ?>templates/<?php echo $this->template ?>/js/main.js?v1.2" defer></script>
    <script src="https://api-maps.yandex.ru/2.0/?load=package.standard,package.geoObjects&amp;lang=ru-RU&amp;apikey=50e1e38f-fa6c-48b8-ace0-a8795364ce1f"
            type="text/javascript" defer></script>


    <!--custom scroll-->
    <!--    <link rel="stylesheet" href="--><?php //echo $this->baseurl ?><!--templates/--><?php //echo $this->template ?><!--/addons/custom-scroll/jquery.custom-scrollbar.css">-->
    <!--    <script src="--><?php //echo $this->baseurl ?><!--templates/-->
    <?php //echo $this->template ?><!--/addons/custom-scroll/jquery.custom-scrollbar.min.js" defer></script>-->

    <jdoc:include type="head"/>
</head>
<body class="site">
<div class="body">
    <!-- *** Header *** -->
    <header class="header">
        <div class="header-top">
            <div class="container header-container">
                <jdoc:include type="modules" name="top_shortcuts"/>
                <div class="header-options">
                    <div class="header-version-vi options-icon" itemprop="copy">
                        <jdoc:include type="modules" name="eye"/>
                        <a href="#" id="top-eye" class="bi bi-eye "></a>
                    </div>
                    <div class="header-account options-icon btn-group">
                        <a href="#" class="bi bi-person-square btn dropdown-toggle">
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/postupayushchim/lichnyj-kabinet-abiturienta">Личный кабинет абитуриента</a></li>
                            <li><a href="/studentam/elektronnaya-informatsionno-obrazovatelnaya-sreda">ЭОС</a></li>
                        </ul>
                    </div>
                    <div class="header-language-select options-icon">
                        <jdoc:include type="modules" name="language_switcher"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom">
            <div class="container header-container">
                <a href="index.php?Itemid=101" class="header-logo">
                    <img class="logo-img" src="<?php echo $this->baseurl ?>templates/<?php echo $this->template ?>/images/logo-exp-gray-40.svg" alt="logo">
                    <img class="logo-img-mobile" src="<?php echo $this->baseurl ?>templates/<?php echo $this->template ?>/images/logo-exp-mobile-1.svg" alt="logo-mobile">
                </a>

                <!--  Main menu rendering  -->
                <jdoc:include type="modules" name="top_menu"/>

                <!--  Mobile shortcuts  -->
                <div class="header-nav-mobile">
                    <jdoc:include type="modules" name="top_shortcuts_mobile"/>
                </div>

                <!-- Main menu underline -->
                <span class="target"></span>

                <div class="header-right-box">
                    <div class="search-wrapper">
                        <jdoc:include type="modules" name="search"/>
                    </div>
                    <!--  Garbage panel link for desktop  -->
                    <a href="#" class="btn-panel pushmenu" id="nav-icon3" style="display: none">
                        <span></span>
                        <span></span>
                        <span></span>
                    </a>
                    <a href="#" class="burger-mobile" id="mobile-menu-trigger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </a>
                </div>
            </div>
        </div>
        <div class="menu-mobile-popup">
            <div class="container">
                <div class="menu-mobile-head">
                    <a href="#" class="menu-mobile-logo">
                        <img src="<?php echo $this->baseurl ?>templates/<?php echo $this->template ?>/images/logo-sitemap.svg" alt="logo">
                    </a>
                    <span class="menu-mobile-close" style="font-size: 25px"><i class="bi bi-x-lg"></i></span>
                </div>
                <div class="menu-mobile-main">
                    <jdoc:include type="modules" name="mobile-top"/>
                </div>
                <div class="menu-mobile-footer">
                    <div>
                        <a href="#" class="menu-mobile-social-icon"><i class="ic-vk"></i></a>
                        <a href="#" class="menu-mobile-social-icon"><i class="ic-facebook"></i></a>
                        <a href="#" class="menu-mobile-social-icon"><i class="ic-instagram"></i></a>
                        <a href="#" class="menu-mobile-social-icon"><i class="ic-odnoklassniki"></i></a>
                    </div>
                    <div>© АГАСУ 2021</div>
                </div>
            </div>
        </div>
    </header>
    <!-- *** -->
    <div class="hidden-overlay"></div>

    <section class="main">

        <?php if (!$detect->isMobile()) { ?>
            <section class="main-slider">
                <!-- Slider main container -->
                <jdoc:include type="modules" name="slider"/>
            </section>
        <?php } ?>


        <?php if ($itemId == 101) { ?>
            <?php // news list module ?>
            <section class="news-section">
                <div class="container">
                    <div class="row">
                        <section class="news col-xl-12">
                            <jdoc:include type="modules" name="latest_news" style="latestNews"/>
                        </section>
                    </div>
                </div>
            </section>
        <?php } else { ?>
            <!--  ***  -->
            <section class="breadcrumb">
                <div class="container">
                    <jdoc:include type="modules" name="breadcrumbs"/>
                </div>
            </section>
            <section class="content">
                <div class="container">
                    <jdoc:include type="component"/>
                </div>
            </section>
        <?php } ?>


        <!--Media block-->
        <?php if ($itemId == 101) { ?>
            <section class="media-socials">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 media">
                            <h3 class="block-heading">Медиаресурсы</h3>
                            <section id="media-content" class="media-content row"></section>
                            <section class="media-footer">
                                <a target="_blank" href="https://www.youtube.com/channel/UCdg84ZdlVAtQyug4mWEwHGQ" class="btn btn-primary">Все видео
                                </a>
                            </section>
                        </div>
                        <div class="col-xl-4 socials">
                            <h3 class="block-heading">мы в соц. сетях</h3>
                            <section class="socials-content">
                                <a class="socials-link" href="https://www.youtube.com/channel/UCdg84ZdlVAtQyug4mWEwHGQ" target="_blank">
                                    <img src="<?php echo $this->baseurl ?>templates/<?php echo $this->template ?>/images/icons/socials/yt-icon.svg" alt="youtube link"/>
                                </a>

                                <a class="socials-link" href="https://t.me/+TomhNYUXJdJiYzVi" target="_blank">
                                    <img src="<?php echo $this->baseurl ?>templates/<?php echo $this->template ?>/images/icons/socials/tg-icon.svg" alt="telegram link"/>
                                </a>

                                <a class="socials-link" href="https://vk.com/asuace" target="_blank">
                                    <img src="<?php echo $this->baseurl ?>templates/<?php echo $this->template ?>/images/icons/socials/vk-icon.svg" alt="vk link"/>
                                </a></section>
                            <div>
                                <script src='https://pos.gosuslugi.ru/bin/script.min.js'></script>
                                <style> #js-show-iframe-wrapper {
                                        position: relative;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        width: 100%;
                                        min-width: 293px;
                                        max-width: 100%;
                                        background: linear-gradient(138.4deg, #38bafe 26.49%, #2d73bc 79.45%);
                                        color: #fff;
                                        cursor: pointer
                                    }

                                    #js-show-iframe-wrapper .pos-banner-fluid * {
                                        box-sizing: border-box
                                    }

                                    #js-show-iframe-wrapper .pos-banner-fluid .pos-banner-btn_2 {
                                        display: block;
                                        width: 240px;
                                        min-height: 56px;
                                        font-size: 18px;
                                        line-height: 24px;
                                        cursor: pointer;
                                        background: #0d4cd3;
                                        color: #fff;
                                        border: none;
                                        border-radius: 8px;
                                        outline: 0
                                    }

                                    #js-show-iframe-wrapper .pos-banner-fluid .pos-banner-btn_2:hover {
                                        background: #1d5deb
                                    }

                                    #js-show-iframe-wrapper .pos-banner-fluid .pos-banner-btn_2:focus {
                                        background: #2a63ad
                                    }

                                    #js-show-iframe-wrapper .pos-banner-fluid .pos-banner-btn_2:active {
                                        background: #2a63ad
                                    }

                                    @-webkit-keyframes fadeInFromNone {
                                        0% {
                                            display: none;
                                            opacity: 0
                                        }
                                        1% {
                                            display: block;
                                            opacity: 0
                                        }
                                        100% {
                                            display: block;
                                            opacity: 1
                                        }
                                    }

                                    @keyframes fadeInFromNone {
                                        0% {
                                            display: none;
                                            opacity: 0
                                        }
                                        1% {
                                            display: block;
                                            opacity: 0
                                        }
                                        100% {
                                            display: block;
                                            opacity: 1
                                        }
                                    }

                                    @font-face {
                                        font-family: LatoWebLight;
                                        src: url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Light.woff2) format("woff2"), url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Light.woff) format("woff"), url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Light.ttf) format("truetype");
                                        font-style: normal;
                                        font-weight: 400
                                    }

                                    @font-face {
                                        font-family: LatoWeb;
                                        src: url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Regular.woff2) format("woff2"), url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Regular.woff) format("woff"), url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Regular.ttf) format("truetype");
                                        font-style: normal;
                                        font-weight: 400
                                    }

                                    @font-face {
                                        font-family: LatoWebBold;
                                        src: url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Bold.woff2) format("woff2"), url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Bold.woff) format("woff"), url(https://pos.gosuslugi.ru/bin/fonts/Lato/fonts/Lato-Bold.ttf) format("truetype");
                                        font-style: normal;
                                        font-weight: 400
                                    }

                                    @font-face {
                                        font-family: RobotoWebLight;
                                        src: url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Light.woff2) format("woff2"), url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Light.woff) format("woff"), url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Light.ttf) format("truetype");
                                        font-style: normal;
                                        font-weight: 400
                                    }

                                    @font-face {
                                        font-family: RobotoWebRegular;
                                        src: url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Regular.woff2) format("woff2"), url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Regular.woff) format("woff"), url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Regular.ttf) format("truetype");
                                        font-style: normal;
                                        font-weight: 400
                                    }

                                    @font-face {
                                        font-family: RobotoWebBold;
                                        src: url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Bold.woff2) format("woff2"), url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Bold.woff) format("woff"), url(https://pos.gosuslugi.ru/bin/fonts/Roboto/Roboto-Bold.ttf) format("truetype");
                                        font-style: normal;
                                        font-weight: 400
                                    }

                                    @font-face {
                                        font-family: ScadaWebRegular;
                                        src: url(https://pos.gosuslugi.ru/bin/fonts/Scada/Scada-Regular.woff2) format("woff2"), url(https://pos.gosuslugi.ru/bin/fonts/Scada/Scada-Regular.woff) format("woff"), url(https://pos.gosuslugi.ru/bin/fonts/Scada/Scada-Regular.ttf) format("truetype");
                                        font-style: normal;
                                        font-weight: 400
                                    }

                                    @font-face {
                                        font-family: ScadaWebBold;
                                        src: url(https://pos.gosuslugi.ru/bin/fonts/Scada/Scada-Bold.woff2) format("woff2"), url(https://pos.gosuslugi.ru/bin/fonts/Scada/Scada-Bold.woff) format("woff"), url(https://pos.gosuslugi.ru/bin/fonts/Scada/Scada-Bold.ttf) format("truetype");
                                        font-style: normal;
                                        font-weight: 400
                                    }

                                    @font-face {
                                        font-family: Geometria;
                                        src: url(https://pos.gosuslugi.ru/bin/fonts/Geometria/Geometria.eot);
                                        src: url(https://pos.gosuslugi.ru/bin/fonts/Geometria/Geometria.eot?#iefix) format("embedded-opentype"), url(https://pos.gosuslugi.ru/bin/fonts/Geometria/Geometria.woff) format("woff"), url(https://pos.gosuslugi.ru/bin/fonts/Geometria/Geometria.ttf) format("truetype");
                                        font-weight: 400;
                                        font-style: normal
                                    }

                                    @font-face {
                                        font-family: Geometria-ExtraBold;
                                        src: url(https://pos.gosuslugi.ru/bin/fonts/Geometria/Geometria-ExtraBold.eot);
                                        src: url(https://pos.gosuslugi.ru/bin/fonts/Geometria/Geometria-ExtraBold.eot?#iefix) format("embedded-opentype"), url(https://pos.gosuslugi.ru/bin/fonts/Geometria/Geometria-ExtraBold.woff) format("woff"), url(https://pos.gosuslugi.ru/bin/fonts/Geometria/Geometria-ExtraBold.ttf) format("truetype");
                                        font-weight: 800;
                                        font-style: normal
                                    } </style>
                                <style> #js-show-iframe-wrapper .bf-2 {
                                        position: relative;
                                        display: grid;
                                        grid-template-columns:var(--pos-banner-fluid-2__grid-template-columns);
                                        grid-template-rows:var(--pos-banner-fluid-2__grid-template-rows);
                                        width: 100%;
                                        max-width: 1060px;
                                        font-family: LatoWeb, sans-serif;
                                        box-sizing: border-box
                                    }

                                    #js-show-iframe-wrapper .bf-2__decor {
                                        grid-column: var(--pos-banner-fluid-2__decor-grid-column);
                                        grid-row: var(--pos-banner-fluid-2__decor-grid-row);
                                        padding: var(--pos-banner-fluid-2__decor-padding);
                                        background: var(--pos-banner-fluid-2__bg-url) var(--pos-banner-fluid-2__bg-position) no-repeat;
                                        background-size: var(--pos-banner-fluid-2__bg-size)
                                    }

                                    #js-show-iframe-wrapper .bf-2__logo-wrap {
                                        position: absolute;
                                        top: var(--pos-banner-fluid-2__logo-wrap-top);
                                        bottom: var(--pos-banner-fluid-2__logo-wrap-bottom);
                                        right: 0;
                                        display: flex;
                                        flex-direction: column;
                                        align-items: flex-end;
                                        padding: var(--pos-banner-fluid-2__logo-wrap-padding);
                                        background: #2d73bc;
                                        border-radius: var(--pos-banner-fluid-2__logo-wrap-border-radius)
                                    }

                                    #js-show-iframe-wrapper .bf-2__logo {
                                        width: 128px
                                    }

                                    #js-show-iframe-wrapper .bf-2__slogan {
                                        font-family: LatoWebBold, sans-serif;
                                        font-size: var(--pos-banner-fluid-2__slogan-font-size);
                                        line-height: var(--pos-banner-fluid-2__slogan-line-height);
                                        color: #fff
                                    }

                                    #js-show-iframe-wrapper .bf-2__content {
                                        padding: var(--pos-banner-fluid-2__content-padding)
                                    }

                                    #js-show-iframe-wrapper .bf-2__description {
                                        display: flex;
                                        flex-direction: column;
                                        margin-bottom: 24px
                                    }

                                    #js-show-iframe-wrapper .bf-2__text {
                                        margin-bottom: 12px;
                                        font-size: 24px;
                                        line-height: 32px;
                                        font-family: LatoWebBold, sans-serif;
                                        color: #fff
                                    }

                                    #js-show-iframe-wrapper .bf-2__text_small {
                                        margin-bottom: 0;
                                        font-size: 16px;
                                        line-height: 24px;
                                        font-family: LatoWeb, sans-serif
                                    }

                                    #js-show-iframe-wrapper .bf-2__btn-wrap {
                                        display: flex;
                                        align-items: center;
                                        justify-content: center
                                    } </style>
                                <div id='js-show-iframe-wrapper'>
                                    <div class='pos-banner-fluid bf-2'>
                                        <div class='bf-2__decor'>
                                            <div class='bf-2__logo-wrap'><img class='bf-2__logo' src='https://pos.gosuslugi.ru/bin/banner-fluid/gosuslugi-logo.svg'
                                                                              alt='Госуслуги'/>
                                                <div class='bf-2__slogan'>Решаем вместе</div>
                                            </div>
                                        </div>
                                        <div class='bf-2__content'>
                                            <div class='bf-2__description'><span class='bf-2__text'> Не убран мусор, яма на дороге, не горит фонарь? </span> <span
                                                        class='bf-2__text bf-2__text_small'> Столкнулись с проблемой&nbsp;— сообщите о ней! </span></div>
                                            <div class='bf-2__btn-wrap'> <!-- pos-banner-btn_2 не удалять; другие классы не добавлять -->
                                                <button class='pos-banner-btn_2' type='button'>Сообщить о проблеме</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script> (function () {
                                        "use strict";

                                        function ownKeys(e, t) {
                                            var o = Object.keys(e);
                                            if (Object.getOwnPropertySymbols) {
                                                var n = Object.getOwnPropertySymbols(e);
                                                if (t) n = n.filter(function (t) {
                                                    return Object.getOwnPropertyDescriptor(e, t).enumerable
                                                });
                                                o.push.apply(o, n)
                                            }
                                            return o
                                        }

                                        function _objectSpread(e) {
                                            for (var t = 1; t < arguments.length; t++) {
                                                var o = null != arguments[t] ? arguments[t] : {};
                                                if (t % 2) ownKeys(Object(o), true).forEach(function (t) {
                                                    _defineProperty(e, t, o[t])
                                                }); else if (Object.getOwnPropertyDescriptors) Object.defineProperties(e, Object.getOwnPropertyDescriptors(o)); else ownKeys(Object(o)).forEach(function (t) {
                                                    Object.defineProperty(e, t, Object.getOwnPropertyDescriptor(o, t))
                                                })
                                            }
                                            return e
                                        }

                                        function _defineProperty(e, t, o) {
                                            if (t in e) Object.defineProperty(e, t, {value: o, enumerable: true, configurable: true, writable: true}); else e[t] = o;
                                            return e
                                        }

                                        var POS_PREFIX_2 = "--pos-banner-fluid-2__", posOptionsInitial = {
                                            "grid-template-columns": "100%",
                                            "grid-template-rows": "310px auto",
                                            "decor-grid-column": "initial",
                                            "decor-grid-row": "initial",
                                            "decor-padding": "30px 30px 0 30px",
                                            "bg-url": "url('https://pos.gosuslugi.ru/bin/banner-fluid/2/banner-fluid-bg-2-small.svg')",
                                            "bg-position": "calc(10% + 64px) calc(100% - 20px)",
                                            "bg-size": "cover",
                                            "content-padding": "0 30px 30px 30px",
                                            "slogan-font-size": "20px",
                                            "slogan-line-height": "32px",
                                            "logo-wrap-padding": "20px 30px 30px 40px",
                                            "logo-wrap-top": "0",
                                            "logo-wrap-bottom": "initial",
                                            "logo-wrap-border-radius": "0 0 0 80px"
                                        }, setStyles = function (e, t) {
                                            Object.keys(e).forEach(function (o) {
                                                t.style.setProperty(POS_PREFIX_2 + o, e[o])
                                            })
                                        }, removeStyles = function (e, t) {
                                            Object.keys(e).forEach(function (e) {
                                                t.style.removeProperty(POS_PREFIX_2 + e)
                                            })
                                        };

                                        function changePosBannerOnResize() {
                                            var e = document.documentElement, t = _objectSpread({}, posOptionsInitial), o = document.getElementById("js-show-iframe-wrapper"),
                                                n = o ? o.offsetWidth : document.body.offsetWidth;
                                            if (n > 405) t["slogan-font-size"] = "24px", t["logo-wrap-padding"] = "30px 50px 30px 70px";
                                            if (n > 500) t["grid-template-columns"] = "min-content 1fr", t["grid-template-rows"] = "100%", t["decor-grid-column"] = "2", t["decor-grid-row"] = "1", t["decor-padding"] = "30px 30px 30px 0", t["content-padding"] = "30px", t["bg-position"] = "0% calc(100% - 70px)", t["logo-wrap-padding"] = "30px 30px 24px 40px", t["logo-wrap-top"] = "initial", t["logo-wrap-bottom"] = "0", t["logo-wrap-border-radius"] = "80px 0 0 0";
                                            if (n > 585) t["bg-position"] = "0% calc(100% - 6px)";
                                            if (n > 800) t["bg-url"] = "url('https://pos.gosuslugi.ru/bin/banner-fluid/2/banner-fluid-bg-2.svg')", t["bg-position"] = "0% center";
                                            if (n > 1020) t["slogan-font-size"] = "32px", t["line-height"] = "40px", t["logo-wrap-padding"] = "30px 30px 24px 50px";
                                            setStyles(t, e)
                                        }

                                        changePosBannerOnResize(), window.addEventListener("resize", changePosBannerOnResize), window.onunload = function () {
                                            var e = document.documentElement;
                                            window.removeEventListener("resize", changePosBannerOnResize), removeStyles(posOptionsInitial, e)
                                        };
                                    })() </script>
                                <script>Widget("https://pos.gosuslugi.ru/form", 239775)</script>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <script>
                document.addEventListener('DOMContentLoaded', () => videoGalleryRender());
            </script>
            <!--Media block end-->

            <!--Useful links block-->
            <section class="useful-links">
                <jdoc:include type="modules" name="useful_links"/>
                <script type="text/javascript" src="<?php echo $this->baseurl ?>templates/<?php echo $this->template ?>/addons/slick/slick.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        jQuery(function ($) {
                            $('.useful-links-slider').slick({
                                dots: true,
                                infinite: true,
                                speed: 500,
                                cssEase: 'linear',
                                slidesToShow: 5,
                                slidesToScroll: 1,
                                autoplay: true,
                                responsive: [{
                                    breakpoint: 1399.98,
                                    settings: {
                                        slidesToShow: 4,
                                        infinite: true
                                    }
                                },
                                    {
                                        breakpoint: 1199.98,
                                        settings: {
                                            slidesToShow: 3,
                                            infinite: true
                                        }
                                    },
                                    {
                                        breakpoint: 767.98,
                                        settings: {
                                            slidesToShow: 2,
                                            infinite: true
                                        }
                                    }
                                    , {
                                        breakpoint: 600,
                                        settings: {
                                            arrows: false,
                                            slidesToShow: 2,
                                            dots: true
                                        }
                                    },
                                    {
                                        breakpoint: 490,
                                        settings: {
                                            arrows: false,
                                            slidesToShow: 1,
                                            dots: true
                                        }
                                    }

                                    , {

                                        breakpoint: 300,
                                        settings: "unslick" // destroys slick
                                    }]
                            });
                        });
                    });

                </script>
            </section>

            <!-- map -->
            <div class="map-block__wrapper">
                <div class="map-block__header">
                    <div class="container">
                        <h3>АДРЕСА КОРПУСОВ</h3>
                    </div>
                </div>
                <div class="container">
                    <div class="map-block__content row">
                        <div class="map-block__content-items col-xl-6" id="styled-scroll">
                        </div>
                        <div class="map-block__content-ma col-xl-6 col-md-12" id="map">
                        </div>
                    </div>
                </div>
            </div>
            <script>
                // load map only when it need
                document.addEventListener('DOMContentLoaded', () => mapRendering());
            </script>
            <div class="region-widget-wrap"></div>
        <?php } ?>
        <!--***-->
    </section>
    <footer class="footer">
        <div class="container">
            <div class="row">
                <section class="footer-info col-xl-4">
                    <a href="index.php?Itemid=101" class="footer-logo">
                        <div class="footer-logo__image">
                            <img src="<?php echo $this->baseurl ?>templates/<?php echo $this->template ?>/images/tmp/logo_footer.png"
                                 alt="">
                        </div>
                        <p class="footer-logo__title"><b>енотаевский филиал</b><br>астраханского государственного<br>архитектурно-строительного<br>университета</p>
                    </a>
                    <section class="footer-info__copyright">
                        &copy&nbsp;<?php echo JText::_('TPL_AGASU_SHORT_NAME'); ?>&nbsp;<?php echo date('Y'); ?>
                    </section>
                </section>
                <section class="footer-info col-xl-5">
                    <div class="footer-contacts">
                        <section class="footer-contacts__phones">
                            <div class="phones_item">
                                <h5>+7 (85143) 9-18-65</h5>
                                <p>Приемная ректора</p>
                            </div>
                            <div class="phones_item">
                                <h5>+7 (85143) 9-41-65</h5>
                                <p>Приемная комиссия</p>
                            </div>
                        </section>
                        <section class="footer-contacts__address">
                            <?php echo JText::_('TPL_ENOT_ADDRESS'); ?>
                        </section>
                        <section class="footer-contacts__email">
                            astpu-28@mail.ru<br>nik_fil_aisi@mail.ru
                        </section>
                    </div>
                </section>
                <section class="footer-extra col-xl-3">
                    <a href="#" class="footer-extra__button footer-extra__button_eye" itemprop="copy">
                        <i class="ic-eye"></i>
                        Версия<br> для слабовидящих
                    </a>
                    <a href="#" class="footer-extra__button footer-extra__button_reception">
                        <i class="ic-appeal"></i>
                        Обращения граждан
                    </a>
                    <section class="footer-extra__socials">
                        <a target="_blank" href="https://vk.com/asuace" class="socials_item">
                            <i class="fa fa-vk"></i>
                        </a>
                        <a target="_blank" href="https://t.me/+TomhNYUXJdJiYzVi" class="socials_item">
                            <i class="fa fa-telegram"></i>
                        </a>
                        <a target="_blank" href="https://www.youtube.com/channel/UCdg84ZdlVAtQyug4mWEwHGQ" class="socials_item">
                            <i class="fa fa-youtube"></i>
                        </a></section>
                </section>
            </div>
        </div>

    </footer>
</div>

<script>
    function sh(obj) {
        if (typeof obj === 'object') if (obj.style.display === 'none') obj.style.display = 'block'; else obj.style.display = 'none';
    }
</script>
<?php if (!in_array($Itemid, [101, 102, 103])) { ?>
    <script>
        document.querySelectorAll("a[data-tooltip]").forEach((el) => {
            if (!el.dataset.tooltip) {
                return;
            }
            let tooltip = document.createElement('div');

            let signLink = document.createElement('a');
            signLink.setAttribute('href', el.dataset.signKeyLink);
            signLink.classList.add('sign-key');

            let signImage = document.createElement('img');
            signImage.setAttribute('src', '/images/banners/signature.png');

            signLink.appendChild(signImage);

            tooltip.classList.add('tooltip-msg');
            tooltip.innerHTML = el.dataset.tooltip;

            signLink.appendChild(tooltip);

            el.before(signLink);
        })
    </script>
    <style>
        a.sign-key {
            position: relative;
        }

        a.sign-key div.tooltip-msg {
            display: none;
            position: absolute;
            bottom: 100%;
            left: -50%;
            left: -50%;
            padding: 10px;
            background: #000000;
            color: #ffffff;
            font-size: 12px;
            z-index: 9999;
            width: max-content;
            text-align: center;
        }

        a.sign-key div.tooltip-msg p {
            margin: 0;
            padding: 0;
            line-height: 15px
        }

        a.sign-key:hover div.tooltip-msg {
            display: block
        }
    </style>
<?php } ?>


</body>