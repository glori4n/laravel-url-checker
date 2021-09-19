
@extends('layouts.app')
@section('content')
{{-- Add --}}
<div class="container-box">
    <h2>Add a New URL</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @elseif(Session::has('message'))
        <div class="alert alert-success">
            {{ Session::get('message') }}
            {{ Session::forget('message') }}
        </div>
    @endif

    <div class="card-body">
            <form action="{{ route('url-create') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>URI</label><br>
                    <input placeholder="https://example.com" type="text" name="uri" id="uri" required>
                </div>
                <div class="form-group">
                    <button class="mt-2 btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
    </div>
</div>

{{-- Read --}}
<div class="bg-gray-50">
    <div class="overflow-auto max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-6 bg-white-50 border-b border-gray-200 mt-10">
            <h1>URLs Registered</h1>
            @if($urls->count())
            <table id="myTable" class="table table-striped table-bordered table-sm" max-width="100%">
                <thead>
                    <tr>
                        <th class="th-sm uri-column">URI</th>
                        <th class="status-column" style="white-space: nowrap;text-align:center">Status Code</th>
                        <th class="th-sm response-column">Response (Body)</th>
                        <th class="th-sm actions-column"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($urls as $url)
                    <tr>
                        <td>{{ $url->uri }}</td>
                        <td style="text-align: center">{{ $url->status }}</td>
                        <td style="text-align: center">
                            @if ($url->status == 200)
                                <form action="{{ route('download', $url->response) }}" method="GET">
                                    @csrf
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to download this response?')">Download</button>
                                </form>
                            @else
                                {{ $url->response }}
                            @endif
                        </td>
                        <td style="text-align: center">
                            <div class="btn-group">
                                <form action="{{ route('url-delete', $url->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this url?')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            @else
                None
            @endif
        </div>
    </div>
</div>

{{-- Table Script --}}
<script type="text/javascript">
    $(document).ready( function () {
        $('#myTable').DataTable({
            "searching": false,
            order: [],
            "columnDefs": [{ 
                targets: ['response-column','actions-column', 'uri-column', 'status-column'], 
                orderable: false
            }]
        });
            setInterval( function () {
                $.ajax({
                type: 'GET',
                url: '/getAjaxData',
                success: function (data) {
                        if (data.ajax_response) {
                            $( "#myTable" ).load(window.location.href + " #myTable" );
                            data.ajax_response = false;
                        }
                    }
                });
            }, 15000 );
        });
</script>

@endsection