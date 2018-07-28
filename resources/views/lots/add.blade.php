@extends('layouts.app')

@section('content')
<div class="container">
        @isset($success)
            <div class="msg" style="color:green;font-weight:bold">
                Lot has been added successfully!
            </div>
        @endisset

        @isset($error)
            <div class="msg" style="color:red;font-weight:bold">
                Sorry, error has been occurred: {{$errorMsg}}
            </div>
        @endisset
    <form action="" method="POST">
        @csrf
        <div>
            <label>
                Currency ID
                <input type="text" name="currency_id">
            </label>
        </div>
        <div>
            <label>
                Timestamp of lot open time
                <input type="text" name="date_time_open">
            </label>
        </div>
        <div>
            <label>
                Timestamp of lot close time
                <input type="text" name="date_time_close">
            </label>
        </div>
        <div>
            <label>
                Price
                <input type="text" name="price">
            </label>
        </div>
        <button type="submit" name="add">Add</button>
    </form>
</div>
@endsection
