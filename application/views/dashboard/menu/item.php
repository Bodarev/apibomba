<!-- BEGIN PAGE HEADER-->
<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="/<?= ADM_CONTROLLER ?>/menu/"><?=lang('Home')?></a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="<?= $parent_url ?>"><?= $parent_title ?></a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span><?= $title ?></span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- END PAGE HEADER-->

<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"><?= $title ?></h1>
<!-- END PAGE TITLE-->

<?php // Отображаем сообщения пользователю ?>
<?php if (isset($_SESSION['success'])) : ?>
    <div class="alert alert-block alert-success fade in">
        <button type="button" class="close" data-dismiss="alert"></button>
        <?= $_SESSION['success']; ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['error'])) : ?>
    <div class="alert alert-block alert-danger fade in">
        <button type="button" class="close" data-dismiss="alert"></button>
        <?php foreach ($_SESSION['error'] as $error) : ?>
            <?= $error ?>
            <br/>
        <?php endforeach; ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="row">
    <div class="portlet light">
        <div class="portlet-body">
            <form method="post" enctype="multipart/form-data">
                <div class="panel-body">
                    <ul class="nav nav-pills">
                        <li class="active">
                            <a href="#tab_1_1" data-toggle="tab"><?=lang('General information')?></a>
                        </li>
                        <li>
                            <a href="#tab_1_2" data-toggle="tab"><?=lang('SEO')?></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="tab_1_1">
                            <div class="table-scrollable">
                                <table class="table table-bordered table-striped table-hover">
                                    <tbody>
                                    <? foreach(language(true) as $lang){ ?>
                                        <tr>
                                            <td width="200"><?=lang('Name')?> <?=strtoupper($lang)?> *</td>
                                            <td>
                                                <input type="text" name="title<?=strtoupper($lang)?>" class="form-control"
                                                    value="<?= $item->{'title'.strtoupper($lang)} ?>" required>
                                            </td>
                                        </tr>
                                    <?}?>
                                    <? foreach(language(true) as $lang){ ?>
                                        <tr>
                                            <td width="200"><?=lang('Text')?> <?=strtoupper($lang)?></td>
                                            <td>
                                                <textarea name="text<?=strtoupper($lang)?>" cols="30" rows="3"
                                                        class="form-control ckeditor"><?= $item->{'text'.strtoupper($lang)} ?></textarea>
                                            </td>
                                        </tr>
                                    <?}?>
                                    <tr>
                                        <td width="200"><?=lang('Photo')?></td>
                                        <td>
                                            <input type="file" name="img" id="file" class="form-control">
                                            <div class="note note-warning" style="margin-bottom: 0px; margin-top: 10px;">
                                                <p>
                                                    <?php if($item->id == 1) { ?>
                                                        <?=lang('Allowable sizes')?>: 480x480
                                                    <?php } else { ?>
                                                        <?=lang('Allowable sizes')?>: 1110x370
                                                    <?php } ?>
                                                </p>
                                            </div>
                                            <?php if (!empty($item->img)): ?>
                                                <?php $src = newthumbs($item->img, $table, 250, 250, '250x250x1', 1) ?>
                                                <br>
                                                <div class="mt-element-card mt-element-overlay item">
                                                    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                                        <div class="mt-card-item">
                                                            <div class="mt-card-avatar mt-overlay-1">
                                                                <img src="<?= $src ?>"/>
                                                                <div class="mt-overlay">
                                                                    <ul class="mt-info">
                                                                        <li>
                                                                            <a class="btn red mine_delete_photo"
                                                                               data-table="<?=$table?>"
                                                                               data-id="<?= $item->id ?>"
                                                                               data-col="img"
                                                                               href="javascript:;">
                                                                                <i class="fa fa-ban"></i>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="200">&nbsp;</td>
                                        <td>
                                            <button type="submit" class="btn green"><i class="fa fa-check"></i><?=lang('Edit')?>
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_1_2">
                            <div class="table-scrollable">
                                <table class="table table-bordered table-striped table-hover">
                                    <tbody>
                                    <? foreach(language(true) as $lang){ ?>
                                        <tr>
                                            <td width="200"><?=lang('Headline')?> <?=strtoupper($lang)?></td>
                                            <td>
                                                <input type="text" name="seoTitle<?=strtoupper($lang)?>" class="form-control"
                                                    value="<?= $item->{'seoTitle'.strtoupper($lang)} ?>">
                                            </td>
                                        </tr>
                                    <?}?>
                                    <? foreach(language(true) as $lang){ ?>
                                        <tr>
                                            <td width="200"><?=lang('Keywords')?> <?=strtoupper($lang)?></td>
                                            <td>
                                                <textarea name="seoKeywords<?=strtoupper($lang)?>" cols="30" rows="3"
                                                        class="form-control"><?= $item->{'seoKeywords'.strtoupper($lang)} ?></textarea>
                                            </td>
                                        </tr>
                                    <?}?>
                                    <? foreach(language(true) as $lang){ ?>
                                        <tr>
                                            <td width="200"><?=lang('Description')?> <?=strtoupper($lang)?></td>
                                            <td>
                                                <textarea name="seoDesc<?=strtoupper($lang)?>" cols="30" rows="3"
                                                        class="form-control"><?= $item->{'seoDesc'.strtoupper($lang)} ?></textarea>
                                            </td>
                                        </tr>
                                    <?}?>
                                    <tr>
                                        <td width="200">&nbsp;</td>
                                        <td>
                                            <button type="submit" class="btn green"><i class="fa fa-check"></i><?=lang('Edit')?>
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
