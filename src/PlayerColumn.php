<?php

namespace OpenAdmin\Admin\MediaPlayer;

use OpenAdmin\Admin\Admin;
use Illuminate\Support\Arr;

class PlayerColumn
{
    public function setupScript($options = [])
    {
        $options = array_merge([
            'pluginPath'       => '/vendor/open-admin-ext/media-player/build',
            'shimScriptAccess' => 'always',
            'videoWidth'       => 1280,
            'videoHeight'      => 768,
        ], $options);

        $options['style'] = 'padding-left:auto;padding-right:auto;';

        $config = json_encode($options);

        $locale = config('app.locale');

        $script = <<<SCRIPT

mejs.i18n.language('$locale');

var config = $config;
config.success = function (player, node) {
    $(player).closest('.mejs__container').attr('lang', mejs.i18n.language());
};

$('video, audio').mediaelementplayer(config);

$('.modal').on('hidden.bs.modal', function () {

    var playerId = $(this).find('.mejs__container').attr('id');
    var player = mejs.players[playerId];
    if (!player.paused) {
        player.pause();
    }
});

$('.mejs__container').css({'margin-left':'auto', 'margin-right':'auto'});
SCRIPT;

        Admin::script($script);
    }

    public static function video()
    {
        $macro = new static();

        return function ($value, $options = []) use ($macro) {

            $macro->setupScript($options);

            $url = MediaPlayer::getValidUrl($value,  Arr::get($options, 'server'));

            $width = Arr::get($options, 'videoWidth');
            $width_total = $width + 32;
            $height = Arr::get($options, 'videoHeight');
            $poster = Arr::get($options, 'poster');

            return <<<HTML
<a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#video-modal-{$this->getKey()}">
    <i class="icon-play"></i><span class="hidden-xs"> Play</span>
</a>
<div class="modal" id="video-modal-{$this->getKey()}" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="width:{$width_total}px;">
      <div class="modal-header">
        <h5 class="modal-title">Play</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <video src="$url" width="{$width}" height="{$height}" poster="{$poster}"></video>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
HTML;
        };
    }

    public static function audio()
    {
        $macro = new static();

        return function ($value, $options = []) use ($macro) {

            $macro->setupScript($options);

            $url = MediaPlayer::getValidUrl($value, Arr::get($options, 'server'));

            $width = Arr::get($options, 'audioWidth');
            $height = Arr::get($options, 'audioHeight');

            return <<<HTML
<audio src="$url" width="{$width}" height="{$height}"></audio>
HTML;
        };
    }
}
