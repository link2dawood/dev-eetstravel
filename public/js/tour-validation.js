/**
 * Tour Form Validation - Client Side
 * Provides real-time validation for tour creation/editing forms
 */

$(document).ready(function() {
    // Form selector
    const tourForm = '#tour-form, [id*="tour"], form[action*="tour"]';

    // Validation rules
    const validationRules = {
        name: {
            required: true,
            minlength: 3,
            maxlength: 191,
            pattern: /^[a-zA-Z0-9\s\-#]+$/
        },
        external_name: {
            required: true,
            minlength: 3,
            maxlength: 191,
            pattern: /^[a-zA-Z0-9\-_]+$/
        },
        departure_date: {
            required: true,
            date: true
        },
        retirement_date: {
            required: true,
            date: true
        },
        pax: {
            required: true,
            min: 1,
            max: 500,
            integer: true
        },
        pax_free: {
            min: 0,
            max: 50,
            integer: true
        },
        phone: {
            required: true,
            pattern: /^[\+]?[0-9\s\-\(\)]+$/
        },
        total_amount: {
            min: 0,
            max: 999999.99,
            decimal: true
        },
        price_for_one: {
            min: 0,
            max: 99999.99,
            decimal: true
        }
    };

    // Error messages
    const errorMessages = {
        name: {
            required: 'Tour name is required',
            minlength: 'Tour name must be at least 3 characters',
            maxlength: 'Tour name cannot exceed 191 characters',
            pattern: 'Tour name can only contain letters, numbers, spaces, hyphens, and hash symbols'
        },
        external_name: {
            required: 'External name is required',
            pattern: 'External name must be URL-friendly (letters, numbers, hyphens, underscores only)'
        },
        departure_date: {
            required: 'Departure date is required',
            date: 'Please enter a valid departure date'
        },
        retirement_date: {
            required: 'Return date is required',
            date: 'Please enter a valid return date'
        },
        pax: {
            required: 'Number of passengers is required',
            min: 'At least 1 passenger is required',
            max: 'Maximum 500 passengers allowed',
            integer: 'Number of passengers must be a whole number'
        },
        pax_free: {
            max: 'Maximum 50 free passengers allowed',
            integer: 'Number of free passengers must be a whole number'
        },
        phone: {
            required: 'Contact phone number is required',
            pattern: 'Please enter a valid phone number'
        }
    };

    // Validation functions
    function validateField(field, value, rules) {
        const errors = [];

        if (rules.required && (!value || value.trim() === '')) {
            errors.push(errorMessages[field]?.required || 'This field is required');
        }

        if (value && rules.minlength && value.length < rules.minlength) {
            errors.push(errorMessages[field]?.minlength || `Minimum ${rules.minlength} characters required`);
        }

        if (value && rules.maxlength && value.length > rules.maxlength) {
            errors.push(errorMessages[field]?.maxlength || `Maximum ${rules.maxlength} characters allowed`);
        }

        if (value && rules.min && parseFloat(value) < rules.min) {
            errors.push(errorMessages[field]?.min || `Minimum value is ${rules.min}`);
        }

        if (value && rules.max && parseFloat(value) > rules.max) {
            errors.push(errorMessages[field]?.max || `Maximum value is ${rules.max}`);
        }

        if (value && rules.pattern && !rules.pattern.test(value)) {
            errors.push(errorMessages[field]?.pattern || 'Invalid format');
        }

        if (value && rules.integer && !Number.isInteger(parseFloat(value))) {
            errors.push(errorMessages[field]?.integer || 'Must be a whole number');
        }

        if (value && rules.date && !isValidDate(value)) {
            errors.push(errorMessages[field]?.date || 'Invalid date format');
        }

        return errors;
    }

    function isValidDate(dateString) {
        const date = new Date(dateString);
        return date instanceof Date && !isNaN(date);
    }

    function validateDateRange() {
        const departureDate = $('[name="departure_date"]').val();
        const retirementDate = $('[name="retirement_date"]').val();

        if (departureDate && retirementDate) {
            const departure = new Date(departureDate);
            const retirement = new Date(retirementDate);

            if (retirement < departure) {
                showFieldError('retirement_date', 'Return date must be on or after departure date');
                return false;
            }

            // Check duration (90 days max)
            const duration = Math.ceil((retirement - departure) / (1000 * 60 * 60 * 24));
            if (duration > 90) {
                showFieldError('retirement_date', 'Tour duration cannot exceed 90 days');
                return false;
            }

            clearFieldError('retirement_date');
            return true;
        }
        return true;
    }

    function validatePaxLogic() {
        const pax = parseInt($('[name="pax"]').val()) || 0;
        const paxFree = parseInt($('[name="pax_free"]').val()) || 0;

        if (paxFree > pax) {
            showFieldError('pax_free', 'Free passengers cannot exceed total passengers');
            return false;
        }

        clearFieldError('pax_free');
        return true;
    }

    function showFieldError(fieldName, message) {
        const field = $(`[name="${fieldName}"]`);
        const errorContainer = field.siblings('.validation-error');

        if (errorContainer.length === 0) {
            field.after(`<div class="validation-error text-danger small">${message}</div>`);
        } else {
            errorContainer.text(message);
        }

        field.addClass('is-invalid');
    }

    function clearFieldError(fieldName) {
        const field = $(`[name="${fieldName}"]`);
        field.removeClass('is-invalid');
        field.siblings('.validation-error').remove();
    }

    function validateForm() {
        let isValid = true;

        // Validate each field
        Object.keys(validationRules).forEach(fieldName => {
            const field = $(`[name="${fieldName}"]`);
            if (field.length > 0) {
                const value = field.val();
                const rules = validationRules[fieldName];
                const errors = validateField(fieldName, value, rules);

                if (errors.length > 0) {
                    showFieldError(fieldName, errors[0]);
                    isValid = false;
                } else {
                    clearFieldError(fieldName);
                }
            }
        });

        // Additional business logic validation
        if (!validateDateRange()) {
            isValid = false;
        }

        if (!validatePaxLogic()) {
            isValid = false;
        }

        return isValid;
    }

    // Real-time validation
    $(document).on('blur', '[name]', function() {
        const fieldName = $(this).attr('name');
        if (validationRules[fieldName]) {
            const value = $(this).val();
            const rules = validationRules[fieldName];
            const errors = validateField(fieldName, value, rules);

            if (errors.length > 0) {
                showFieldError(fieldName, errors[0]);
            } else {
                clearFieldError(fieldName);
            }
        }
    });

    // Date range validation
    $(document).on('change', '[name="departure_date"], [name="retirement_date"]', function() {
        setTimeout(validateDateRange, 100);
    });

    // Passenger validation
    $(document).on('input', '[name="pax"], [name="pax_free"]', function() {
        setTimeout(validatePaxLogic, 100);
    });

    // Form submission validation
    $(document).on('submit', tourForm, function(e) {
        if (!validateForm()) {
            e.preventDefault();

            // Show general error message
            if ($('.form-validation-summary').length === 0) {
                $(this).prepend('<div class="alert alert-danger form-validation-summary">Please correct the errors below before submitting.</div>');
            }

            // Focus on first error field
            $('.is-invalid').first().focus();

            return false;
        }

        // Remove validation summary if form is valid
        $('.form-validation-summary').remove();
    });

    // File upload validation
    $(document).on('change', 'input[type="file"]', function() {
        const files = this.files;
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            if (file.size > maxSize) {
                showFieldError($(this).attr('name'), `File "${file.name}" exceeds 10MB limit`);
                $(this).val('');
                return;
            }

            if (!allowedTypes.includes(file.type)) {
                showFieldError($(this).attr('name'), `File "${file.name}" has invalid type. Allowed: JPEG, PNG, GIF, PDF, DOC, DOCX`);
                $(this).val('');
                return;
            }
        }

        clearFieldError($(this).attr('name'));
    });

    console.log('Tour validation initialized');
});