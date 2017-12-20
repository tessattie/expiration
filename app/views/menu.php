<nav class="navbar navbar-default" id = "columnid">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/orders/public/home"><p><img src="/orders/public/images/logo.png" class="logo"></p></a>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle navrightmenu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= ucfirst($_SESSION['orders']['firstname']); ?> <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/orders/public/home">Home</a></li>
            <li><a href="/orders/public/account">Settings</a></li>
            <?php 
              if($_SESSION['orders']['role'] == 7)
              {
                echo '<li><a href="/orders/public/logs">Logs</a></li>';
              }
            ?>
            <li><a href="/orders/public/home/logout">Log out</a></li>
          </ul>
        </li>
      </ul>
    <ul class="nav navbar-nav navbar-left">
    <?php  
      if($_SESSION['orders']['role'] == 5 || $_SESSION['orders']['role'] == 6 || $_SESSION['orders']['role'] == 7 || $_SESSION['orders']['role'] == 8)
      {
        echo '<li><a class="menuitems" href="/orders/public/home">New order</a></li>';
      }
      if($_SESSION['orders']['role'] == 7 || $_SESSION['orders']['role'] == 6 || $_SESSION['orders']['role'] == 8)
      {
        echo '<li><a class="menuitems" href="/orders/public/reports">Order list</a></li>';
      }
      if($_SESSION['orders']['role'] == 7 || $_SESSION['orders']['role'] == 6)
      {
        echo '<li><a class="menuitems" href="/orders/public/reports/batch">Batch order</a></li>';
      }
    ?>
    </ul>
      
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<div class="container-fluid">
  <div class="row salesrow">
    <div class = "col-md-7 fildiv">
      <?php 
      if(!empty($data['title']))
      {
        echo '<p class="filArianne"><span class="csm"><a href="/orders/public/home">REPORTS</a></span><span class="glyphicon glyphicon-chevron-right"></span><span class="tablecaption">'.$data['title'].'</span>';
      }
      ?>
    </div>
  </div>