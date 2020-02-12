<?php 
    error_reporting(0);
    require "db.php";
    $errors = array();
    $data = $_POST;
    if(isset($data['log_in'])){
        if($data['login'] == "admin"){
            if($data['pass'] == "admin"){
                $_SESSION['logged'] = true;
            } else {
                echo "Incorrect password";
            }
        } else {
            echo "Incorrect login";
        }
    }
    if(isset($data['do_activity'])){
        if(empty(trim($data['name']))){
            $errors[] = "Пустое поле название";
        }
        if(empty(trim($data['desc']))){
            $errors[] = "Пустое поле описания";
        }
        if(empty(trim($data['date']))){
            $errors[] = "Пустое поле даты";
        }
        if(empty($errors)){
            $new_activity = R::dispense("activities");
            $new_activity->name = $data['name'];
            $new_activity->desc = $data['desc'];
            $new_activity->date = $data['date'];
            if(!empty($data['site'])){
                $new_activity->site = $data['site'];
            }
            $new_activity->completed = false;
            R::store($new_activity);
            echo "<div style = \"color:green; font-size:16px;\">Completed successfully</div>";
        } else {
            echo "<div style = \"color:red; font-size:16px;\">".array_shift($errors)."</div>";
        }
    }
    if(isset($data['cancel_act'])){
        $activity = R::findOne("activities","id = ?",array($data['act_id']));
        $activity->completed = true;
        R::store($activity);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Panel | Student Council</title>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css" />
    <script src="./bootstrap/js/bootstrap.min.js"></script>
    <style>
        body{
            margin-left: 15px;
        }
    </style>
</head>
<body>
    <?php if (!$_SESSION['logged']){ ?>
    <div>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
            <input type="text" name="login" placeholder="login"><br><br>
            <input type="password" name="pass" id="pass" placeholder="pass"><br><br>
            <button type="submit" name="log_in">Войти</button>
        </form>
    </div>
    <?php } ?>
    <?php if($_SESSION['logged']){ ?>
        <div class="container row">
        <div class="col-md-4 col-sm-12">
            <h1>New activity</h1>
            <div>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                    <input type="text" name="name" placeholder="Название"><br><br>
                    <textarea name="desc" id="desc" cols="30" rows="10" placeholder="Описание"></textarea><br><br>
                    <input type="text" name="date" placeholder="Дата"><br><br>
                    <input style="width:320px" type="text" name="site" placeholder="Сайт в формате: http://rating.novel.systems"><br><br>
                    <button type="submit" name="do_activity">Добавить</button>
                </form>
            </div>
            <br><br>
            <a href="logout.php">Выйти</a>
        </div>
        <div class="col-md-8 col-sm-12">
      <?php
        $activities = R::find("activities","completed = ?",array(false));
        if($activities){
      ?>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Дата</th>
              <th scope="col">Название</th>
              <th scope="col">Описание</th>
              <th scope="col">Дополнительно</th>
              <th scope="col">Завершить</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($activities as $activity){ ?>
            <tr>
              <th scope="row"><?php echo $activity->date; ?></th>
              <td><?php echo $activity->name; ?></td>
              <td><?php echo $activity->desc; ?></td>
              <td>
                <?php if($activity->site){ ?>
                <a href="<?php echo $activity->site ?>"><?php echo $activity->site ?></a>
                <?php } ?>
              </td>
              <td>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                    <input type="hidden" name="act_id" value="<?php echo $activity->id; ?>">
                    <button class="btn btn-danger" type="submit" name="cancel_act">Закончить</button>
                </form>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php 
        }
      ?>
      </div>
    </div>
    </div>
    <div class="container">
        <div class="col-12">
        <h1 class="text-center">History</h1>
        <br>
        <?php
            $activities = R::findAll("activities");
            if($activities){
        ?>
        <div class="table-responsive">
            <table class="table table-striped">
            <thead>
                <tr>
                <th scope="col">Дата</th>
                <th scope="col">Название</th>
                <th scope="col">Описание</th>
                <th scope="col">Дополнительно</th>
                <th scope="col">Завершить</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($activities as $activity){ ?>
                <tr>
                <th scope="row"><?php echo $activity->date; ?></th>
                <td><?php echo $activity->name; ?></td>
                <td><?php echo $activity->desc; ?></td>
                <td>
                    <?php if($activity->site){ ?>
                    <a href="<?php echo $activity->site ?>"><?php echo $activity->site ?></a>
                    <?php } ?>
                </td>
                </tr>
                <?php } ?>
            </tbody>
            </table>
            <?php 
                }
            ?>
        </div>
    </div>
    <?php } ?>
</body>
</html>