@extends('layouts.app')

@section('content')
    <style>
        #logTable {
            border-collapse: collapse;
            width: 100%;
        }

        #logTable th,
        #logTable td {
            border-right: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        #logTable th {
            background-color: #f2f2f2;
        }
    </style>


    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Log File Viewer') }}</div>

                <div class="card-body">

                    <form class="row" id="logForm">

                        <div class="col-10">
                            <input type="text" class="form-control" name="path" id="path" placeholder="/path/to/file">
                        </div>
                        <div class="col-2">
                            <button type="submit" class="btn btn-primary mb-3" style="padding: 5px 42px;">view</button>
                        </div>
                    </form>

                    <div class="row mt-3">
                        <div class="col">
                            <div id="divContent"></div>
                            <table id="logTable" class="table table-borderless border border-secondary">
                                <tbody id="logContent">

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination Controls -->
                    <div class="text-center" id="paginationControls">

                            <a class="btn btn-light border border-secondary pagination-link" style="padding: 5px 50px;" href="#" id="firstPage"> |< </a>
                            <a class="btn btn-light border border-secondary pagination-link" style="padding: 5px 50px;"  href="#" id="prevPage"> < </a>
                            <a class="btn btn-light border border-secondary pagination-link" style="padding: 5px 50px;"  href="#" id="nextPage"> > </a>
                            <a class="btn btn-light border border-secondary pagination-link" style="padding: 5px 50px;"  href="#" id="lastPage"> >| </a>

                    </div>
                    <div id="currentPage">Current page: 1</div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            var linesPerPage = 10;

            $('#logForm').submit(function (event) {
                event.preventDefault();
                var path = $('#path').val();
                var operation = determineOperationFromId($(this).attr('id'));
                var currentPage = parseInt($('#currentPage').text().trim());

                sendAjaxRequest(path, operation,currentPage);
            });

            $('.pagination-link').click(function (event) {
                event.preventDefault();
                var path = $('#path').val();
                var operation = determineOperationFromId($(this).attr('id'));
                var currentPage = parseInt($('#currentPage').text().trim());

                sendAjaxRequest(path, operation,currentPage);
            });

            function generateLogRow(lineNumber, content) {
                return `
            <tr>
                <td class="text-center bg-light" style="padding: 10px 30px;font-weight: bolder;">${lineNumber}</td>
                <td>${content}</td>
            </tr>
        `;
            }

            function populateLogTable(content) {
                $('#logTable #logContent').html(content);
            }

            function updatePaginationControls(data) {
                $('#currentPage').val(data.currentPage);
                $('#totalPages').text(data.totalPages);

                if (data.totalPages > 1) {
                    $('#paginationControls').show();
                } else {
                    $('#paginationControls').hide();
                }
            }
            function determineOperationFromId(id) {
                switch (id) {
                    case 'firstPage':
                        return 'first';
                    case 'prevPage':
                        return 'prev';
                    case 'nextPage':
                        return 'next';
                    case 'lastPage':
                        return 'last';
                    default:
                        return 'first';
                }
            }

            function sendAjaxRequest(path, operation,currentPage) {


                $.ajax({
                    url: "{{ route('load-file') }}",
                    method: "GET",
                    data: { path: path, operation: operation  ,page: currentPage},
                    success: function (data) {
                        var lines = data.data.content;
                        var logContent = '';
                        var linesCount = 0;

                        for (var i = 0; i < lines.length && linesCount < linesPerPage; i++) {
                            var line = lines[i].trim();
                            if (line !== '') {
                                logContent += generateLogRow(linesCount + 1, line);
                                linesCount++;
                            }
                        }
                        populateLogTable(logContent);
                        updatePaginationControls(data.data);
                        $('#currentPage').text(data.data.currentPage);
                        $('#divContent').hide();
                        $('#logContent').show();
                    },
                    error:function (xhr, status, error){
                        $('#divContent').show().html(`<p class="text-center">${xhr.responseJSON.message}</p>`);
                        $('#logContent').hide();
                        return;

                    }
                });
            }

        });

    </script>

@endsection
