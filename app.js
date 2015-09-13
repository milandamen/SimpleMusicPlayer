
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
        player.currentPlayIndex = index;
        $('#playerContainer .songName').text(playlist[index].name);
    }
}

function playNext() {
    play(player.currentPlayIndex + 1);
}

/**************************************/
/******** Playlist Visualising ********/
/**************************************/

function add(element) {
    var song = {
        url: element.dataset.url,
        name: element.textContent,
        play: function() {
            player.src = this.url;
        }
    };
    playlist.push(song);
    playIfFirstSong();

    $('#playlist').append(
        '<li>' +
            '<a href="#player" onclick="playSongFromPlaylist(this);" data-index="' + (playlist.length - 1) + '">' +
                song.name +
            '</a>' +
        '</li>'
    );
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