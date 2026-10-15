<?php

namespace App\Observers;

use App\Models\CmsMedia;
use App\Models\ContentEntry;
use App\Models\SiteSetting;
use App\Support\CmsPublicImages;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class CmsPublicImagesObserver implements ShouldHandleEventsAfterCommit
{
    public function saved(CmsMedia|ContentEntry|SiteSetting $record): void
    {
        $publisher = app(CmsPublicImages::class);
        if ($record instanceof CmsMedia) {
            // Initial insertion precedes the source-file copy; MediaHasBeenAdded handles it later.
            if ($record->wasChanged()) {
                $publisher->syncTree($record);
            }
        } else {
            $publisher->syncOwner($record);
        }
    }

    public function deleted(CmsMedia|ContentEntry|SiteSetting $record): void
    {
        $publisher = app(CmsPublicImages::class);
        if ($record instanceof CmsMedia) {
            $publisher->remove($record);
        } else {
            // Soft-deleted content retains private files, but loses public copies.
            $publisher->syncOwner($record);
        }
    }
}
