@extends('basic')

@section('form')

    <div class="container">

        @if(isset($output_file))

            <div id="button" class="text-center">
                <a href="{{ route('main') }}" class="btn btn-primary">Start Page</a>
            </div>
            <br>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">RESULT</div>

                        <div class="card-body">
                            <p class="g-font">Were fixed {{ $shift_count }} shifted rows</p>
                            <br>
                            @if($non_unique)
                                <p class="g-font">Were removed {{ $non_unique }} no unique rows</p>
                            @endif
                            <br>
                            <a href="{{ $output_file }}" class="btn btn-outline-success">Download fixed File</a>
                        </div>
                    </div>
                </div>
            </div>

        @endif
    </div>

@endsection


