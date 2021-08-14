<?php
$php_pass="";
$php_name="";
$php_text="";
$e_number="";
$password1="";
$password2="";
$password3="";
//データベース接続
$dsn="データベース名";
$user="ユーザー名";
$password="パスワード";
$pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));

//テーブルの作成
$sql="CREATE TABLE IF NOT EXISTS m5_2"//テーブル名に-は使えない
."("
."id INT AUTO_INCREMENT PRIMARY KEY,"
."name char(32),"
."comment TEXT,"
."date DATETIME,"
."password TEXT"
.");";
$stmt=$pdo->query($sql);

//送信を押した場合
if(isset($_POST["submit1"])){
    $name=trim($_POST["name"]);
    $comment=trim($_POST["comment"]);
    $date=date("Y/m/d H:i:s");
    $password1=$_POST["password1"];
    $e_check=$_POST["edit_check"];
    //新規投稿で名前とコメントが書かれている場合の場合
    if($e_check==NULL and $name!=NULL and $comment!=NULL){
        //テーブルにデータを挿入する。
        $sql = $pdo -> prepare("INSERT INTO m5_2 (name, comment,date,password) VALUES (:name, :comment,:date,:password)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':password',$password1, PDO::PARAM_STR);
        $sql -> execute();
    //編集する場合    
    }elseif($e_check!=NULL and $name!=NULL and $comment!=NULL){
        //テーブルのデータを変更
        $id = $e_check; //変更する投稿番号
        $sql = 'UPDATE m5_2 SET name=:name,comment=:comment,date=:date WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':date',$date, PDO::PARAM_STR);
        $stmt->execute();
    }
}
//削除を押した場合
if(isset($_POST["submit2"])){
    $d_number=$_POST["d_number"];
    $password2=$_POST["password2"];
    //テーブルからデータを取得
    $sql = 'SELECT * FROM m5_2';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //パスワードと削除番号が合っている場合
        if($d_number==$row['id'] and $password2==$row['password']){
            //データを削除する
            $id=$d_number;
            $sql="delete from m5_2 where id=:id";
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(":id",$id,PDO::PARAM_INT);
            $stmt->execute();
        }
    }
}

//編集を押した場合
if(isset($_POST["submit3"])){
    $e_number=$_POST["e_number"];
    $password3=$_POST["password3"];

    //テーブルからデータを取得
    $sql = 'SELECT * FROM m5_2';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //パスワードと編集番号が合っている場合
        if($e_number==$row['id'] and $password3==$row['password']){
            $php_name=$row['name'];
            $php_text=$row['comment'];
            $php_pass=$row['password'];
        }
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>m5-1</title>
</head>    
<body>
<h1 style="text-align:center;background-color:#6699FF;padding:20px 0px 0px 0px;">掲示板
<hr></h1>
<p>この掲示板は、好きなアニメや漫画について語り合う掲示板です。</p>
<hr>

<form method="post" action="">
    <p>パスワード：<input type="password" name="password1" value="<?= $php_pass?>"></p>
    <p>名前：<input type="text" name="name" value="<?= $php_name ?>"></p>
    <p>コメント：<input type="text" name="comment" value="<?= $php_text ?>"></p>
    <input type="hidden" name="edit_check" value=<?= $e_number?>>
    <?php
    //空欄で提出された場合の処理
    if(isset($_POST["submit1"])){
        if($password1==NULL){
            echo "パスワードが未入力です。<br>";
        }elseif($name==NULL){
            echo "名前が未入力です。<br>";
        }elseif($comment==NULL){
            echo "コメントが未入力です。<br>";
        }
    }    
    ?>
    <input type="submit" name="submit1"><br>
</form>

<form method="post" action="">
    <p>パスワード：<input type="password" name="password2"></p>
    <p>削除番号：<input type="int" name="d_number"></p>
    <?php
    //空欄で提出された場合の処理
    if(isset($_POST["submit2"])){
        if($password2==NULL){
            echo "パスワードが未入力です。<br>";
        }elseif($d_number==NULL){
            echo "削除番号が未入力です。<br>";
        }
    }
    ?>
    <input type="submit" name="submit2" value="削除"><br>
</form>

<form method="post" action="">
    <p>パスワード：<input type="password" name="password3"></p>
    <p>編集番号：<input type="int" name="e_number"></p>
    <?php
    //空欄で提出された場合の処理
    if(isset($_POST["submit3"])){
        if($password3==NULL){
            echo "パスワードが未入力です。<br>";
        }elseif($e_number==NULL){
            echo "編集番号が未入力です。<br>";
        }
    }
    ?>
    <input type="submit" name="submit3" value="編集"><br>
</form>
<hr>
<?php
//m5_2を表示させる
$sql = 'SELECT * FROM m5_2';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
    echo $row['id'].' ';
    echo $row['name'].' ';
    echo $row['comment'].' ';
    echo $row['date'].'<br>';
    echo '<br>';
}
?>
</body>
</html>