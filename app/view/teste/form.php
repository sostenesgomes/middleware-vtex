<div><h1>Teste de <?php echo Template::getObjeto('tituloTeste') ?></h1></div>

<select id="selectTeste" name="selectTeste">
    <option value="<?php echo Template::getObjeto('formActionBase') ?>">Selecione um método</option>
    <?php
    $options = Template::getObjeto('optionsTeste');

    foreach($options as $key => $value){
        echo '<option value="'. $value .'">'.$key.'</option>';
    }
    ?>
    ?>
</select>

<br><br>

<div id="url">URL: <?php echo Template::getObjeto('formActionBase') ?></div>
<br>
<div>Case: <a id="case" target="_blank"></a></div>

<br><br>

<div>
    <form action="<?php echo Template::getObjeto('formActionBase') ?>" id="formTeste" name="formTeste" method="post">
        <label for="json-textarea">Digite ou cole um string do tipo JSON</label>
        <textarea id="json-textarea" name="json"></textarea>
        <input type="submit" value="Enviar">
    </form>
</div>