<!DOCTYPE html>
<html>
<head>
<?php require 'html_head.php' ?>
<style>
    #content > div{
        white-space: nowrap; 
        top: 40px; 
        left: 40px; 
        position: relative; 
        text-align: center;
        height:100%;
    }

    #content > div > img{
        position: relative; 
        left: -20px; 
        vertical-align:middle; 
        margin-right: 30px;
    }

    #content > div > div{
        text-align:left; 
        display:inline-block;
    }

</style>
</head>
<body>
<?php 
echo $this->data['menu'];
require 'msgbox.php' 
?>
        <div id="main">
            <div class="banner">
                <a href="/">
                    <img src="/img/logo.png" />
                </a>
            </div>
            <div id="content">
                <div>
                    <img src="/img/astronaut.png" />
                    <div>
                    <h1>Упс!</h1>
                    <?php echo $this->data['error'] ?>
                        </div>
                </div>
            </div>
            <?php include('footer.php'); ?>
        </div>
</body>
</html>