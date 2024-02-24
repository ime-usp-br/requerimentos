<div class="overlay-container">
    <div class="overlay-content">

        <div class="overlay-header">
            <div class="overlay-title">
                {{ $title }}
            </div>
            <img class="close-button" src="{{ asset('img/overlay/close-button.svg') }}" alt="Close button" >
        </div>

        {{ $slot }}

    </div>
</div>