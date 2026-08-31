@if(session('success'))
    <div class="notice success" role="status"><strong>{{ session('success') }}</strong></div>
@endif
@if($errors->any())
    <div class="notice error" role="alert" tabindex="-1" id="error-summary">
        <strong>Periksa kembali isian Anda.</strong>
        <ul>
            @foreach($errors->messages() as $field => $messages)
                @foreach($messages as $message)
                    <li><a href="#{{ $field }}">{{ $message }}</a></li>
                @endforeach
            @endforeach
        </ul>
    </div>
@endif

