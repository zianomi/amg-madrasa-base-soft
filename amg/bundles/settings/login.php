<?php


$errors = array();
if(isset($_POST['_chk'])==1){

    $set = new SettingModel();
    $userName = $set->filter($_POST['username']);
    $password = $set->filter($_POST['password']);


    if(empty($userName) && !empty($password)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("please_insert_username"));
    }

    if(empty($password) && !empty($userName)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("please_insert_password"));
    }

    if(empty($userName) && empty($password)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("please_insert_username_and_password"));
    }

    if(!empty($userName) && !empty($password)){

        $password = md5($password);
        $check_user = array(
            'username' => $userName,
            'password' => $password
          );
        $exists = $set->exists($set->getPrefix() . 'users', 'id', $check_user );

        if($exists){
            $row = $set->checkLogin($userName,$password);
            $session = $set->getCurrentSession();
            $curSessionId = $session['id'];
            $id = $row['id'];
            $group = $row['group_id'];
            $set->insertLoginModules($id);
            $set->insertLoginUserBrnaches(array("user" => $id, "group" => $group));
            $_SESSION['UserId'] = $id;
            $_SESSION['UserName'] = $row['name'];
            $_SESSION['userType'] = $row['user_type'];
            //$_SESSION['UserBranchId'] = $row['branch_id'];
            $_SESSION['SessionId'] = $curSessionId;
            $tpl->removeMenuCache();
            $tool->Redir("","","","");
        }
        else{
            $errors[] = $tool->Message("alert",$tool->transnoecho("invalid_username_or_passowrd"));
        }

    }

}

echo $tpl->header();

?>


<div class="container">

    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />

      <form class="form-login" method="post" action="">
          <input type="hidden" name="_chk" value="1">
          <?php echo $tpl->formHidden(); ?>

          <?php
          if(count($errors)>0){
              echo implode("<br />",$errors);
          }
          ?>
        <h2 class="form-heading"><img src="<?php echo WEB ?>/img/logo_report.png" /></h2>
        <input type="text" style="direction: ltr; text-align: left;" name="username" value="<?php if(isset($_POST['username'])) echo $_POST['username']; ?>" class="input-block-level" placeholder="User Name">
        <input type="password" style="direction: ltr; text-align: left;" name="password" class="input-block-level" placeholder="Password">

        <div class="row-fluid">
          <button class="btn btn-primary pull-right span12" type="submit">Log in</button>
        </div>


      </form>

    </div>


<!--wraper div-->
</div>

<style>body{opacity: 1}.wraper {display: block;}#amgloader {display: none;}</style>;
