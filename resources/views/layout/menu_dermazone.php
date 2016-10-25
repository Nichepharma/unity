<?php
$module = '';
if (isset($data['modules']) && !empty($data['modules']))
    $module = $data['modules'];

if(!isset($_GET['datefrom'])){
  $_GET['datefrom']="";
  $_GET['dateto']="";
}
?>
<nav class="mainmenubg" role="navigation">
    <div class="mainmenu">
        <div class="menu_mob_btn"></div>
        <ul class="menu" id="menu">
            <li><a href="http://tacitapp.com/dermazone/"><span class="glyphicon glyphicon-home"></span> Home</a></li>
            <li><a href="http://tacitapp.com/unity/support/3"><span class="glyphicon glyphicon-comment"></span> Customer Service</a></li>
        </ul>

        <script type="text/javascript">
            var dropdown = new TINY.dropdown.init("dropdown", {id: 'menu', active: 'menuhover'});
        </script>
    </div>
</nav>
