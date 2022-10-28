<!DOCTYPE html>
<html lang="en">

    <head>
        <title><?= $page_title ?></title>
        <meta content="<?= $description_for_layout ?>" name="description">
        <meta content="<?= $keywords_for_layout ?>" name="keywords">
        <meta property="og:type" content="website"/>
        <meta property="og:title" content="<?= $otitle ?>"/>
        <meta property="og:description" content="<?= $description_for_layout ?>"/>
        <meta property="og:image" content="<?= $og_img ?>"/>
        <meta property="og:image:secure_url" content="<?= $site_url . $og_img ?>"/>
        <meta property="og:image:type" content="image/jpeg"/>
        <meta property="og:image:width" content="<?= $og_img_width ?>"/>
        <meta property="og:image:height" content="<?= $og_img_height ?>"/>
        <meta charset="utf-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="/dist/css/main.css">

        
    </head>

    <body>

        <div class="preloader"></div>
        <?php $this->load->view('/layouts/pages/header'); ?>
        <?php $this->load->view($inner_view); ?>
        <?php $this->load->view('/layouts/pages/footer'); ?>

    </body>

</html>
