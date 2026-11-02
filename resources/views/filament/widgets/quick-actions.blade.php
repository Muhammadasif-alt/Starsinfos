<x-filament-widgets::widget>
    <x-filament::section heading="What would you like to add?" description="Pick a section. Each form only asks for the details that section needs.">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:12px">
            @foreach ($actions as $action)
                <a href="{{ $action['url'] }}" style="display:flex;gap:12px;align-items:center;padding:14px 16px;border:1px solid rgb(220 230 225);border-radius:12px;background:#F7FBF9;text-decoration:none">
                    <x-filament::icon :icon="$action['icon']" style="width:28px;height:28px;color:#0B6B4F;flex:none" />
                    <span>
                        <strong style="display:block;color:#12251E">{{ $action['label'] }}</strong>
                        <span style="font-size:13px;color:#5B6B64">{{ $action['hint'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
