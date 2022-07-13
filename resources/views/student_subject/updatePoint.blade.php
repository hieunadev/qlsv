@extends('layouts.main')
@include('layouts.flash_message')
@section('content')
    <style>
        option:disabled {
            display: none;
        }
    </style>
    <main id="main" class="main">
        <div class="card shadow m-3 p-3">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button class="btn btn-success add-select" type="button">Add</button>
                <a class="btn btn-primary" href="{{ route('student.slug', $student->slug) }}">Back</a>
            </div>
            @yield('error')
            <h3>Student: {{ $student->name }} - ID: {{ $student->id }}</h3>
            <form action="{{ route('savePoint', $student->id) }}" method="POST" enctype="multipart/form-data"
                id="formSubmit">
                @csrf
                <div class="table table-sm">
                    <div class="thead">
                        <div class="row ms-2">
                            <div class="form-group col-4">Subject</div>
                            <div class="form-group col-4">Point</div>
                            <div class="form-group col-4">Delete</div>
                        </div>
                    </div>
                    <div class="addMore">
                        <div class="m-2">
                            @foreach ($donesubjects as $key => $donesubject)
                                <div class="row">
                                    <div class="form-group col-4 mb-2">
                                        <select name="subject_id[]" id="subject_id" class="form-select subject_id">
                                            <option>Select Subject</option>
                                            <option value="{{ $donesubject->id }}" {{ $donesubject->id ? 'selected' : '' }}>{{ $donesubject->name }}</option>
                                            @foreach ($subjects as $subject)
                                                <option value="{{ $subject->id }}" >{{ $subject->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-4 mb-2">
                                        <input type="text" name="point[]" id="point" class="form-control point" value="{{ old('point.' . $key) }}" autofocus>
                                    </div>
                                    <div class="form-group col-2 mb-2">
                                        <button type="button" class="btn btn-danger delete"><i class="fa-regular fa-trash-can"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary" id="submitbtn">Save</button>
                </div>
                <p class="text-danger d-none m-2 fst-italic"><i class="fa-solid fa-circle-xmark text-danger m-1"></i>Bạn đã add hết môn rồi!</p>
            </form>
        </div>
    </main>
    <select name="subject_id[]" id="clone" class="form-select subject_id d-none">
        @foreach ($subjects as $key => $subject)
            <option value="{{ $subject->id }}">
                {{ $subject->name }}
            </option>
        @endforeach
    </select>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            updateSelects()
            $(".addMore").on("change", "select", function() {
                updateSelects()
            })

            function updateSelects() {
                let selected = [];
                $("div.addMore").find("option").prop("disabled", false);
                $("div.addMore select").each(function(index, select) {
                    if (select.value !== "") {
                        selected.push(select.value);
                    }
                })
                for (let index in selected) {
                    $('option[value="' + selected[index] + '"]:not(:selected)')
                        .prop("disabled", true)
                }
            }

            $(document).on("click", ".add-select", function() {
                let tag = $('#clone').html();
                let addUpdate =
                    `<div class="row">` +
                    `<div class="form-group col-4 mb-2">` +
                    `<select name="subject_id[]" id="subject_id" class="form-select subject_id count"><option>Select Subject</option>` +
                    tag +
                    `</select>` +
                    `</div>` +
                    `<div class="form-group col-4 mb-2">` +
                    `<input type="text" name="point[]" id="point" class="form-control point">` +
                    `</div>` +
                    `<div class="form-group col-2 mb-2">` +
                    `<button type="button" class="btn btn-danger delete"><i class="fa-regular fa-trash-can"></i></button>` +
                    `</div>` +
                    `</div>`;
                $('.addMore > div').append(addUpdate);

                if ($('select#clone option').length == $('.addMore div.row').length) {
                    $("button.add-select").addClass('d-none');
                    $("main#main p").removeClass('d-none').addClass('d-inline');
                }
            });

            $('.addMore').delegate('.delete', 'click', function() {
                $(this).parent().parent().remove();
                $("button.add-select").removeClass('d-none');
                $("main#main p").addClass('d-none');
                updateSelects()
            });
        });
    </script>
@endsection
