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
    <div class="portlet bordered">
        <div class="accordion" id="accordion">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #fff;">
                    <h4 class="panel-title">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse1"><i
                                    class="fa fa-plus"></i> <?= $add ?></a>
                    </h4>
                </div>
                <div id="collapse1" class="panel-collapse collapse">
                    <form action="<?= $a_path ?>" method="post" enctype="multipart/form-data">
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
                                                        <input type="text" name="title<?=strtoupper($lang)?>" class="form-control" required>
                                                    </td>
                                                </tr>
                                            <?}?>
                                            <? foreach(language(true) as $lang){ ?>
                                                <tr>
                                                    <td width="200"><?=lang('Text')?> <?=strtoupper($lang)?></td>
                                                    <td>
                                                        <textarea name="text<?=strtoupper($lang)?>" cols="30" rows="3" class="form-control ckeditor"></textarea>
                                                    </td>
                                                </tr>
                                            <?}?>
                                            <tr>
                                                <td width="200"><?=lang('Photo')?></td>
                                                <td>
                                                    <input type="file" name="img" id="file" class="form-control">
                                                    <div class="note note-warning" style="margin-bottom: 0px; margin-top: 10px;">
                                                        <p>
                                                        <?=lang('Allowable sizes')?>: 1110x370
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="200">&nbsp;</td>
                                                <td>
                                                    <button type="submit" class="btn green"><i class="fa fa-check"></i>
                                                        <?=lang('Add')?>
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
                                                        <input type="text" name="seoTitle<?=strtoupper($lang)?>" class="form-control">
                                                    </td>
                                                </tr>
                                            <?}?>
                                            <? foreach(language(true) as $lang){ ?>
                                                <tr>
                                                    <td width="200"><?=lang('Keywords')?> <?=strtoupper($lang)?></td>
                                                    <td>
                                                        <textarea name="seoKeywords<?=strtoupper($lang)?>" cols="30" rows="3" class="form-control"></textarea>
                                                    </td>
                                                </tr>
                                            <?}?>
                                            <? foreach(language(true) as $lang){ ?>
                                                <tr>
                                                    <td width="200"><?=lang('Description')?> <?=strtoupper($lang)?></td>
                                                    <td>
                                                        <textarea name="seoDesc<?=strtoupper($lang)?>" cols="30" rows="3" class="form-control"></textarea>
                                                    </td>
                                                </tr>
                                            <?}?>
                                            <tr>
                                                <td width="200">&nbsp;</td>
                                                <td>
                                                    <button type="submit" class="btn green"><i class="fa fa-check"></i>
                                                        <?=lang('Add')?>
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
    </div>
</div>

<?php if (!empty($objects)) : ?>
    <div class="row">
        <div class="portlet light">
            <div class="portlet-body">
                <form action="<?= $o_path; ?>" method="post">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th width="100"> <?=lang('Sorting')?></th>
                                <th> <?=lang('Name')?></th>
                                <th width="180"></th>
                                <th width="180"></th>
                                <th width="190"></th>
                                <th width="305"> <?=lang('Action')?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($objects as $item): ?>
                                <tr style="height: 51px;">
                                    <td class="align-middle">
                                        <input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" min="1"
                                               class="form-control text-center sorder" value="<?= $item->sorder ?>"
                                               name="so[<?= $item->id ?>]">
                                    </td>
                                    <td class="align-middle"><a style="font-weight: 900;"
                                           href="<?= $e_path . $item->id; ?>"><?= $item->{'title'.get_language_for_admin(true)} ?></a></td>
                                    <td class="align-middle">
                                        <?php $cmod = (!empty($item->isShown)) ? 'checked' : '' ?>
                                        <label class="mt-checkbox mt-checkbox-outline">
                                            <input type="checkbox" <?= $cmod ?> value="<?= $item->id ?>"
                                                   data-col="isShown" data-table="<?=$table?>" class="mine_change_check"><?=lang('Show on site')?>
                                            <span></span>
                                        </label>
                                    </td>
                                    <td class="align-middle">
                                        <?php $cmod = (!empty($item->onTop)) ? 'checked' : '' ?>
                                        <label class="mt-checkbox mt-checkbox-outline">
                                            <input type="checkbox" <?= $cmod ?> value="<?= $item->id ?>"
                                                data-col="onTop" data-table="<?=$table?>" class="mine_change_check"><?=lang('Show in header')?>
                                            <span></span>
                                        </label>
                                    </td>
                                    <td class="align-middle">
                                        <?php $cmod = (!empty($item->onBottom)) ? 'checked' : '' ?>
                                        <label class="mt-checkbox mt-checkbox-outline">
                                            <input type="checkbox" <?= $cmod ?> value="<?= $item->id ?>"
                                                data-col="onBottom" data-table="<?=$table?>"class="mine_change_check"><?=lang('Show in footer')?>
                                            <span></span>
                                        </label>
                                    </td>
                                    <td class="align-middle">
                                        <a href="<?= $site_url . $item->uriRU ?>"
                                           class="btn btn-xs default btn-editable blue-stripe" target="_blank">
                                            <i class="glyphicon glyphicon-new-window"></i>
                                        </a>
                                        <a href="<?= $e_path . $item->id . '/' ?>"
                                           class="btn btn-xs default btn-editable green-stripe">
                                            <i class="glyphicon glyphicon-edit"></i> <?=lang('Edit')?>
                                        </a>
                                        <?php if (!$item->system): ?>
                                            <a href="<?= $del_path . $item->id . '/' ?>"
                                               class="btn btn-xs default btn-editable red-stripe mine_delete_row">
                                                <i class="glyphicon glyphicon-remove-circle"></i> <?=lang('Delete')?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn green"><i class="fa fa-check"></i> <?=lang('Refresh order')?></button>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
