const submitUserForm = () => {
    // Clear previous errors
    $('.is-invalid').removeClass('is-invalid'); // Remove error class from inputs
    $('.error-message').remove(); // Remove all error messages

    // Validate the form (frontend validation)
    if ($('#userForm').valid()) {
        var formData = new FormData($('#userForm')[0]); // Use FormData object

        $.ajax({
            type: 'POST',
            url: '/api/submit-form', // Adjust the endpoint to your server-side route
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                loadUsers();
                // Reset form and hide it after successful submission
                $('#userForm')[0].reset();
                $('#profile_pic').attr('src', baseurl + '/images/userImg.png');
                $('#userForm').slideUp(); // Hide form
                $('#addUserBtn').show(); // Show Add button
                $('#cancelBtn').hide(); // Hide Cancel button
            },
            error: function(response) {
                // Clear all frontend validation error messages before showing backend errors
                $('.is-invalid').removeClass('is-invalid');
                $('.error-message').remove(); // Clear frontend errors

                let errors = response.responseJSON.errors;
                let errorBox = $('#errorBox');
                errorBox.html(''); // Clear previous errors

                // Display backend validation errors under the respective input fields
                $.each(errors, function(field, messages) {
                    let inputElement = $(`#${field}`);
                    inputElement.addClass('is-invalid'); // Add error class

                    // Create and insert the error message below the input
                    let errorMessage = `<div class="error-message">${messages[0]}</div>`;
                    inputElement.after(errorMessage); // Show error message under input
                });

                // Scroll to the first error
                $('html, body').animate({
                    scrollTop: $('.is-invalid:first').offset().top - 50
                }, 500);
            }
        });
    }
};

$(document).ready(function() {
    // Toggle Add User Form
    $('#addUserBtn').click(function() {
        $('#userForm').slideDown(); // Show form
        $('#addUserBtn').hide(); // Hide "Add New User" button
        $('#cancelBtn').show(); // Show Cancel button
    });

    $('#cancelBtn').click(function() {
        $('#userForm').slideUp(); // Hide form
        $('#addUserBtn').show(); // Show "Add New User" button
        $('#cancelBtn').hide(); // Hide Cancel button
        $('#userForm')[0].reset(); // Reset form when cancelled
        $('#profile_pic').attr('src', ''); // Clear the preview image when cancelled
        $('.error-message').remove(); // Remove validation error messages
    });

    $('#description').on('input', function() {
    const maxLength = 250;
    const currentLength = $(this).val().length;
    $('#wordCount').text(`${currentLength}/${maxLength}`);
    if (currentLength >= maxLength) {
    $('#wordCount').css('color', 'red');
        } else {
            $('#wordCount').css('color', ''); 
        }

    });

    $.validator.addMethod("lettersOnly", function(value, element) {
        return this.optional(element) || /^[a-zA-Z\s]*$/.test(value); // Allow letters and spaces
    }, "Name must contain only letters.");

    $.validator.addMethod("indianPhone", function(value, element) {
        return this.optional(element) || /^[6-9][0-9]{9}$/.test(value);
    }, "Please enter a valid Indian phone number.");

    // Add custom method to validate email format
    $.validator.addMethod("validEmail", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);
    }, "Please enter a valid email address.");
    
    $.validator.addMethod("maxSize", function(value, element, param) {
        if (element.files.length === 0) return true; // No file selected
        return element.files[0].size <= param * 1024; // Convert KB to bytes
    }, "File size must be less than {0} MB.");

    $('#userForm').validate({
        rules: {
            name: {
                required: true,
                minlength: 3,
                maxlength: 50, 
                lettersOnly: true 
            },
            email: {
                required: true,
                validEmail: true 
            },
            phone: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 10,
                indianPhone: true 
            },
            description: {
                required: true,
                maxlength: 250 
            },
            role_id: {
                required: true
            },
            profile_image: {
                required: true,
                maxSize: 2048 
            }
        },
        messages: {
            name: {
                required: "Please enter your name",
                minlength: "Name must be at least 3 characters long",
                maxlength: "Name must not exceed 50 characters",
                lettersOnly: "Name must contain only letters"
            },
            email: {
                required: "Please enter your email",
                validEmail: "Please enter a valid email address"
            },
            phone: {
                required: "Please enter your phone number",
                digits: "Please enter only numbers",
                minlength: "Phone number must be 10 digits",
                maxlength: "Phone number must be 10 digits",
                indianPhone: "Only Indian phone numbers are allowed"
            },
            description: {
                required: "Please enter a description",
                maxlength: "Description must not exceed 250 characters"
            },
            role_id: {
                required: "Please select a role"
            },
            profile_image: {
                required: "Please upload a profile image"
            }
        },
        errorPlacement: function(error, element) {
            $('.error-message').remove(); // Clear previous errors
            element.after(error); // Show error messages after the input field
        },
        highlight: function(element) {
            $(element).addClass('is-invalid'); // Add error class
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid'); // Remove error class
        },
    });

    // Fetch initial user data and populate the table
    loadUsers();
});

function loadUsers() {
    $.ajax({
        type: 'GET',
        url: '/api/get-users', // Adjust the endpoint to your server-side route
        success: function(data) {
            const tableBody = $('#userTable tbody');
            tableBody.empty(); // Clear the table before adding new rows

            // Iterate through user data
            data.forEach(user => {
                const truncatedDescription = user.description.length > 50 ? user.description.substring(0, 50) + '...' : user.description;
                const row = `<tr>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>${user.phone}</td>
                    <td title="${user.description}">${truncatedDescription}</td> <!-- Full description on hover -->
                    <td>${user.role.role_name}</td>
                    <td><img src="${user.profile_image}" alt="${baseurl}."/images/userImg.png" class="previewImage"></td>

                </tr>`;
                tableBody.append(row);
            });

            // Optional: Set fixed column size for table (using CSS)
            $('#userTable').css('table-layout', 'fixed');
            $('#userTable th, #userTable td').css('overflow', 'hidden');
        }
    });
}
    