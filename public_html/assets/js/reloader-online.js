$(document).ready (function () {
    var updater = setInterval (function () {
        $('div#server-online').load ('OnlineStatus.php', 'update=true');
    }, 1000);

    var updaterr = setInterval (function () {
        $('div#server-ping').load ('PingStatus.php', 'update=true');
    }, 1000);
});