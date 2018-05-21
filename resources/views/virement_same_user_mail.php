<html>
    <body>
        <h2>Bonjour <?php echo $email_receiver; ?></h2>
        <p>Vous avez fait un virement de votre compte <?php echo $account_sender; ?>
        vers votre compte<?php echo $account_receiver; ?></p>
        <br>
        <p>Le montant de virement:<?php echo $amount; ?><?php echo $account_currency; ?></p>
        <br>
        <p>Tharwa Bank vous souhaite une bonne journée.</p>
    </body>
</html>