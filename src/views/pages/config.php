<?=$render('header', ['loggedUser' => $loggedUser]);?>
    <section class="container main">
        <?=$render('sidebar', ['activeMenu' => 'config']);?>
        <section class="feed mt-10">
            <?php if (!empty($flash)): ?>
                <div class="flash">
                    <p><?=$flash?></p>
                </div>
            <?php endif; ?>
            <h1>Configurações</h1>
            <form class="config-form" action="<?=$base?>/config" method="post" enctype="multipart/form-data">
                <label for="avatar">Novo avatar:<br>
                <input type="file" name="avatar" id="avatar"><br>
                    <img class="image-edit" src="">
                </label>
                <label for="cover">Nova capa:<br>
                <input type="file" name="cover" id="cover"><br>
                    <img class="image-edit" src="">
                </label>
                <hr>
                <label for="name">Nome Completo:<br>
                <input type="text" name="name" id="name" value="<?=$user->name?>"><br></label>
                <label for="birthdate">Data de nascimento:<br>
                <input type="text" name="birthdate" id="birthdate" value="<?=date('d/m/Y', strtotime($user->birthdate))?>"><br></label>
                <label for="email">E-mail:<br>
                <input type="email" name="email" id="email" value="<?=$user->email?>"><br></label>
                <label for="city">Cidade:<br>
                <input type="text" name="city" id="city" value="<?=$user->city?>"><br></label>
                <label for="work">Trabalho:<br>
                <input type="text" name="work" id="work" value="<?=$user->work?>"><br></label>
                <hr>
                <label for="pass1">Nova Senha:<br>
                <input type="password" name="pass1" id="pass1"><br></label>
                <label for="pass2">Confirmar Nova Senha:<br>
                <input type="password" name="pass2" id="pass2"><br></label>
                <button type="submit" class="button">Salvar</button>
            </form>
        </section>
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
<?=$render('footer');?>