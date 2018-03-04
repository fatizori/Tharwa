<html>
    <body>
        <h2>Bonjour <?php echo $email; ?></h2>
        <p>Veillez entrer ce code <b><?php echo $code ?></b> pour que vous puissiez authentifier chez Tharwa</p>
        <br>
        <p>Ce code est valable pour une heure depuis <b><?php echo \Carbon\Carbon::now() ?></b></p>
        <br>
        <p>Tharwa Bank vous souheite une bonne journée.</p>
    </body>
</html>