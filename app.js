
player = document.getElementById('player');
player.currentPlayIndex = -1;
player.onended = playNext;

playlist = [];

/*************************************/
/********** Player Controls **********/
/*************************************/

function play(index) {
    if (index < playlist.length && index > -1) {
        playlist[index].play();
        setPlaying(player.currentPlayIndex, false);
        setPlaying(index, true);
        player.currentPlayIndex = index;
        $('#playerContainer .songName').text(playlist[index].name);
    }
}

function playNext() {
    play(player.currentPlayIndex + 1);
}

function playPrevious() {
    play(player.currentPlayIndex - 1);
}

/**************************************/
/******** Playlist Visualising ********/
/**************************************/

function add(element) {
    var scope = getScope(element);
    if (scope !== '') {
        scope = scope.substring(0, scope.length - 1);
    } else {
        scope = '/';
    }

    var song = {
        url: element.dataset.url,
        name: element.textContent,
        scope: scope,
        play: function() {
            player.src = this.url;
        }
    };

    if (playlist.length === 0 || playlist[playlist.length-1].scope !== scope) {
        $('#playlist').append(
            '<li class="d">' +
                scope +
            '</li>'
        );
    }

    playlist.push(song);

    $('#playlist').append(
        '<li class="s">' +
            '<a href="#player" onclick="playSongFromPlaylist(this);" data-index="' + (playlist.length - 1) + '">' +
                song.name +
            '</a>' +
        '</li>'
    );

    playIfFirstSong();
}

function addFolder(element) {
    var liFolder = element.parentNode;
    var songs = $('li.song a', liFolder);
    songs.each(function() {
        add(this);
    });
}

function playIfFirstSong() {
    if (playlist.length == 1) {
        play(0);
    }
}

function playSongFromPlaylist(element) {
    play(+element.dataset.index);
}

function setPlaying(index, bool) {
    var element = $('#playlist li.s:eq(' + index + ')');
    if (!element) {return;}

    if (bool) {
        element.addClass('playing');
    } else {
        element.removeClass('playing');
    }
}

/*************************************/
/************* Song Data *************/
/*************************************/

function getScope(element) {
    var dir = element.parentNode.parentNode.parentNode;

    if (dir && dir.tagName === 'LI') {
        var a = dir.childNodes[0];
        return getScope(a) + a.textContent + '/';
    }

    return '';
}