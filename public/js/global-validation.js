/**
 * Global Validation System for All Modules
 * Provides consistent client-side validation across the entire application
 */

class GlobalValidator {
    constructor() {
        this.initializeValidation();
        this.setupEventListeners();
    }

    // Common validation patterns
    patterns = {
        name: /^[a-zA-Z\s\-\.\']+$/,
        businessName: /^[a-zA-Z0-9\s\-\.\&\(\)]+$/,
        phone: /^[\+]?[0-9\s\-\(\)]+$/,
        currency: /^[A-Z]{3}$/,
        offerNumber: /^[A-Z0-9\-]+$/,
        alphanumeric: /^[a-zA-Z0-9\s\-#]+$/,
        urlFriendly: /^[a-zA-Z0-9\-_]+$/
    };

    // Common validation rules by field type
    fieldRules = {
        // Names and Text
        'client_name': {
            required: true,
            minlength: 2,
            maxlength: 191,
            pattern: this.patterns.name
        },
        'name': {
            required: true,
            minlength: 2,
            maxlength: 191,
            pattern: this.patterns.name
        },
        'first_name': {
            required: true,
            minlength: 2,
            maxlength: 191,
            pattern: this.patterns.name
        },
        'tour_name': {
            required: true,
            minlength: 3,
            maxlength: 191,
            pattern: this.patterns.alphanumeric
        },
        'hotel_name': {
            required: true,
            minlength: 2,
            maxlength: 191,
            pattern: this.patterns.businessName
        },
        'restaurant_name': {
            required: true,
            minlength: 2,
            maxlength: 191,
            pattern: this.patterns.businessName
        },

        // Contact Information
        'work_phone': {
            required: true,
            pattern: this.patterns.phone
        },
        'contact_phone': {
            required: false,
            pattern: this.patterns.phone
        },
        'work_email': {
            required: true,
            email: true
        },
        'contact_email': {
            required: false,
            email: true
        },

        // Financial
        'total_amount': {
            required: false,
            min: 0,
            max: 999999.99,
            decimal: true
        },
        'price_for_one': {
            required: false,
            min: 0,
            max: 99999.99,
            decimal: true
        },
        'currency': {
            required: true,
            pattern: this.patterns.currency
        },

        // Passenger Information
        'pax': {
            required: true,
            min: 1,
            max: 500,
            integer: true
        },
        'pax_free': {
            required: false,
            min: 0,
            max: 50,
            integer: true
        },

        // Tour-specific fields
        'child_count': {
            required: false,
            min: 0,
            max: 100,
            integer: true
        },
        'passenger_count': {
            required: true,
            min: 1,
            max: 500,
            integer: true
        },

        // Dates
        'departure_date': {
            required: true,
            date: true,
            futureDate: true
        },
        'retirement_date': {
            required: true,
            date: true,
            afterField: 'departure_date'
        },
        'offer_date': {
            required: true,
            date: true,
            futureDate: true
        },
        'valid_until': {
            required: true,
            date: true,
            afterField: 'offer_date'
        },

        // Location
        'country': {
            required: true,
            minlength: 2,
            maxlength: 191,
            pattern: this.patterns.name
        },
        'city': {
            required: true,
            minlength: 2,
            maxlength: 191,
            pattern: this.patterns.name
        },
        'address': {
            required: true,
            minlength: 5,
            maxlength: 500
        }
    };

    // Error messages
    errorMessages = {
        required: 'This field is required',
        email: 'Please enter a valid email address',
        minlength: 'Must be at least {0} characters',
        maxlength: 'Cannot exceed {0} characters',
        min: 'Must be at least {0}',
        max: 'Cannot exceed {0}',
        pattern: 'Invalid format',
        integer: 'Must be a whole number',
        decimal: 'Must be a valid number',
        date: 'Please enter a valid date',
        futureDate: 'Date cannot be in the past',
        afterField: 'Must be after {0}',
        fileSize: 'File size cannot exceed {0}MB',
        fileType: 'Invalid file type. Allowed: {0}'
    };

    // Field-specific error messages
    fieldMessages = {
        'client_name': {
            pattern: 'Name can only contain letters, spaces, hyphens, dots, and apostrophes'
        },
        'tour_name': {
            pattern: 'Tour name can only contain letters, numbers, spaces, hyphens, and hash symbols'
        },
        'hotel_name': {
            pattern: 'Hotel name can only contain letters, numbers, spaces, and common business symbols'
        },
        'work_phone': {
            pattern: 'Please enter a valid phone number'
        },
        'currency': {
            pattern: 'Currency must be a 3-letter code (e.g., USD, EUR)'
        },
        'pax': {
            min: 'At least 1 passenger is required',
            max: 'Maximum 500 passengers allowed'
        },
        'pax_free': {
            max: 'Maximum 50 free passengers allowed'
        }
    };

    initializeValidation() {
        console.log('Global validation system initialized');
    }

    setupEventListeners() {
        // Real-time validation on blur
        $(document).on('blur', 'input, select, textarea', (e) => {
            this.validateField($(e.target));
        });

        // Date validation on change
        $(document).on('change', 'input[type="date"]', (e) => {
            this.validateField($(e.target));
            this.validateRelatedDateFields($(e.target));
        });

        // Passenger count validation
        $(document).on('input', 'input[name="pax"], input[name="pax_free"]', (e) => {
            setTimeout(() => this.validatePassengerLogic(), 100);
        });

        // File upload validation
        $(document).on('change', 'input[type="file"]', (e) => {
            this.validateFileUpload($(e.target));
        });

        // Form submission validation
        $(document).on('submit', 'form', (e) => {
            if (!this.validateForm($(e.target))) {
                e.preventDefault();
                this.showFormErrors($(e.target));
                return false;
            }
        });
    }

    validateField($field) {
        const fieldName = $field.attr('name');
        const value = $field.val();
        const rules = this.getFieldRules(fieldName);

        if (!rules) return true;

        const errors = this.checkFieldRules(fieldName, value, rules);

        if (errors.length > 0) {
            this.showFieldError($field, errors[0]);
            return false;
        } else {
            this.clearFieldError($field);
            return true;
        }
    }

    getFieldRules(fieldName) {
        // Direct match
        if (this.fieldRules[fieldName]) {
            return this.fieldRules[fieldName];
        }

        // Pattern matching for dynamic field names
        for (const pattern in this.fieldRules) {
            if (fieldName.includes(pattern)) {
                return this.fieldRules[pattern];
            }
        }

        return null;
    }

    checkFieldRules(fieldName, value, rules) {
        const errors = [];

        // Required validation
        if (rules.required && (!value || value.trim() === '')) {
            errors.push(this.getErrorMessage(fieldName, 'required'));
            return errors; // If required fails, don't check other rules
        }

        // Skip other validations if field is empty and not required
        if (!value || value.trim() === '') {
            return errors;
        }

        // Length validations
        if (rules.minlength && value.length < rules.minlength) {
            errors.push(this.getErrorMessage(fieldName, 'minlength', rules.minlength));
        }

        if (rules.maxlength && value.length > rules.maxlength) {
            errors.push(this.getErrorMessage(fieldName, 'maxlength', rules.maxlength));
        }

        // Numeric validations
        if (rules.min !== undefined && parseFloat(value) < rules.min) {
            errors.push(this.getErrorMessage(fieldName, 'min', rules.min));
        }

        if (rules.max !== undefined && parseFloat(value) > rules.max) {
            errors.push(this.getErrorMessage(fieldName, 'max', rules.max));
        }

        // Type validations
        if (rules.email && !this.isValidEmail(value)) {
            errors.push(this.getErrorMessage(fieldName, 'email'));
        }

        if (rules.integer && !Number.isInteger(parseFloat(value))) {
            errors.push(this.getErrorMessage(fieldName, 'integer'));
        }

        if (rules.decimal && isNaN(parseFloat(value))) {
            errors.push(this.getErrorMessage(fieldName, 'decimal'));
        }

        if (rules.date && !this.isValidDate(value)) {
            errors.push(this.getErrorMessage(fieldName, 'date'));
        }

        // Pattern validation
        if (rules.pattern && !rules.pattern.test(value)) {
            errors.push(this.getErrorMessage(fieldName, 'pattern'));
        }

        // Future date validation
        if (rules.futureDate && this.isValidDate(value) && new Date(value) < new Date()) {
            errors.push(this.getErrorMessage(fieldName, 'futureDate'));
        }

        return errors;
    }

    validateRelatedDateFields($field) {
        const fieldName = $field.attr('name');

        // Find fields that depend on this date field
        $('input[type="date"]').each((index, element) => {
            const $relatedField = $(element);
            const relatedFieldName = $relatedField.attr('name');
            const rules = this.getFieldRules(relatedFieldName);

            if (rules && rules.afterField === fieldName) {
                this.validateField($relatedField);
            }
        });
    }

    validatePassengerLogic() {
        const pax = parseInt($('input[name="pax"]').val()) || 0;
        const paxFree = parseInt($('input[name="pax_free"]').val()) || 0;

        if (paxFree > pax) {
            this.showFieldError($('input[name="pax_free"]'), 'Free passengers cannot exceed total passengers');
            return false;
        }

        this.clearFieldError($('input[name="pax_free"]'));
        return true;
    }

    validateFileUpload($field) {
        const files = $field[0].files;
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf',
                             'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            // Size validation
            if (file.size > maxSize) {
                this.showFieldError($field, this.getErrorMessage($field.attr('name'), 'fileSize', '10'));
                $field.val('');
                return false;
            }

            // Type validation
            if (!allowedTypes.includes(file.type)) {
                this.showFieldError($field, this.getErrorMessage($field.attr('name'), 'fileType', 'JPEG, PNG, GIF, PDF, DOC, DOCX'));
                $field.val('');
                return false;
            }
        }

        this.clearFieldError($field);
        return true;
    }

    validateForm($form) {
        let isValid = true;
        const $fields = $form.find('input, select, textarea');

        $fields.each((index, element) => {
            if (!this.validateField($(element))) {
                isValid = false;
            }
        });

        // Additional business logic validations
        if (!this.validatePassengerLogic()) {
            isValid = false;
        }

        return isValid;
    }

    showFieldError($field, message) {
        this.clearFieldError($field);

        $field.addClass('is-invalid');
        $field.after(`<div class="validation-error text-danger small mt-1">${message}</div>`);

        // Also show in parent if it's a form group
        const $formGroup = $field.closest('.form-group, .input-group');
        if ($formGroup.length > 0) {
            $formGroup.addClass('has-error');
        }
    }

    clearFieldError($field) {
        $field.removeClass('is-invalid');
        $field.siblings('.validation-error').remove();

        const $formGroup = $field.closest('.form-group, .input-group');
        if ($formGroup.length > 0) {
            $formGroup.removeClass('has-error');
        }
    }

    showFormErrors($form) {
        const $firstError = $form.find('.is-invalid').first();

        if ($firstError.length > 0) {
            $firstError.focus();

            // Show general error message
            if ($form.find('.form-validation-summary').length === 0) {
                $form.prepend(`
                    <div class="alert alert-danger form-validation-summary">
                        <strong>Please correct the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            ${$form.find('.validation-error').map((i, el) => `<li>${$(el).text()}</li>`).get().join('')}
                        </ul>
                    </div>
                `);
            }
        }
    }

    getErrorMessage(fieldName, type, param = null) {
        // Check for field-specific message first
        if (this.fieldMessages[fieldName] && this.fieldMessages[fieldName][type]) {
            return this.fieldMessages[fieldName][type];
        }

        // Use general message
        let message = this.errorMessages[type] || 'Invalid value';

        // Replace parameter placeholders
        if (param !== null) {
            message = message.replace('{0}', param);
        }

        return message;
    }

    // Utility functions
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    isValidDate(dateString) {
        const date = new Date(dateString);
        return date instanceof Date && !isNaN(date);
    }

    // Public method to add custom validation rules
    addFieldRule(fieldName, rules) {
        this.fieldRules[fieldName] = rules;
    }

    // Public method to add custom error messages
    addFieldMessage(fieldName, messages) {
        if (!this.fieldMessages[fieldName]) {
            this.fieldMessages[fieldName] = {};
        }
        Object.assign(this.fieldMessages[fieldName], messages);
    }
}

// Initialize global validation system
$(document).ready(() => {
    window.globalValidator = new GlobalValidator();
    console.log('Global validation system ready');
});