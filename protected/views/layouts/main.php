<!DOCTYPE html>
<?php
/**
 * Главный шаблон сайта
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="ru" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<?php Yii::app()->bootstrap->registerCoreCss(); ?>
	<?php Yii::app()->bootstrap->registerYiiCss(); ?>
	<?php Yii::app()->bootstrap->registerCoreScripts(); ?>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->urlManager->baseUrl; ?>/css/styles.css" />
</head>
<body>
<?php $this->widget('bootstrap.widgets.TbNavbar',array(
	'brand'=>false,
    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'items'=>Usermenu::getMenu()
        ),

		array(
            'class'=>'bootstrap.widgets.TbMenu',
			'htmlOptions'=>array('class'=>'pull-right'),
			'encodeLabel' => false,
            'items'=>array(
                array('label'=>'Войти', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest,
					'items' => array(
						array('label' => '<p><form method="post" action="'.Yii::app()->createUrl('/site/login').'" accept-charset="UTF-8">
							<input style="margin-bottom: 15px;" type="text" placeholder="Логин" id="LoginForm_username" name="LoginForm[username]">
							<input style="margin-bottom: 15px;" type="password" placeholder="Пароль" id="LoginForm_password" name="LoginForm[password]">
							<input type="hidden" value="'.Yii::app()->request->csrfToken.'" name="'.Yii::app()->request->csrfTokenName.'" />
							<input class="btn btn-primary btn-block" name="yt0" type="submit" value="Войти">
						</form></p>', )
					)),
                array('label'=>Yii::app()->user->name, 'url'=>'#', 'visible'=>!Yii::app()->user->isGuest,
					'items' => array(
						array('label'=>'Админцентр', 'url'=>array('/admin'), 'visible'=>!Yii::app()->user->isGuest),
						'---',
						array('label'=>'Выйти', 'url'=>array('/site/logout'), 'icon'=>'icon-off'),
					))
            ),
        ),
    ),
)); ?>
<div id="wrap">
	<div class="container" id="page">
		<?php if(isset($this->breadcrumbs)):?>
			<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
				'links'=>$this->breadcrumbs,
			)); ?>
		<?php endif?>

		<?php echo $content; ?>

		<div class="clear"></div>
		<div id="push"></div>
	</div>
</div>
<div id="footer">
	<div class="container">
		<p class="muted credit">
			&copy; <?php echo date('Y'); ?> 
			<?php echo CHtml::link('Craft-Soft Studio', 'http://craft-soft.ru', array('target' => '_blank'));  ?>
			<br />
			All Rights Reserved.
			<br />
			<br />
		</p>
	</div>
</div>
<div id="loading">
	<h1>Загрузка</h1>
	<div class="circle"></div>
	<div class="circle1"></div>
</div>
</body>
</html>
