@extends('basic')

@section('form')

    <div class="container">

        @if(!$checked)
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">XLSX FIXER</div>

                        <div class="card-body">
                            <form action="{{ route('check', [], true) }}"  method="post" enctype="multipart/form-data">
                                @csrf
                                {{-- FILE--}}
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label for="age">Choose XLSX File</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="file" class="form-control-file" id="file" name="file"  value="" required>
                                            <br>
                                            <p>Max file size shouldn't be more then 2000 kB</p>
                                            <p>File extension should be '.xlsx'</p>
                                        </div>
                                    </div>
                                </div>
                                {{-- BUTTON --}}
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4"></div>
                                        <div class="col-sm-offset-0 col-sm-8">
                                            <input type="submit" class="btn btn-primary" value="CHECK FILE">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($title))

            <div id="button" class="text-center">
                <a href="{{ route('main') }}" class="btn btn-primary">Start Page</a>
            </div>
            <br>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">CHECKED RESULT</div>
                        <div class="card-body">

                            @if($shifted)
                                <p class="r-font">The File  "{{ $file_name }}" has {{ $shifted }} shifted rows</p>
                                <p class="r-font">If you want to fix shifted rows of the "{{ $file_name }}" choose this file</p>
                            @else
                                <p class="g-font">This file doesn't have shifted rows</p>
                            @endif
                            <p class="b-font">If you want to check & remove equial rows choose column with unique values</p>
                            <form action="{{ route('execute', [], true) }}"  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="shifted" value="{{ $shifted }}">
                                @csrf
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label for="">Choose column</label>
                                        </div>
                                        <div class="col-sm-8">

                                            <select name="unique" id="">
                                                @php $i=0 @endphp
                                                @foreach($title as $el)
                                                    <option value="{{$i++}}" >{{ $el }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                </div>
                                {{-- FILE--}}
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label>Choose the same File</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="file" class="form-control-file" id="file" name="file"  value="" required>
                                        </div>
                                    </div>
                                </div>
                                {{-- BUTTON --}}
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-3"></div>
                                        <div class="col-sm-offset-0 col-sm-8">
                                            <input type="submit" class="btn btn-primary" value="GET FIXED FILE">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        @endif


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
                            <h4>Were fixed {{ $shift_count }} shifted rows</h4>
                            <br>
                            <h4>Were removed {{ $non_unique }} no unique rows</h4>
                            <br>
                            <a href="{{ $output_file }}" class="btn btn-outline-success">Download fixed File</a>
                        </div>
                    </div>
                </div>
            </div>

        @endif
    </div>

@endsection


