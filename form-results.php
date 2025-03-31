<?php
/*
Template Name: Form Results
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

get_header();
?>

<div class="heading-section">
  <div class="container">
    <h1>Prijava na Enterwell nagradnu igru!</h1>
    <h2>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</h2>
  </div>
</div>

<?php if (isset($_GET['success'])): ?>
  <div class="form-section">
    <div class="container">
      <div class="form-container results">
        <div class="form-results">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/images/icon-success.png" alt="Uspjeh">
          <h2>Uspješna prijava</h2>
          <p>Dok čekaš mail potvrde, vrati se i pročitaj zadnji korak na putu do nagrade.</p>
          <a href="<?php echo home_url(); ?>" class="btn btn-primary">Ok</a>
        </div>
      </div>
    </div>
  </div>
<?php else: ?>
  <div class="form-section">
    <div class="container">
      <div class="form-container results">
        <div class="form-results">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/images/icon-failed.png" alt="Greška">
          <h2>Neuspješna prijava</h2>
          <p>Došlo je do greške u komunikaciji. Provjeri svoju internetsku vezu i pokušaj ponovo.</p>
          <a href="<?php echo home_url(); ?>" class="btn btn-primary">Pokušaj ponovo</a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php get_footer(); ?>