<?php //var_dump($feedItem->comments); die(); ?>
<div class="box feed-item" data-id="<?=$feedItem->id?>">
    <div class="box-body">
        <div class="feed-item-head row mt-20 m-width-20">
            <div class="feed-item-head-photo">
                <a href="<?=$base?>/perfil/<?=$feedItem->user->id?>"><img src="<?=$base?>/media/avatars/<?=$feedItem->user->avatar?>" /></a>
            </div>
            <div class="feed-item-head-info">
                <a href="<?=$base?>/perfil/<?=$feedItem->user->id?>"><span class="fidi-name"><?=$feedItem->user->name?></span></a>
                <span class="fidi-action"><?php
                        switch ($feedItem->type) {
                            case 'text':
                                echo 'fez um post';
                            break;
                            case 'photo':
                                echo 'postou uma foto';
                            break;
                        }
                    ?></span>
                <br/>
                <span class="fidi-date"><?=date('d/m/Y', strtotime($feedItem->created_at));?></span>
            </div>
            <?php if ($feedItem->mine): ?>
                <div class="feed-item-head-btn">
                        <img src="<?=$base?>/assets/images/more.png" />
                    <div class="feed-item-more-window">
                        <a href="<?=$base?>/post/<?=$feedItem->id?>/apagar" onclick="return confirm('Deseja realmente apagar essa postagem?')">Excluir Post</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="feed-item-body mt-10 m-width-20">
            <?php
                switch ($feedItem->type) {
                    case 'text':
                        echo nl2br($feedItem->body);
                    break;
                    case 'photo':
                        echo '<img src="'.$base.'/media/uploads/'.$feedItem->body.'">';
                    break;
                }
            ?>
        </div>
        <div class="feed-item-buttons row mt-20 m-width-20">
            <div class="like-btn <?=($feedItem->liked ? 'on' : '');?>"><?=$feedItem->likeCount?></div>
            <div class="msg-btn"><?=count($feedItem->comments)?></div>
        </div>
        <div class="feed-item-comments">
            <div class="feed-item-comments-area">
                <?php foreach ($feedItem->comments as $item): ?>
                    <div class="fic-item row m-height-10 m-width-20">
                        <div class="fic-item-photo">
                            <a href="<?=$base?>/perfil/<?=$item['user']['id']?>"><img src="<?=$base?>/media/avatars/<?=$item['user']['avatar']?>" /></a>
                        </div>
                        <div class="fic-item-info">
                            <a href="<?=$base?>/perfil/<?=$item['user']['id']?>"><?=$item['user']['name']?></a>
                            <?=$item['body']?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="fic-answer row m-height-10 m-width-20">
                <div class="fic-item-photo">
                    <a href="<?=$base?>/perfil/<?=$loggedUser->id?>"><img src="<?=$base?>/media/avatars/<?=$loggedUser->avatar?>" /></a>
                </div>
                <input type="text" class="fic-item-field" placeholder="Escreva um comentário" />
            </div>

        </div>
    </div>
</div>