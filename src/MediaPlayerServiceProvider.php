<?php

namespace OpenAdmin\Admin\MediaPlayer;

use OpenAdmin\Admin\Admin;
use OpenAdmin\Admin\Grid\Column;
use OpenAdmin\Admin\Show\Field;
use Illuminate\Support\ServiceProvider;

class MediaPlayerServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(MediaPlayer $extension)
    {
        if (! MediaPlayer::boot()) {
            return ;
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/open-admin-ext/media-player')],
                'open-admin-media-player'
            );
        }

        Admin::booting(function () {

            Admin::js('vendor/open-admin-ext/media-player/build/mediaelement-and-player.min.js');
            Admin::css('vendor/open-admin-ext/media-player/build/mediaelementplayer.min.css');

            Field::macro('video', PlayerField::video());
            Field::macro('audio', PlayerField::audio());

            Column::extend('video', PlayerColumn::video());
            Column::extend('audio', PlayerColumn::audio());
        });
    }
}
