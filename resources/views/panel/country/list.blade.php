@extends('panel.layout')

@section('content')
    <div class="dashboard__inner__item dashboard__card bg__white padding-20 radius-10">

        <h4 class="dashboard__inner__item__header__title">{{$title}}</h4>
        <button type="button" class="btn btn-success float-end" data-bs-toggle="modal"
                data-bs-target="#addCountry"><i data-feather="plus-circle"></i> Add Country
        </button>
        <br>
        <!-- Table Design One -->
        <div class="tableStyle_one mt-4">
            <div class="table_wrapper">
                <!-- Table -->
                <table id="countryTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th class="text-center">Name</th>
                        <th class="text-end">Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
        </div>
        <!-- End-of Table one -->
    </div>
    {{--for add modal --}}
    <div class="modal fade" id="addCountry" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Country</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="countryForm">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Name:</label>
                            <input type="text" class="form-control" id="recipient-name" name="name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveButton">Save</button>
                </div>
            </div>
        </div>
    </div>
    {{--    For Edit Modal--}}
    <div class="modal fade" id="editCountryModal" tabindex="-1" aria-labelledby="editCountryLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCountryLabel">Edit Country</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCountryForm">
                        <input type="hidden" id="editCountryId">
                        <div class="mb-3">
                            <label for="editCountryName" class="col-form-label">Country Name:</label>
                            <input type="text" class="form-control" id="editCountryName" name="name">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="updateCountryButton">Update</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('script')
    <script>
        $(document).ready(function () {
            // Function to fetch countries and populate the table
            function fetchCountries() {
                $.ajax({
                    url: '{{route('ajax.fetch_countries')}}',
                    type: 'GET',
                    success: function (response) {
                        // Clear the table body
                        $('#countryTable tbody').empty();

                        // Populate the table with data
                        response.forEach(function (country, index) {
                            $('#countryTable tbody').append(`
                        <tr data-id="${country.id}">
                            <td>${index + 1}</td>
                            <td class="text-center">${country.name}</td>
                            <td class="text-end">
                                <div class="dropdown custom__dropdown">
                                    <button class="dropdown-toggle" type="button" id="dropdownMenuButton${country.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="las la-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton${country.id}">
                                        <li><a class="dropdown-item" href="#" onclick="editCountry(${country.id})">Edit</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="deleteCountry(${country.id})">Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    `);
                        });
                    },
                    error: function (xhr) {
                        const errorMessage = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'An unexpected error occurred. Please try again.';

                        iziToast.error({
                            title: 'Error',
                            message: errorMessage
                        });
                    }
                });
            }

            // Call fetchCountries function on page load
            fetchCountries();


            // Add country
                $('#saveButton').on('click', function (e) {
                    e.preventDefault();

                    $.ajax({
                        url: "{{ route('ajax.store_countries') }}", // Make sure this route exists
                        type: 'POST',
                        data: $('#countryForm').serialize(), // Serialize the form data correctly
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                        },
                        success: function (response) {

                            iziToast.success({message: response.message});
                            $('#countryForm')[0].reset(); // Clear form fields
                            $('#addCountry').modal('hide'); // Hide the modal
                            // Optionally, add code to update your table or UI here
                            $('#countryTable tbody').append(`
                <tr data-id="${response.country.id}">
                    <td>${$('#countryTable tbody tr').length + 1}</td>
                    <td class="text-center">${response.country.name}</td>
                    <td class="text-end">
                        <div class="dropdown custom__dropdown">
                            <button class="dropdown-toggle" type="button" id="dropdownMenuButton${response.country.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="las la-ellipsis-h"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton${response.country.id}">
                                <li><a class="dropdown-item" href="#" onclick="editCountry(${response.country.id})">Edit</a></li>
                                <li><a class="dropdown-item" href="#" onclick="deleteCountry(${response.country.id})">Delete</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            `);
                        },
                        error: function (xhr) {
                            const errorMessage = xhr.responseJSON && xhr.responseJSON.message
                                ? xhr.responseJSON.message
                                : 'An unexpected error occurred. Please try again.';

                            iziToast.error({
                                title: 'Error',
                                message: errorMessage
                            });
                        }
                    });
                });

            //     for get the edit data

            window.editCountry = function(id) {
                $.ajax({
                    url: `/ajax/countries/${id}`, // Endpoint to fetch country data (should be set up in routes and controller)
                    type: 'GET',
                    success: function (response) {
                        // Populate the modal fields with the response data
                        $('#editCountryId').val(response.country.id);

                        $('#editCountryName').val(response.country.name);

                        // Show the edit modal
                        $('#editCountryModal').modal('show');
                    },
                    error: function (xhr) {
                        const errorMessage = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'An unexpected error occurred. Please try again.';

                        iziToast.error({
                            title: 'Error',
                            message: errorMessage
                        });
                    }
                });
            };


                // Update country via AJAX when the Update button is clicked
                $('#updateCountryButton').on('click', function (e) {
                    e.preventDefault();

                    const id = $('#editCountryId').val();
                    const name = $('#editCountryName').val();

                    $.ajax({
                        url: `/ajax/countries/${id}`, // Endpoint to update country data (should be set up in routes and controller)
                        type: 'PUT',
                        data: {
                            name: name,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            iziToast.success({message: response.message});
                            // Update the row in the table without refreshing
                            $('#editCountryModal').modal('hide');
                            $(`#countryTable tbody tr[data-id="${id}"] td:nth-child(2)`).text(name);

                            // Close the modal

                        },
                        error: function (xhr) {
                            const errorMessage = xhr.responseJSON && xhr.responseJSON.message
                                ? xhr.responseJSON.message
                                : 'An unexpected error occurred. Please try again.';

                            iziToast.error({
                                title: 'Error',
                                message: errorMessage
                            });
                        }
                    });
                });

            //     for delete
            window.deleteCountry = function(id) {
                if (!confirm("Are you sure you want to delete this country?")) {
                    return;
                }

                $.ajax({
                    url: `/ajax/countries/${id}`, // Endpoint to delete country (should be set up in routes and controller)
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Remove the row from the table
                        $(`#countryTable tbody tr[data-id="${id}"]`).remove();
                        iziToast.success({message: response.message});
                    },
                    error: function (xhr) {
                        const errorMessage = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'An unexpected error occurred. Please try again.';

                        iziToast.error({
                            title: 'Error',
                            message: errorMessage
                        });
                    }
                });
            };


        });
    </script>
@endpush
