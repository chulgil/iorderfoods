<?php

##DelhiPizza##start
Router::connect('/DelhiPizza', array('controller' => 'users', 'action' => 'store'));
Router::connect('/DelhiPizza/admin', array('controller' => 'stores', 'action' => 'store'));
##DelhiPizza##end

##DunPizza##start
Router::connect('/DunPizza', array('controller' => 'users', 'action' => 'store')); 
Router::connect('/DunPizza/admin', array('controller' => 'stores', 'action' => 'store'));
##DunPizza##end

?>
