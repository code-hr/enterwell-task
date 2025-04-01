<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>

<div class="form-section">
  <div class="container">
    <div class="form-container">
      <form id="giveaway-form" method="post" enctype="multipart/form-data" novalidate>
        <?php wp_nonce_field('enterwell_giveaway_form', 'enterwell_nonce'); ?>

        <div class="form-content">
          <div class="form-post-left">
            <div class="form-group choose-document">
              <div class="file-upload">
                <span class="file-upload-icon"></span>
                <span id="file-name" class="file-box"></span>
                <div class="inputfile-box">
                  <input type="file" id="file" class="inputfile" name="document" onchange="validateFile(this)">
                  <div class="file-box">
                    <p>Povuci i ispusti datoteku kako bi započeo prijenos<br>ili <label for="file"><span class="file-button">pretraži računalo.</span></label></p>
                  </div>
                  <span class="drop-file">Ispusti datoteku</span>
                </div>
                <span class="file-format">PODRŽANI FORMATI:<br>pdf, png, jpg</span>
              </div>
              <span class="error-message" id="file-error">* Format nije podržan</span>
            </div>

            <div class="form-group">
              <input type="number" class="form-control" id="account_number" name="account_number" placeholder="Broj računa*" required>
              <label for="account_number" class="input-label">Broj računa*</label>
              <span class="error-message">* Obavezna ispuna polja</span>
              <span class="required-focus">* Obavezno</span>
              <span class="error-message account-number-error">* Korisnik s ovim brojem računa već postoji</span>
            </div>
          </div>

          <div class="form-post-right">
            <div class="form-group">
              <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Ime*" required>
              <label for="first_name" class="input-label">Ime*</label>
              <span class="error-message">* Obavezna ispuna polja</span>
              <span class="required-focus">* Obavezno</span>
            </div>

            <div class="form-group">
              <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Prezime*" required>
              <label for="last_name" class="input-label">Prezime*</label>
              <span class="error-message">* Obavezna ispuna polja</span>
              <span class="required-focus">* Obavezno</span>
            </div>

            <div class="form-group">
              <input type="text" class="form-control" id="address" name="address" placeholder="Adresa*" required>
              <label for="address" class="input-label">Adresa*</label>
              <span class="error-message">* Obavezna ispuna polja</span>
              <span class="required-focus">* Obavezno</span>
            </div>

            <div class="form-group">
              <input type="number" class="form-control" id="house_number" name="house_number" placeholder="Kućni broj*" required>
              <label for="house_number" class="input-label">Kućni broj*</label>
              <span class="error-message">* Obavezna ispuna polja</span>
              <span class="required-focus">* Obavezno</span>
            </div>

            <div class="form-group">
              <input type="text" class="form-control" id="city" name="city" placeholder="Mjesto*" required>
              <label for="city" class="input-label">Mjesto*</label>
              <span class="error-message">* Obavezna ispuna polja</span>
              <span class="required-focus">* Obavezno</span>
            </div>

            <div class="form-group">
              <input type="number" class="form-control" id="zip_code" name="zip_code" placeholder="Poštanski broj*" required>
              <label for="zip_code" class="input-label">Poštanski broj*</label>
              <span class="error-message">* Obavezna ispuna polja</span>
              <span class="required-focus">* Obavezno</span>
            </div>

            <div class="form-group">
              <input type="hidden" id="country" name="country" value="" required aria-hidden="true">
              <div class="custom-select-dropdown" role="listbox" aria-labelledby="country-label">
                <div class="selected-option" id="selected-country" data-value="" required>Država*</div>
                <div class="options">
                  <div class="option" data-value="Croatia" role="option">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/flags/croatia.svg" alt="Croatia">
                    Hrvatska
                  </div>
                  <div class="option" data-value="Bosnia and Herzegovina" role="option">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/flags/bosnia.svg" alt="Bosnia and Herzegovina">
                    Bosna i Hercegovina
                  </div>
                </div>
              </div>
              <label class="input-label">Država*</label>
              <span class="error-message">* Obavezna ispuna polja</span>
              <span class="required-focus">* Obavezno</span>
            </div>

            <div class="form-group">
              <input type="tel" class="form-control" id="mobile" name="mobile" placeholder="Kontakt telefon*" required>
              <label for="mobile" class="input-label">Kontakt telefon*</label>
              <span class="error-message">* Obavezna ispuna polja</span>
              <span class="required-focus">* Obavezno</span>
            </div>

            <div class="form-group">
              <input type="email" class="form-control" id="email" name="email" placeholder="Email*" required>
              <label for="email" class="input-label">Email*</label>
              <span class="error-message">* Obavezna ispuna polja</span>
              <span class="required-focus">* Obavezno</span>
              <span class="error-message email-error">* Korisnik s ovim emailom već postoji</span>
            </div>
          </div>
        </div>

        <button type="submit" class="submit-form btn-primary">Pošalji</button>
      </form>
    </div>
  </div>
</div>