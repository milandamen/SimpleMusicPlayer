<?php
/**
 * Created by PhpStorm.
 * User: Milan Damen
 * Date: 5-9-2015
 * Time: 17:08
 */

include 'views/partials/header.php';
?>

<div id="playerAndPlaylistContainer">
    <div id="playerContainer">
        <audio id="player" controls autoplay>
            Your browser does not support the audio tag.
        </audio>
        <span class="songName">No song playing.</span>
        <button onclick="playNext();">
            Play Next
        </button>
        <button onclick="playNext();">
            Play Previous TODO
        </button>
    </div>

    <div id="playlistContainer">
        <ul id="playlist">

        </ul>
    </div>
</div>
<div id="songListContainer">

<?php
$filelist = scandir_recursive(App::$config->music_dir);

outputFileList($filelist);

/**
 * Parse the file list and prepare and output it to the view.
 * @param array $array
 */
function outputFileList(array $array) {
    echo '<ul>';

    foreach ($array as $k => $v) {
        if (is_array($v)) {
            echo '<li>';
            echo "<a href='#player' onclick='addFolder(this);'><i></i>$k</a>";
            outputFileList($v);
            echo '</li>';
        } else {
            if ($relpath = toUnixPath(toRelativePath($v, App::$config->music_dir))) {
                $relpath = toSmpEntitiesPath(toUnixPath($relpath));
                $url = 'index.php?stream=' . $relpath;
                $basename = basename($v);

                echo '<li class="song">';
                echo "<a href='#player' onclick='add(this);' data-url='$url'><i></i>$basename</a>";
                echo '</li>';
            }
        }
        echo '</li>';
    }

    echo '</ul>';
}
?>

</div>

<?php
include 'views/partials/footer.php';
?>