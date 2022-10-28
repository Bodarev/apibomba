<?php

$sidebar_menu = array(
    'menu' => array('name' => lang("Menu"), 'ico' => '<i class="fa fa-bars"></i>'),
    // Пример составления выпадающего меню
    /*
    'all_for_products' => array('name' => 'Товары', 'ico' => '<i class="fa fa-opencart"></i>',
        'childs' => array(
            'categories' => array('name' => 'Категории товаров', 'ico' => '<i class="fa fa-map-o"></i>'),
            'filters' => array('name' => 'Фильтры', 'ico' => '<i class="fa fa-filter"></i>'),
            'plates_of_goods' => array('name' => 'Плашки товаров', 'ico' => '<i class="fa fa-adjust"></i>'),
            'promotions' => array('name' => 'Акции', 'ico' => '<i class="fa fa-rocket"></i>'),
            'products' => array('name' => 'Товары', 'ico' => '<i class="fa fa-opencart"></i>')
        )),
    */
    'constants' => array('name' => lang("Constants"), 'ico' => '<i class="fa fa-globe"></i>')
);
?>
<!-- BEGIN SIDEBAR MENU -->
<!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
<!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
<!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
<!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
<!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
<ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true"
    data-slide-speed="200" style="padding-top: 20px">
    <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
    <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
    <li class="sidebar-toggler-wrapper hide">
        <div class="sidebar-toggler">
            <span></span>
        </div>
    </li>
    <!-- END SIDEBAR TOGGLER BUTTON -->
    <?php foreach ($sidebar_menu as $key => $val): ?>
        <?php if (!isset($val['childs'])): ?>
            <li class="nav-item <?= ($key == uri(2)) ? 'start active open' : '' ?>">
                <a href="/cp/<?= $key ?>/" class="nav-link ">
                    <?= $val['ico'] ?>
                    <span class="title"><?= $val['name'] ?></span>
                    <?php if ($key == uri(2)) : ?>
                        <span class="selected"></span>
                    <?php endif; ?>
                </a>
            </li>
        <?php else: ?>
            <?php
            $flag = false;
            foreach ($val['childs'] as $k => $v) {
                if ($k == uri(2)) {
                    $flag = true;
                }
            }
            ?>
            <li class="nav-item <?= ($flag) ? 'start active open' : '' ?>">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <?= $val['ico'] ?>
                    <span class="title"><?= $val['name'] ?></span>
                    <?php if ($flag) : ?>
                        <span class="selected"></span>
                    <?php endif; ?>
                    <span class="arrow <?=($flag)?' open':''?>"></span>
                </a>
                <?php if (!empty($val['childs'])) : ?>
                    <ul class="sub-menu">
                        <?php foreach ($val['childs'] as $k => $v): ?>
                            <?php
                            $status = ($k == uri(2)) ? 'start active open' : '';
                            $link = $k;
                            ?>
                            <li class="nav-item <?= $status ?>">
                                <a href="/cp/<?= $link ?>/" class="nav-link <?= $status ?>">
                                    <?= $v['ico'] ?>
                                    <span class="title"><?= $v['name'] ?></span>
                                    <?php if ($k == uri(2)) : ?>
                                        <span class="selected"></span>
                                    <?php endif; ?>
                                    <?php if (!empty($v['badge']) && isset($v['badge_value'])): ?>
                                        <span class="badge badge-<?= $v['badge'] ?>"><?= $v['badge_value'] ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
<!-- END SIDEBAR MENU -->
<!-- END SIDEBAR MENU -->
