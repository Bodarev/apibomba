<!-- BEGIN PAGE HEADER-->
<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="/<?= ADM_CONTROLLER ?>/constants/"><?=lang('Home')?></a>
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
                            <a href="#tab_1_1" data-toggle="tab">Общая
                                информация</a>
                        </li>
                        <li>
                            <a href="#tab_1_2" data-toggle="tab">Служебная
                                информация</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="tab_1_1">
                            <div class="table-scrollable">
                                <table class="table table-bordered table-striped table-hover">
                                    <tbody>
                                    <tr>
                                        <td width="200"><?=lang('Name')?> RU *</td>
                                        <td>
                                            <input type="text" name="{'title'.get_language_for_admin(true)}"
                                                   class="form-control"
                                                   value="<?= $item->{'title'.get_language_for_admin(true)} ?>"
                                                   required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="200"><?=lang('Name')?> RO </td>
                                        <td>
                                            <input type="text" name="titleRO"
                                                   class="form-control"
                                                   value="<?= $item->titleRO ?>"
                                            >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="200"><?=lang('Name')?> EN *</td>
                                        <td>
                                            <input type="text" name="titleEN"
                                                   class="form-control"
                                                   value="<?= $item->titleEN ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="200"><?=lang('Text')?> RU *</td>
                                        <td>
                                            <textarea name="textRU" id="textRU"
                                                      cols="30" rows="3"
                                                      required
                                                      class="form-control ckeditor"><?= $item->textRU ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="200"><?=lang('Text')?> RO</td>
                                        <td>
                                            <textarea name="textRO" id="textRO"
                                                      cols="30" rows="3"
                                                      class="form-control ckeditor"><?= $item->textRO ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="200"><?=lang('Text')?> EN *</td>
                                        <td>
                                            <textarea name="textEN" id="textEN"
                                                      cols="30" rows="3" required
                                                      class="form-control ckeditor"><?= $item->textEN ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="200"><?=lang('Photo')?></td>
                                        <td>
                                            <input type="file" name="img"
                                                   id="file"
                                                   class="form-control">
                                            <div class="note note-warning"
                                                 style="margin-bottom: 0px; margin-top: 10px;">
                                                <p>
                                                    <?=lang('Allowable sizes')?>: 1110x370
                                                </p>
                                            </div>
                                            <?php if (!empty($item->img)): ?>
                                                <?php $src = newthumbs(
                                                    $item->img,
                                                    'constants',
                                                    250,
                                                    250,
                                                    '250x250x1',
                                                    1
                                                ) ?>
                                                <br>
                                                <div class="mt-element-card mt-element-overlay">
                                                    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                                        <div class="mt-card-item">
                                                            <div class="mt-card-avatar mt-overlay-1">
                                                                <img src="<?= $src ?>"/>
                                                                <div class="mt-overlay">
                                                                    <ul class="mt-info">
                                                                        <li>
                                                                            <a class="btn red mine_delete_photo"
                                                                               data-table="constants"
                                                                               data-id="<?= $item->id ?>"
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
                                            <button type="submit"
                                                    class="btn green"><i
                                                        class="fa fa-check"></i>
                                               <?=lang('Edit')?>
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
                                    <tr>
                                        <td width="200"><?=lang('Headline')?> RU *</td>
                                        <td>
                                            <input type="text"
                                                   name="seo{'title'.get_language_for_admin(true)}"
                                                   class="form-control" required
                                                   value="<?= $item->seo{'title'.get_language_for_admin(true)} ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="200"><?=lang('Headline')?> RO</td>
                                        <td>
                                            <input type="text"
                                                   name="seoTitleRO"
                                                   class="form-control"
                                                   value="<?= $item->seoTitleRO ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="200"><?=lang('Headline')?> EN *</td>
                                        <td>
                                            <input type="text"
                                                   name="seoTitleEN"
                                                   class="form-control" required
                                                   value="<?= $item->seoTitleEN ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="200"><?=lang('Keywords')?> RU *</td>
                                        <td>
                                            <textarea name="seoKeywordsRU"
                                                      id="seoKeywordsRU" cols="30"
                                                      rows="3" required
                                                      class="form-control"><?= $item->seoKeywordsRU ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="200"><?=lang('Keywords')?> RO</td>
                                        <td>
                                            <textarea name="seoKeywordsRO"
                                                      id="seoKeywordsRO" cols="30"
                                                      rows="3"
                                                      class="form-control"><?= $item->seoKeywordsRO ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="200"><?=lang('Keywords')?> EN *</td>
                                        <td>
                                            <textarea name="seoKeywordsEN"
                                                      id="seoKeywordsEN" cols="30"
                                                      rows="3" required
                                                      class="form-control"><?= $item->seoKeywordsEN ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="200"><?=lang('Description')?> RU *</td>
                                        <td>
                                            <textarea name="seoDescRU"
                                                      id="seoDescRU"
                                                      cols="30" rows="3" required
                                                      class="form-control"><?= $item->seoDescRU ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="200"><?=lang('Description')?> RO</td>
                                        <td>
                                            <textarea name="seoDescRO"
                                                      id="seoDescRO"
                                                      cols="30" rows="3"
                                                      class="form-control"><?= $item->seoDescRO ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="200"><?=lang('Description')?> EN *</td>
                                        <td>
                                            <textarea name="seoDescEN"
                                                      id="seoDescEN"
                                                      cols="30" rows="3" required
                                                      class="form-control"><?= $item->seoDescEN ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="200">&nbsp;</td>
                                        <td>
                                            <button type="submit"
                                                    class="btn green"><i
                                                        class="fa fa-check"></i>
                                               <?=lang('Edit')?>
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
