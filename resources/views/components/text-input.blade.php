@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-gray-900/80 border border-gray-700 rounded-xl text-white placeholder-gray-600 px-3.5 py-2.5 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors disabled:opacity-50']) }}>
