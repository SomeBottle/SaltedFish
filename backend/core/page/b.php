<h1 class='btitle' id='bt'></h1>
<script>
    document.getElementById('bt').innerHTML = username + ' VS. ' + enemyname;
    setTimeout(function() {
        t('s', 't');
        getroom();
    }, 1500);
    setaudio(soundResourceBase+'battle.mp3');
</script>