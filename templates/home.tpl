<?php $this->layout('layout::base', ['title' => 'Demo']) ?>

<h1>Home</h1>

<ol>
    <?php foreach ($cities as $city) : ?>
    <li><?= $city ?></li>
    <?php endforeach ?>
</ol>
