<x-filament-widgets::widget>
    <x-filament::section heading="Keep our community informed" description="Create updates, document our work and keep website content current.">
        <div class="cms-quick-actions">
            @foreach (['stories' => 'Write an update', 'projects' => 'Add a project', 'galleries' => 'Create a gallery', 'events' => 'Add an event'] as $collection => $label)
                <x-filament::button tag="a" :href="$this->resources()[$collection]::getUrl('create')">{{ $label }}</x-filament::button>
            @endforeach
        </div>
        <h3 class="cms-recent-heading">Recently updated</h3>
        <div class="cms-recent-list">
            @forelse ($this->entries() as $entry)
                <a href="{{ $this->resources()[$entry->collection]::getUrl('edit', ['record' => $entry]) }}" class="cms-recent-row">
                    <span><strong>{{ $entry->title }}</strong><span>{{ \App\Support\CmsSchema::COLLECTIONS[$entry->collection] }} · {{ ucfirst($entry->status) }}</span></span>
                    <span>{{ $entry->editor?->name ?? 'Content import' }}<span>{{ $entry->updated_at->diffForHumans() }}</span></span>
                </a>
            @empty
                <p>Import the existing website content to begin, or create your first record.</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
