<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

get_header(); ?>

<div class="heading-section">
    <div class="container">
        <h1>Prijava na Enterwell nagradnu igru!</h1>
        <h2>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</h2>
    </div>
</div>

<?php include(get_template_directory() . '/components/enterwell-giveaway-form.php'); ?>

<?php get_footer(); ?>
