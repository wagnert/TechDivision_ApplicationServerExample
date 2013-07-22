<!DOCTYPE html>
<html>

    <head>
        <title>phpinfo()</title>

    </head>

    <body>

        <div id="phpinfo"><?php

            ob_start();
            phpinfo();
            $phpinfo = ob_get_contents();
            ob_end_clean();

            echo nl2br($phpinfo);

        ?></div>
    </body>

</html>