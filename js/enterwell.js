const giveawayForm = document.getElementById('giveaway-form');
if (giveawayForm) {
    giveawayForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const fields = form.querySelectorAll('input[required], textarea[required], select[required]');
        let valid = true;

        form.querySelectorAll('.form-group').forEach(el => el.classList.remove('error', 'email-error', 'account-number-error'));

        fields.forEach(field => {
            const formGroup = field.closest('.form-group');

            field.addEventListener('input', function () {
                formGroup.classList.remove('error', 'email-error', 'account-number-error');
            });

            field.addEventListener('focus', function () {
                formGroup.classList.remove('error');
            });

            if (!field.value || !field.value.trim()) {
                valid = false;
                formGroup.classList.add('error');
            }
        });

        const fileInput = form.querySelector('input[type="file"]');
        const file = fileInput.files[0];
        const allowedTypes = ['application/pdf', 'image/png', 'image/jpeg'];
        const fileFormGroup = fileInput.closest('.form-group');

        if (file) {
            if (!allowedTypes.includes(file.type)) {
                valid = false;
                fileFormGroup.classList.add('error');
            } else {
                fileFormGroup.classList.remove('error');
            }
        }

        if (!valid) return;

        // Show loader
        const loader = document.createElement('div');
        loader.className = 'loader';
        form.parentElement.appendChild(loader);
        form.classList.add('loading');

        const formData = new FormData(form);
        formData.append('action', 'check_user_exists');
        formData.append('_ajax_nonce', ajax_object.nonce);

        fetch(ajax_object.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.email_exists) {
                form.querySelector('[name="email"]').closest('.form-group').classList.add('error', 'email-error');
                valid = false;
            }

            if (data.account_number_exists) {
                form.querySelector('[name="account_number"]').closest('.form-group').classList.add('error', 'account-number-error');
                valid = false;
            }

            if (valid) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'submit_form';
                hiddenInput.value = '1';
                form.appendChild(hiddenInput);

                if (form.checkValidity()) {
                    setTimeout(() => {
                        form.submit();
                        loader.remove();
                        form.classList.remove('loading'); 
                    }, 3000);
                }
            } else {
                loader.remove(); 
                form.classList.remove('loading'); 
            }
        })
        .catch(error => {
            console.error('Error in AJAX call:', error);
            loader.remove(); 
            form.classList.remove('loading'); 
        });
    });
}

// Validation file
function validateFile(input) {
    const file = input.files[0];
    const allowedTypes = ['application/pdf', 'image/png', 'image/jpeg'];
    const fileNameElement = document.getElementById("file-name");
    const fileErrorElement = document.getElementById("file-error");
    const formGroupElement = input.closest('.form-group');

    if (file) {
        if (allowedTypes.includes(file.type)) {
            fileNameElement.innerHTML = file.name;
            fileErrorElement.style.display = 'none';
            formGroupElement.classList.remove('error');
            formGroupElement.classList.add('success');
        } else {
            fileNameElement.innerHTML = 'Prijenos nije uspio';
            fileErrorElement.style.display = 'block';
            formGroupElement.classList.remove('success');
            formGroupElement.classList.add('error');
        }
    } else {
        fileNameElement.innerHTML = '';
        fileErrorElement.style.display = 'none';
        formGroupElement.classList.remove('error', 'success');
    }
}

// Custom select dropdown
document.querySelectorAll('.custom-select-dropdown').forEach(dropdown => {
    const selectedOption = dropdown.querySelector('.selected-option');
    const options = dropdown.querySelector('.options');
    const formGroupElement = dropdown.closest('.form-group');

    selectedOption.addEventListener('click', () => {
        options.classList.toggle('show');
        dropdown.classList.toggle('focused', options.classList.contains('show'));
        if (options.classList.contains('show')) {
            formGroupElement.classList.remove('error');
        }
    });

    options.querySelectorAll('.option').forEach(option => {
        option.addEventListener('click', () => {
            const optionImage = option.querySelector('img').cloneNode(true);
            selectedOption.innerHTML = '';
            selectedOption.appendChild(optionImage);
            selectedOption.appendChild(document.createTextNode(option.innerText.trim()));
            const value = option.getAttribute('data-value');
            selectedOption.setAttribute('data-value', value);

            const hiddenInput = document.getElementById('country');
            hiddenInput.value = value;

            const event = new Event('change', { bubbles: true });
            hiddenInput.dispatchEvent(event);

            options.classList.remove('show');
            dropdown.classList.remove('focused');
        });
    });
});

// Drag & Drop support
const formContainer = document.querySelector('.form-container');
const dropArea = document.querySelector('.form-group.choose-document');
const fileInput = document.getElementById('file');
const fileError = document.getElementById('file-error');
let wasErrorBeforeDrag = false;
let wasSuccessBeforeDrag = false;

if (formContainer && dropArea && fileInput && fileError) {
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            formContainer.classList.add('drag-over');
            if (dropArea.classList.contains('error')) {
                wasErrorBeforeDrag = true;
                fileError.style.display = 'none'; 
            }
            if (dropArea.classList.contains('success')) {
                wasSuccessBeforeDrag = true;
            }
            dropArea.classList.remove('error', 'success');
        });
    });

    dropArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (!dropArea.contains(e.relatedTarget)) {
            formContainer.classList.remove('drag-over');
            if (wasErrorBeforeDrag) {
                dropArea.classList.add('error');
                fileError.style.display = 'block';
                wasErrorBeforeDrag = false;
            }
            if (wasSuccessBeforeDrag) {
                dropArea.classList.add('success');
                wasSuccessBeforeDrag = false;
            }
        }
    });

    dropArea.addEventListener('drop', (e) => {
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            validateFile(fileInput);
        }
        formContainer.classList.remove('drag-over');
        wasErrorBeforeDrag = false;
        wasSuccessBeforeDrag = false;
    });
}
