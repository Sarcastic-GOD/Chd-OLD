<?php
$id = $_GET["id"] ?? die("Invalid ID.");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/plyr@3.6.2/dist/plyr.css" />
<script src="https://cdn.jsdelivr.net/npm/plyr@3.6.12/dist/plyr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hls.js@1.1.4/dist/hls.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<style>
html {
  font-family: Poppins;
  background: #000;
  margin: 0;
  padding: 0
}

    .plyr__video-wrapper::before {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
        content: '';
        height: 100px;
        width: 115px;
        background: url('') no-repeat;
        background-size: 75px auto, auto;
    }

    .plyr__video-wrapper::after {
        position: absolute;
        top: 15px;
        left: 15px;
        z-index: 10;
        content: '';
        height: 115px;
        width: 115px;
        background-size: 75px auto, auto;
    }

</style>

</head>
<body>

<video autoplay controls crossorigin poster="xxx" playsinline>
<source type="application/vnd.apple.mpegurl" src="index.php?id=<?php echo $id; ?>"></video>
</body>
<script>

document.addEventListener("DOMContentLoaded", () => {
    const e = document.querySelector("video"),
        n = e.getElementsByTagName("source")[0].src,
        o = {};
    if (Hls.isSupported()) {
        var config = {
            maxMaxBufferLength: 100,
        };
        const t = new Hls(config);
        t.loadSource(n), t.on(Hls.Events.MANIFEST_PARSED, function(n, l) {
            const s = t.levels.map(e => e.height);
            o.quality = {
                default: s[0],
                options: s,
                forced: !0,
                onChange: e => (function(e) {
                    window.hls.levels.forEach((n, o) => {
                        n.height === e && (window.hls.currentLevel = o)
                    })
                })(e)
            };
            new Plyr(e, o)
        }), t.attachMedia(e), window.hls = t
    } else {
        new Plyr(e, o)
    }
});

</script>
</body>
</html>
