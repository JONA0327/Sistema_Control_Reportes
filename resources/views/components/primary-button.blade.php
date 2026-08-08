<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-login-gradient inline-flex items-center px-5 py-2.5 rounded-xl font-semibold text-sm text-white shadow-lg shadow-red-950/40 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-900 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
