<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500">
            <span class="text-white">{{ __('Profile') }}</span>
        </div>
    </x-slot>

    <div class="max-w-3xl space-y-6">
        <div class="p-6 sm:p-8 bg-gray-800/40 border border-gray-700/40 rounded-2xl">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-gray-800/40 border border-gray-700/40 rounded-2xl">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-gray-800/40 border border-gray-700/40 rounded-2xl">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
