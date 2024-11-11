@extends('panel.layout')

@section('content')
    <div class="dashboard__inner__item dashboard__card bg__white padding-20 radius-10">

        <h4 class="dashboard__inner__item__header__title">{{$title}}</h4>
        <button type="button" class="btn btn-success float-end" data-bs-toggle="modal"
                data-bs-target="#addState"><i data-feather="plus-circle"></i> Add State
        </button>
        <br>
        <!-- Table Design One -->
        <div class="tableStyle_one mt-4">
            <div class="table_wrapper">
                <!-- Table -->
                <table id="stateTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th class="text-center">Name</th>
                        <th class="text-center">Country</th>
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
    <div class="modal fade" id="addState" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add State</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="stateForm">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Name:</label>
                            <input type="text" class="form-control" id="recipient-name" name="name">
                        </div>
                        <div class="form-group">
                            <label for="recipient-country" class="col-form-label">Country:</label>
                            <select type="text" class="form-select" id="recipient-country" name="country_id">
                                <option value="" disabled selected>--Select One--</option>
                                @foreach($countries as $country)
                                    <option value="{{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
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
    <div class="modal fade" id="editStateModal" tabindex="-1" aria-labelledby="editStateLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStateLabel">Edit State</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCountryForm">
                        <input type="hidden" id="editStateId">
                        <div class="mb-3">
                            <label for="editStateName" class="col-form-label"> Name:</label>
                            <input type="text" class="form-control" id="editStateName" name="name">
                        </div>
                        <div class="form-group">
                            <label for="editCountrySelect" class="col-form-label">Country:</label>
                            <select class="form-control" id="editCountrySelect" name="country_id">
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="updateStateButton">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
         aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this country?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('script')
    <script>
        $(document).ready(function () {
            // Function to fetch countries and populate the table
            function fetchStates() {
                $.ajax({
                    url: '{{route('ajax.fetch.states')}}',
                    type: 'GET',
                    success: function (response) {
                        console.log(response);
                        // Clear the table body
                        $('#stateTable tbody').empty();

                        // Populate the table with data
                        response.forEach(function (state, index) {
                            $('#stateTable tbody').append(`
                        <tr data-id="${state.id}">
                            <td>${index + 1}</td>
                            <td class="text-center">${state.state_name}</td>
                            <td class="text-center">${state.country_name || 'N/A'}</td>
                            <td class="text-end">
                                <div class="dropdown custom__dropdown">
                                    <button class="dropdown-toggle" type="button" id="dropdownMenuButton${state.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="las la-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton${state.id}">
                                        <li><a class="dropdown-item" href="#" onclick="editState(${state.id})">Edit</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="deleteState(${state.id})">Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    `);
                        });
                    },
                    error: function () {
                        alert("Failed to load country data.");
                    }
                });
            }

            // Call fetchCountries function on page load
            fetchStates();


            // Add country
            $('#saveButton').on('click', function (e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('ajax.store.states') }}",
                    type: 'POST',
                    data: $('#stateForm').serialize(), // Serialize the form data correctly
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                    },
                    success: function (response) {
                        iziToast.success({message: response.message});
                        $('#stateForm')[0].reset(); // Clear form fields
                        $('#addState').modal('hide'); // Hide the modal

                        // Optionally, add code to update your table or UI here
                        $('#stateTable tbody').append(`
                <tr data-id="${response.state.id}">
                    <td>${$('#stateTable tbody tr').length + 1}</td>
                    <td class="text-center">${response.state.name}</td>
                    <td class="text-center">${response.state.country_name || 'N/A'}</td>
                    <td class="text-end">
                        <div class="dropdown custom__dropdown">
                            <button class="dropdown-toggle" type="button" id="dropdownMenuButton${response.state.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="las la-ellipsis-h"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton${response.state.id}">
                                <li><a class="dropdown-item" href="#" onclick="editCountry(${response.state.id})">Edit</a></li>
                                <li><a class="dropdown-item" href="#" onclick="deleteCountry(${response.state.id})">Delete</a></li>
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

            window.editState = function (id) {
                $.ajax({
                    url: `ajax/states/${id}`, // Endpoint to fetch states data (should be set up in routes and controller)
                    type: 'GET',
                    success: function (response) {
                        // Populate the modal fields with the response data
                        $('#editStateId').val(response.state.id);

                        $('#editStateName').val(response.state.name);

                        let countryOptions = '<option value="">-- Select Country --</option>';
                        response.countries.forEach(function (country) {
                            countryOptions += `<option value="${country.id}" ${
                                country.id === response.state.country_id ? 'selected' : ''
                            }>${country.name}</option>`;
                        });

                        $('#editCountrySelect').html(countryOptions);

                        // Show the edit modal
                        $('#editStateModal').modal('show');
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


            // Update state via AJAX when the Update button is clicked
            $('#updateStateButton').on('click', function (e) {
                e.preventDefault();

                const id = $('#editStateId').val();
                const name = $('#editStateName').val();

                const country_id = $('#editCountrySelect').val();

                $.ajax({
                    url: `ajax/states/${id}`, // Endpoint to update country data (should be set up in routes and controller)
                    type: 'PUT',
                    data: {
                        name: name,
                        country_id: country_id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        iziToast.success({message: response.message});
                        // Update the row in the table without refreshing
                        $('#editStateModal').modal('hide');

                        $(`#stateTable tbody tr[data-id="${id}"] td:nth-child(2)`).text(response.state.name);
                        $(`#stateTable tbody tr[data-id="${id}"] td:nth-child(3)`).text(response.state.country_name);

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
            let deleteCountryId = null; // Variable to hold the ID of the country to delete

            // Function to show confirmation modal
            window.deleteState = function (id) {
                deleteCountryId = id; // Store the ID in the variable
                $('#confirmDeleteModal').modal('show'); // Show the confirmation modal
            };

            // Event listener for the confirmation button
            $('#confirmDelete').on('click', function () {
                $.ajax({
                    url: `ajax/states/${deleteCountryId}`, // Endpoint to delete country
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                    },
                    success: function (response) {
                        // Remove the row from the table
                        $(`#stateTable tbody tr[data-id="${deleteCountryId}"]`).remove();
                        iziToast.success({message: response.message});
                        $('#confirmDeleteModal').modal('hide'); // Hide the modal after successful deletion
                    },
                    error: function (xhr) {
                        const errorMessage = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'An unexpected error occurred. Please try again.';

                        iziToast.error({
                            title: 'Error',
                            message: errorMessage
                        });
                        $('#confirmDeleteModal').modal('hide'); // Hide the modal even on error if needed
                    }
                });
            });


        });
    </script>
@endpush
