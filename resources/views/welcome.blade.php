<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <link rel="stylesheet" href="{{ asset('custom.css') }}"> 
   
</head>
<body>

<div class="container mt-5">
    <!-- Search Bar -->
    <div class="row">
        <div class="col-md-12">
            <input type="text" id="search" class="form-control" placeholder="Search by name or role">
        </div>
    </div>

    <!-- Header Section: User List on Left, Add New User on Right -->
    <div class="row header-section">
        <div class="col-md-6">
            <h4>User List</h4>
        </div>
        <div class="col-md-6 text-end">
            <button id="addUserBtn" class="btn btn-primary">Add New User</button>
            <button id="cancelBtn" class="btn btn-secondary cancel-btn">Cancel</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form id="userForm" enctype="multipart/form-data" class="row g-3">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter name">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter email">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="number" class="form-control" name="phone" id="phone" placeholder="Enter phone">
                    </div>
                    <div class="col-md-6">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-select" name="role_id" id="role_id">
                            <option value="" disabled selected>Select a Role</option>
                            @if(isset($roles))
                            @foreach($roles as $role)
                                <option value="{{ $role['id'] }}">{{ $role['role_name'] }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="3" placeholder="Enter description"></textarea>
                        <span id="wordCount" style="float: right;">0/250</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="profile_image" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" name="profile_image" id="profile_image" accept="image/jpeg, image/png">
                        
                    </div>
                        <div class="col-md-6">
                            <div class="mt-2 d-flex justify-content-center"> <!-- Flexbox for centering -->
                                <img id="profile_pic" src="{{ url('/images/userImg.png') }}" alt="Profile Image" class="previewImage">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-center">
                        <button type="button" class="btn btn-primary" onclick="submitUserForm()">Submit</button>
                    </div>
            </form>
        </div>
    </div>

    <!-- User List Table -->
    <div class="row mt-4">
        <div class="col-md-12">
            <table class="table table-bordered table-hover" id="userTable">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th  style="width:20%">Description</th>
                        <th>Role</th>
                        <th>Profile Image</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    var baseurl = "{{ url('/') }}"; 
    const submitUserForm = () => {
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid'); 
        $('.error-message').remove(); 

        if ($('#userForm').valid()) {
            var formData = new FormData($('#userForm')[0]); 

            $.ajax({
                type: 'POST',
                url: '/api/submit-form', 
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    loadUsers();
                    $('#userForm')[0].reset();
                    $('#profile_pic').attr('src', baseurl + '/images/userImg.png');
                    $('#userForm').slideUp(); 
                    $('#addUserBtn').show(); 
                    $('#cancelBtn').hide(); 
                },
                error: function(response) {
                    // Clear all frontend validation error messages before showing backend errors
                    $('.is-invalid').removeClass('is-invalid');
                    $('.error-message').remove(); 

                    let errors = response.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        let inputElement = $(`#${field}`);
                        inputElement.addClass('is-invalid'); 
                        let errorMessage = `<div class="error-message">${messages[0]}</div>`;
                        inputElement.after(errorMessage); 
                    });
                }
            });
        }
    };

    $(document).ready(function() {
        // Toggle Add User Form
        $('#addUserBtn').click(function() {
            $('#userForm').slideDown(); 
            $('#addUserBtn').hide(); 
            $('#cancelBtn').show(); 
        });

        $('#cancelBtn').click(function() {
            $('#userForm').slideUp(); 
            $('#addUserBtn').show(); 
            $('#cancelBtn').hide();
            $('#userForm')[0].reset(); 
            $('#profile_pic').attr('src', '');
            $('.error-message').remove(); 
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
        // custom validations
        $.validator.addMethod("lettersOnly", function(value, element) {
            return this.optional(element) || /^[a-zA-Z\s]*$/.test(value); 
        }, "Name must contain only letters.");

        $.validator.addMethod("indianPhone", function(value, element) {
            return this.optional(element) || /^[6-9][0-9]{9}$/.test(value);
        }, "Please enter a valid Indian phone number.");

        $.validator.addMethod("validEmail", function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);
        }, "Please enter a valid email address.");
        
        $.validator.addMethod("maxSize", function(value, element, param) {
            if (element.files.length === 0) return true; 
            return element.files[0].size <= param * 1024; 
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
                $('.error-message').remove(); 
                element.after(error); 
            },
            highlight: function(element) {
                $(element).addClass('is-invalid'); 
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
                $('.error-message').remove(); 

            },
        });

        // Fetch initial user data 
        loadUsers();
    });

    function loadUsers(query = '') {
        $.ajax({
            type: 'GET',
            url: '/api/get-users', 
            data: { search: query },

            success: function(data) {
                const tableBody = $('#userTable tbody');
                tableBody.empty();
                if (data.length === 0) {
                const noRecordsRow = `<tr>
                    <td colspan="6" class="text-center">No records found</td>
                </tr>`;
                tableBody.append(noRecordsRow); 
            } else {
                data.forEach(user => {
                    const truncatedDescription = user.description.length > 50 ? user.description.substring(0, 50) + '...' : user.description;
                    const row = `<tr>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.phone}</td>
                        <td class = "text-ellipsis" title="${user.description}">${truncatedDescription}</td> 
                        <td >${user.role.role_name}</td>
                        <td><img src="${user.profile_image}" alt="${baseurl}."/images/userImg.png" class="previewImage"></td>

                    </tr>`;
                    tableBody.append(row);
                });
                $('#userTable').css('table-layout', 'fixed');
                $('#userTable th, #userTable td').css('overflow', 'hidden');
            }
        }
        });
    }
        
    
    // Image Preview 
    $("#profile_image").change(function() {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#profile_pic').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
    });
     // Search functionality
     $('#search').on('keyup', function() {
            let query = $(this).val();
            loadUsers(query); 
    });
</script>

</body>
</html>
