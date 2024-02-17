<div class="overlay-container">
    <div class="overlay-content">
        <div class="overlay-header">
            <div class="overlay-title">
                @if (session()->has('success'))
                    {{ session('success')['title message'] }}
                @elseif($errors->any())
                    Erros no prenchimento dos dados
                @endif
            </div>
            <img class="close-button" src="{{ asset('img/newRequisition/close-button.svg') }}" alt="Close button" >
        </div>
        @if (session()->has('success'))
            <style>
                .overlay-container {
                    display: block;
                }
            </style>
            <p>{{ session('success')['body message'] }}</p>
            <div class="overlay-nav">
                {{ $slot }}
            </div>
        @elseif($errors->any())
            <style>
                .overlay-container {
                    display: block;
                }
            </style>
            <p class="overlay-error-message">Os erros podem ter sido causados por campos obrigatórios não preenchidos ou por inconsistência nos dados inseridos.</p>
            
        @endif
    </div>
</div>