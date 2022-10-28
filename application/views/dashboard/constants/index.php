<?php
/**
 * Created by PhpStorm.
 * User: WhoAmI
 * Date: 01.03.2018
 * Time: 18:43
 */
?>
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

<?php if (!empty($constants)): ?>
    <form method="post">
        <?php foreach ($constants as $constant): ?>
            <div class="row">
                <div class="portlet box blue-hoki bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-cogs"></i>
                            <span class="caption-subject bold uppercase"><?= $constant->name ?></span>
                            <span class="caption-helper" style="color: #ffffff"><?= $constant->description ?></span>
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse"></a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-scrollable">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <? foreach(language(false) as $key=>$lang){ ?>
                                        <th class="text-center"><?=$lang?> (<?=strtoupper($key)?>)</th>
                                    <?}?>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php switch ($constant->fieldType) :
                                         case '0': ?>
                                        <?php $dopclass = ''; ?>
                                        <tr>
                                            <? foreach(language(true) as $lang){ ?>
                                                <td>
                                                    <input type="text" name="<?=$lang?>[<?= $constant->id ?>]" value="<?= $constant->{strtoupper($lang)} ?>" class="form-control <?= $dopclass ?>">
                                                </td>
                                            <?}?>
                                        </tr>
                                    <?php break; ?>
                                    <?php case '1': ?>
                                        <?php $dopclass = ''; ?>
                                        <tr>
                                            <? foreach(language(true) as $lang){ ?>
                                                <td>
                                                    <textarea name="<?=$lang?>[<?= $constant->id ?>]" cols="30" rows="5" class="form-control <?= $dopclass ?>"><?= $constant->{strtoupper($lang)} ?></textarea>
                                                </td>
                                            <?}?>
                                        </tr>
                                    <?php break; ?>
                                        <?php case '2': ?>
                                            <?php $dopclass = 'ckeditor'; ?>
                                            <tr>
                                                <? foreach(language(true) as $lang){ ?>
                                                    <td>
                                                        <textarea name="<?=$lang?>[<?= $constant->id ?>]" cols="30" rows="5" class="form-control <?= $dopclass ?>"><?= $constant->{strtoupper($lang)} ?></textarea>
                                                    </td>
                                                <?}?>   
                                            </tr>
                                        <?php break; ?>
                                    <?php endswitch; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn green"><i class="fa fa-check"></i><?=lang('Edit')?></button>
    </form>
<?php endif; ?>
