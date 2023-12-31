<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Devsbook | Cadastro</title>
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1"/>
    <link rel="stylesheet" href="<?=$base?>/assets/css/login.css" />
</head>
<body>
<header>
    <div class="container">
        <a href=""><img src="<?=$base?>/assets/images/devsbook_logo.png" /></a>
    </div>
</header>
<section class="container main">
    <form method="POST" action="<?=$base?>/cadastro">
        <?php if ($flash):?>
            <div class="flash"><?=$flash?></div>
        <?php endif; ?>
        <input placeholder="Digite o seu nome completo*" class="input" type="text" name="name" />

        <input placeholder="Digite seu e-mail*" class="input" type="email" name="email" />

        <input placeholder="Digite uma senha*" class="input" type="password" name="password" />

        <input placeholder="Informe a sua data de nascimento*" class="input" type="text" name="birthdate" id="birthdate" />

        <input class="button" type="submit" value="Fazer o cadastro" />

        <a href="<?=$base?>/login">Já tem uma conta? Faça o login!</a>
    </form>
</section>
<script src="https://unpkg.com/imask"></script>
<script>
    IMask(
        document.getElementById('birthdate'),
        {
            mask: '00/00/0000'
        }
    )
</script>
</body>
</html>