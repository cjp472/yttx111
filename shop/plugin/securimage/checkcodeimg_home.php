<?php
include 'securimage.php';

$img = new securimage();

//Change some settings
$img->image_width  = 80;
$img->image_height = 25;
$img->perturbation = 0.4;
$img->code_length  = 4;
$img->image_bg_color = new Securimage_Color("#ffffff");
$img->use_transparent_text = true;
$img->text_transparency_percentage = 30; // 100 = completely transparent
$img->num_lines = 1;
$img->image_signature = '';
$img->text_color = new Securimage_Color("#000000");
$img->line_color = new Securimage_Color("#eeeeee");

$img->show('backgrounds/bg4.jpg');