<?php
/**
 * Шаблон 2 колонки
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */

?>
<?php $this->beginContent('//layouts/main'); ?>
<div class="row">
    <div class="span9">
        <div id="content">
            <?php echo $content; ?>
        </div>
    </div>
    <div class="span3">
        <div id="sidebar">
        <?php
            $this->beginWidget('zii.widgets.CPortlet', array(
                'title'=>'Меню',
            ));
            $this->widget('bootstrap.widgets.TbMenu', array(
                'items'=>$this->menu,
                'htmlOptions'=>array('class'=>'operations'),
            ));
            $this->endWidget();
        ?>
        </div><!-- sidebar -->
    </div>
</div>
<?php $this->endContent(); ?>