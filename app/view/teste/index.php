<div>
    <?php
        $links = Template::getObjeto('links');

        foreach($links as $link){
            ?>
            <div>Link para Teste de <?php echo $link['titulo']?>: <a href="<?php echo $link['url'] ?>"><?php echo $link['url']?></a></div><br>
            <?php
        }
    ?>
</div>