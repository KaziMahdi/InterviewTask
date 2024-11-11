@extends('panel.layout')

@section('content')
    <div class="dashboard__inner__item dashboard__card bg__white padding-20 radius-10">

        <h4 class="dashboard__inner__item__header__title">{{$title}}</h4>
        <button type="button" class="btn btn-success float-end" data-bs-toggle="modal"
                data-bs-target="#addCity"><i data-feather="plus-circle"></i> Add City
        </button>
        <br>
        <!-- Table Design One -->
        <div class="tableStyle_one mt-4">
            <div class="table_wrapper">
                <!-- Table -->
                <table id="cityTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th class="text-center">Name</th>
                        <th class="text-center">State</th>
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
    <div class="modal fade" id="addCity" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add City</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="cityForm">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Name:</label>
                            <input type="text" class="form-control" id="recipient-name" name="name">
                        </div>
                        <div class="form-group">
                            <label for="recipient-city" class="col-form-label">State:</label>
                            <select type="text" class="form-select" id="recipient-city" name="state_id">
                                <option value="" disabled selected>--Select One--</option>
                                @foreach($states as $state)
                                    <option value="{{$state->id}}">{{$state->name}}</option>
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
    <div class="modal fade" id="editCityModal" tabindex="-1" aria-labelledby="editCityLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCityLabel">Edit City</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCityForm">
                        <input type="hidden" id="editCityId">
                        <div class="mb-3">
                            <label for="editCityName" class="col-form-label"> Name:</label>
                            <input type="text" class="form-control" id="editCityName" name="name">
                        </div>
                        <div class="form-group">
                            <label for="editStateSelect" class="col-form-label">State:</label>
                            <select class="form-control" id="editStateSelect" name="state_id">
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="updateCityButton">Update</button>
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
            function fetchCities() {
                $.ajax({
                    url: '{{route('ajax.fetch.cities')}}',
                    type: 'GET',
                    success: function (response) {
                        // Clear the table body
                        $('#cityTable tbody').empty();

                        // Populate the table with data
                        response.forEach(function (city, index) {
                            $('#cityTable tbody').append(`
                        <tr data-id="${city.id}">
                            <td>${index + 1}</td>
                            <td class="text-center">${city.name}</td>
                            <td class="text-center">${city.state ? city.state.name : 'N/A'}</td>
                            <td class="text-end">
                                <div class="dropdown custom__dropdown">
                                    <button class="dropdown-toggle" type="button" id="dropdownMenuButton${city.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="las la-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton${city.id}">
                                        <li><a class="dropdown-item" href="#" onclick="editCity(${city.id})">Edit</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="deleteCity(${city.id})">Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    `);
                        });
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText); // Log any error
                        iziToast.error({title: 'Error', message: response.message});

                    }
                });
            }

            // Call fetchCountries function on page load
            fetchCities();


            // Add country
            $('#saveButton').on('click', function (e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('ajax.store.cities') }}",
                    type: 'POST',
                    data: $('#cityForm').serialize(), // Serialize the form data correctly
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                    },
                    success: function (response) {
                        iziToast.success({message: response.message});
                        $('#cityForm')[0].reset(); // Clear form fields
                        $('#addCity').modal('hide'); // Hide the modal

                        // Optionally, add code to update your table or UI here
                        $('#cityTable tbody').append(`
                <tr data-id="${response.city.id}">
                    <td>${$('#cityTable tbody tr').length + 1}</td>
                    <td class="text-center">${response.city.name}</td>
                    <td class="text-center">${response.city.state ? response.city.state.name : 'N/A'}</td>
                    <td class="text-end">
                        <div class="dropdown custom__dropdown">
                            <button class="dropdown-toggle" type="button" id="dropdownMenuButton${response.city.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="las la-ellipsis-h"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton${response.city.id}">
                                <li><a class="dropdown-item" href="#" onclick="editCity(${response.city.id})">Edit</a></li>
                                <li><a class="dropdown-item" href="#" onclick="deleteCity(${response.city.id})">Delete</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            `);
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText); // Log any error
                        iziToast.error({title: 'Error', message: response.message});

                    }
                });
            });

            //     for get the edit data

            window.editCity = function (id) {
                $.ajax({
                    url: `ajax/cities/${id}`, // Endpoint to fetch states data (should be set up in routes and controller)
                    type: 'GET',
                    success: function (response) {
                        // Populate the modal fields with the response data
                        $('#editCityId').val(response.city.id);

                        $('#editCityName').val(response.city.name);

                        let stateOptions = '<option value="">-- Select State --</option>';
                        response.states.forEach(function (state) {
                            stateOptions += `<option value="${state.id}" ${
                                state.id === response.city.state_id ? 'selected' : ''
                            }>${state.name}</option>`;
                        });

                        $('#editStateSelect').html(stateOptions);

                        // Show the edit modal
                        $('#editCityModal').modal('show');
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
            $('#updateCityButton').on('click', function (e) {
                e.preventDefault();

                const id = $('#editCityId').val();
                const name = $('#editCityName').val();
                const state_id = $('#editStateSelect').val();

                $.ajax({
                    url: `ajax/cities/${id}`, // Endpoint to update country data (should be set up in routes and controller)
                    type: 'PUT',
                    data: {
                        name: name,
                        state_id: state_id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        iziToast.success({message: response.message});
                        // Update the row in the table without refreshing
                        $('#editCityModal').modal('hide');

                        $(`#cityTable tbody tr[data-id="${id}"] td:nth-child(2)`).text(response.city.name);
                        $(`#cityTable tbody tr[data-id="${id}"] td:nth-child(3)`).text(response.state_name);

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
            let deleteCityId = null; // Variable to hold the ID of the country to delete

            // Function to show confirmation modal
            window.deleteCity = function (id) {
                deleteCityId = id; // Store the ID in the variable
                $('#confirmDeleteModal').modal('show'); // Show the confirmation modal
            };

            // Event listener for the confirmation button
            $('#confirmDelete').on('click', function () {
                $.ajax({
                    url: `ajax/cities/${deleteCityId}`, // Endpoint to delete country
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                    },
                    success: function (response) {
                        // Remove the row from the table
                        $(`#cityTable tbody tr[data-id="${deleteCityId}"]`).remove();
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
