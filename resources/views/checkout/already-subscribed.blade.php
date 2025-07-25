<x-layouts.focus-center>

    <x-slot name="title">
        {{ __('Already Subscribed') }}
    </x-slot>

    <div class="mx-4">
        <div class="card max-w-3xl bg-base-100 shadow-xl mx-auto text-center">
            <div class="card-body">
                @svg('party', 'w-24 h-24 mx-auto text-primary-500 stroke-primary-500')
                <x-heading.h3 class="text-primary-900">
                    {{ __('You are already subscribed!') }}
                </x-heading.h3>
                <p>
                    {{ __('You are already subscribed to our service. You can manage your subscription from your account.') }}
                </p>

                <x-button-link.primary href="{{ route('filament.dashboard.pages.dashboard') }}" class="mt-4 mx-auto">
                    {{ __('Continue Your Journey') }}
                </x-button-link.primary>

            </div>
        </div>
    </div>

</x-layouts.focus-center>
