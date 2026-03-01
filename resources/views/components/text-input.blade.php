@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-surface border border-border text-text-primary text-sm rounded-xl focus:ring-primary focus:border-primary block w-full p-3 outline-none transition']) }}>
